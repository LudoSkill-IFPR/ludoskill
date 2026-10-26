<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AutenticacaoService;
use app\services\UsuarioService;
use app\helpers\SendMailHelper;

class AutenticacaoController extends Controller
{

    private AutenticacaoService $autenticacaoService;
    private UsuarioService $usuarioService;
    private SendMailHelper $sendMailHelper;

    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
        $this->usuarioService = new UsuarioService();
        $this->sendMailHelper = new SendMailHelper();
    }

    public function login()
    {

        $this->view('autenticacao/login');
    }

    public function logar()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->autenticacaoService->logar($email, $senha);

        if (!$usuario) {
            $data['erro'] = $this->autenticacaoService->getUltimoErro() ?? 'E-mail ou senha incorretos.';
            $data['email'] = $email;
            $this->view('entrada/login', $data);
            return;
        }

        if ($usuario->getPerfil() === null) {
            session_destroy();

            $this->redirect(URL_BASE . '/cadastro-nao-liberado');
        }

        switch ($usuario->getPerfil()) {

            case 'admin':
                $this->redirect(URL_BASE . '/administrador/inicial');
                break;

            case 'gestor':
                $this->redirect(URL_BASE . '/gestor/inicial');
                break;

            case 'funcionario':
                $this->redirect(URL_BASE . '/funcionario/inicial');
                break;

            default:
                session_destroy();

                $this->redirect(URL_BASE . '/cadastro-nao-liberado');
        }
    }

    public function cadastroNaoLiberado()
    {
        $this->view('entrada/cadastroNaoAutorizado');
    }

    public function logout()
    {
        $this->autenticacaoService->logout();
        $this->redirect(URL_BASE . '/');
    }

    public function esqueciSenha()
    {
        $this->view('entrada/esqueciSenha');
    }

    public function recuperarSenha()
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('entrada/esqueciSenha', [
                'erro' => 'Informe um endereço de e-mail válido.',
                'email' => $email,
            ]);
            return;
        }

        $usuario = $this->usuarioService->getUsuarioByEmail($email);
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['recuperacoes_senha'][$token] = [
                'id_usuario' => $usuario->getId(),
                'email' => $usuario->getEmail(),
                'expira_em' => time() + 1800,
            ];

            $link = APP_URL . '/gerar-nova-senha?token=' . rawurlencode($token);
            $nome = htmlspecialchars($usuario->getNomeCompleto(), ENT_QUOTES, 'UTF-8');
            $linkSeguro = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
            $mensagem = "<h2>Recuperação de senha</h2>"
                . "<p>Olá, {$nome}.</p>"
                . '<p>Recebemos uma solicitação para redefinir sua senha no LudoSkill.</p>'
                . "<p><a href=\"{$linkSeguro}\">Criar uma nova senha</a></p>"
                . '<p>O link expira em 30 minutos. Se você não fez essa solicitação, ignore este e-mail.</p>';

            if (!$this->sendMailHelper->sendMail($usuario->getEmail(), 'Recuperação de senha - LudoSkill', $mensagem)) {
                unset($_SESSION['recuperacoes_senha'][$token]);
                $this->view('entrada/esqueciSenha', [
                    'erro' => 'Não foi possível enviar o e-mail agora. Tente novamente mais tarde.',
                    'email' => $email,
                ]);
                return;
            }
        }

        $this->view('entrada/esqueciSenha', [
            'sucesso' => 'Se o e-mail estiver cadastrado, você receberá um link de recuperação válido por 30 minutos.',
        ]);
    }

    public function gerarNovaSenha()
    {
        $token = (string) ($_GET['token'] ?? '');
        $recuperacao = $this->recuperacaoValida($token);

        if (!$recuperacao) {
            $this->view('entrada/gerarNovaSenha', ['erro' => 'Este link é inválido ou expirou.']);
            return;
        }

        $this->view('entrada/gerarNovaSenha', [
            'token' => $token,
            'email' => $recuperacao['email'],
        ]);
    }

    public function redefinirSenha()
    {
        $token = (string) ($_POST['token'] ?? '');
        $novaSenha = (string) ($_POST['nova_senha'] ?? '');
        $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');
        $recuperacao = $this->recuperacaoValida($token);

        if (!$recuperacao) {
            $this->view('entrada/gerarNovaSenha', ['erro' => 'Este link é inválido ou expirou.']);
            return;
        }

        if ($novaSenha !== $confirmarSenha) {
            $this->view('entrada/gerarNovaSenha', [
                'token' => $token,
                'email' => $recuperacao['email'],
                'erro' => 'As senhas não coincidem.',
            ]);
            return;
        }

        if (strlen($novaSenha) < 8) {
            $this->view('entrada/gerarNovaSenha', [
                'token' => $token,
                'email' => $recuperacao['email'],
                'erro' => 'A senha deve ter pelo menos 8 caracteres.',
            ]);
            return;
        }

        if (!$this->autenticacaoService->redefinirSenha((int) $recuperacao['id_usuario'], $novaSenha)) {
            $this->view('entrada/gerarNovaSenha', [
                'token' => $token,
                'email' => $recuperacao['email'],
                'erro' => 'Não foi possível redefinir a senha. Tente novamente.',
            ]);
            return;
        }

        unset($_SESSION['recuperacoes_senha'][$token]);
        $_SESSION['mensagem_sucesso'] = 'Sua senha foi redefinida com sucesso!';
        $this->redirect(URL_BASE . '/login');
    }

    private function recuperacaoValida(string $token): ?array
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $recuperacao = $_SESSION['recuperacoes_senha'][$token] ?? null;
        if (!is_array($recuperacao) || (int) ($recuperacao['expira_em'] ?? 0) < time()) {
            unset($_SESSION['recuperacoes_senha'][$token]);
            return null;
        }

        return $recuperacao;
    }
}
