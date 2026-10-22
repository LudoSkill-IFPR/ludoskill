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

        if (empty($email)) {
            $data['erro'] = 'Por favor, insira um endereço de e-mail.';
            $this->view('entrada/esqueciSenha', $data);
            return;
        }

        $usuario = $this->usuarioService->getUsuarioByEmail($email);

        if (!$usuario) {
            $data['erro'] = 'Não encontramos um usuário com esse e-mail.';
            $this->view('entrada/esqueciSenha', $data);
            return;
        }

        // Aqui você pode implementar a lógica para enviar um e-mail de recuperação de senha.
        // Por exemplo, gerar um token, salvar no banco e enviar o link para o usuário
        $hash = md5(date('d/m/Y H:i:s') . $usuario->getEmail());

        $mensagem = 'Clique no link para redefinir sua senha: <a href="' . URL_BASE . '/gerar-nova-senha?hash=' . $hash . '">Redefinir Senha</a>';

        $this->sendMailHelper->sendMail($email, 'Recuperação de Senha', $mensagem);


        $_SESSION[$hash]['email'] = $usuario->getEmail();
        $_SESSION[$hash]['hash'] = $hash;


        


        $data['sucesso'] = 'Um e-mail de recuperação de senha foi enviado para ' . htmlspecialchars($email) . '.';
        $this->view('entrada/esqueciSenha', $data);


    }

    public function gerarNovaSenha(){
        
        $hash = $_GET['hash'] ?? '';

        if (empty($hash)) {
            $data['erro'] = 'Link inválido ou expirado.';
            $this->view('entrada/gerarNovaSenha', $data);
            return;
        }

        if (!isset($_SESSION[$hash])) {
            $data['erro'] = 'Link inválido ou expirado.';
            $this->view('entrada/gerarNovaSenha', $data);
            return;
        }

        if ($_SESSION[$hash]['hash'] !== $hash) {
            $data['erro'] = 'Link inválido ou expirado.';
            $this->view('entrada/gerarNovaSenha', $data);
            return;
        }

        $this->view('entrada/gerarNovaSenha', ['email' => $_SESSION[$hash]['email']]);


    }

    public function redefinirSenha()
    {
        $email = $_POST['email'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';


        if ($novaSenha !== $confirmarSenha) {
            $data['erro'] = 'As senhas não coincidem.';
            $this->view('entrada/gerarNovaSenha', ['email' => $_SESSION[$hash]['email'], 'erro' => $data['erro']]);
            return;
        }

        if (strlen($novaSenha) < 8) {
            $data['erro'] = 'A senha deve ter pelo menos 8 caracteres.';
            $this->view('entrada/gerarNovaSenha', ['email' => $_SESSION[$hash]['email'], 'erro' => $data['erro']]);
            return;
        }



        // Atualiza a senha do usuário
        $this->autenticacaoService->redefinirSenhaByEmail($email, password_hash($novaSenha, PASSWORD_DEFAULT));


        // Remove o hash da sessão após a redefinição da senha
        unset($_SESSION[$hash]);

        $_SESSION['mensagem_sucesso'] = 'Sua senha foi redefinida com sucesso!';
        $this->redirect(URL_BASE . '/login');
    }
}
