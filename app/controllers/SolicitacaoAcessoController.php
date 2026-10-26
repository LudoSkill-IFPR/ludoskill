<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\SendMailHelper;
use app\helpers\Validador;
use app\models\SolicitacaoAcesso;
use app\services\SolicitacaoAcessoService;

class SolicitacaoAcessoController extends Controller
{
    private SolicitacaoAcessoService $service;
    private SendMailHelper $mail;

    public function __construct()
    {
        $this->service = new SolicitacaoAcessoService();
        $this->mail = new SendMailHelper();
    }

    public function criar(): void
    {
        $this->view('entrada/solicitarAcesso');
    }

    public function salvar(): void
    {
        $dados = $_POST;
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf'] ?? '');
        $dados['empresa_cnpj'] = preg_replace('/\D/', '', $dados['empresa_cnpj'] ?? '');
        $dados['email'] = strtolower(trim((string) ($dados['email'] ?? '')));
        $dados['empresa_email'] = strtolower(trim((string) ($dados['empresa_email'] ?? '')));
        $erros = Validador::validarSolicitacaoAcesso($dados);

        if (empty($erros) && $this->service->existeEmAberto($dados['email'], $dados['cpf'])) {
            $erros['email'] = 'Já existe uma solicitação em análise ou um acesso concedido para estes dados.';
        }

        if (!empty($erros)) {
            $this->view('entrada/solicitarAcesso', ['erros' => $erros, 'solicitacao' => $dados]);
            return;
        }

        $solicitacao = new SolicitacaoAcesso(
            0,
            trim($dados['nome_completo']),
            new \DateTimeImmutable($dados['data_nascimento']),
            $dados['cpf'],
            $dados['email'],
            trim($dados['numero_telefone']),
            trim($dados['empresa_nome']),
            $dados['empresa_cnpj'],
            $dados['empresa_email'],
            trim((string) ($dados['mensagem'] ?? ''))
        );

        $this->service->salvar($solicitacao);
        $this->view('entrada/solicitarAcesso', [
            'sucesso' => 'Solicitação enviada. Um administrador analisará os dados e, se aprovada, você receberá o acesso por e-mail.',
        ]);
    }

    public function listar(): void
    {
        $this->adminRequired();
        $mensagem = $_SESSION['mensagem_solicitacao'] ?? null;
        $tipoMensagem = $_SESSION['tipo_mensagem_solicitacao'] ?? 'sucesso';
        unset($_SESSION['mensagem_solicitacao'], $_SESSION['tipo_mensagem_solicitacao']);

        $this->view('administrador/solicitacoes/solicitacao_list', [
            'lista' => $this->service->getTodas(),
            'mensagem' => $mensagem,
            'tipoMensagem' => $tipoMensagem,
        ]);
    }

    public function aprovar(): void
    {
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $senha = (string) ($_POST['senha'] ?? '');
        $plano = (string) ($_POST['plano'] ?? 'BASICO');

        if (!$id || strlen($senha) < 8 || !in_array($plano, ['BASICO', 'INTERMEDIARIO', 'AVANCADO', 'CORPORATIVO'], true)) {
            $this->definirMensagem('Informe uma senha de pelo menos 8 caracteres e um plano válido.', 'erro');
            $this->redirect(URL_BASE . '/administrador/solicitacoes-acesso');
        }

        try {
            $acesso = $this->service->aprovar($id, $senha, $plano);
            $nome = htmlspecialchars($acesso['nome'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($acesso['email'], ENT_QUOTES, 'UTF-8');
            $senhaEmail = htmlspecialchars($acesso['senha'], ENT_QUOTES, 'UTF-8');
            $login = htmlspecialchars(APP_URL . '/login', ENT_QUOTES, 'UTF-8');
            $mensagem = "<h2>Seu acesso ao LudoSkill foi aprovado</h2>"
                . "<p>Olá, {$nome}.</p>"
                . '<p>Seu cadastro de gestor foi aprovado. Use os dados abaixo para entrar:</p>'
                . "<p><strong>E-mail:</strong> {$email}<br><strong>Senha temporária:</strong> {$senhaEmail}</p>"
                . "<p><a href=\"{$login}\">Acessar o LudoSkill</a></p>"
                . '<p>Por segurança, altere a senha depois do primeiro acesso.</p>';

            if ($this->mail->sendMail($acesso['email'], 'Acesso aprovado - LudoSkill', $mensagem)) {
                $this->service->marcarEmailEnviado($id);
                $this->definirMensagem('Solicitação aprovada e dados de acesso enviados por e-mail.');
            } else {
                $this->definirMensagem('O gestor foi criado, mas o e-mail não pôde ser enviado. Confira a configuração SMTP.', 'erro');
            }
        } catch (\DomainException $e) {
            $this->definirMensagem($e->getMessage(), 'erro');
        } catch (\Throwable $e) {
            error_log($e->__toString());
            $this->definirMensagem('Não foi possível aprovar a solicitação.', 'erro');
        }

        $this->redirect(URL_BASE . '/administrador/solicitacoes-acesso');
    }

    public function recusar(): void
    {
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->service->recusar($id)) {
            $this->definirMensagem('A solicitação não existe ou já foi analisada.', 'erro');
        } else {
            $this->definirMensagem('Solicitação recusada.');
        }
        $this->redirect(URL_BASE . '/administrador/solicitacoes-acesso');
    }

    private function definirMensagem(string $mensagem, string $tipo = 'sucesso'): void
    {
        $_SESSION['mensagem_solicitacao'] = $mensagem;
        $_SESSION['tipo_mensagem_solicitacao'] = $tipo;
    }
}
