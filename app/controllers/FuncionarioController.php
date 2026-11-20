<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Funcionario;
use app\models\Empresa;
use app\services\FuncionarioService;
use app\helpers\Validador;
use app\helpers\SendMailHelper;
use DateTimeImmutable;
use app\services\InventarioService;
use DomainException;

class FuncionarioController extends Controller
{
    private FuncionarioService $funcionarioService;
    private SendMailHelper $Mail;

    public function inicial()
    {
        $this->funcionarioRequired();
        $idUsuario = $_SESSION['usuario_logado']->getId();
        $atividadeService = new \app\services\FuncionarioAtividadeService();
        $this->view('funcionario/inicial', [
            'funcionario' => $this->funcionarioService->getFuncionarioByUsuario($idUsuario),
            'inventario' => (new InventarioService())->getByUsuario($idUsuario),
            'resumo' => $atividadeService->getResumoByUsuario($idUsuario),
            'proximaAtividade' => $atividadeService->getProximaAtividade($idUsuario),
            'mensagem' => $_SESSION['mensagem'] ?? null,
            'erro' => $_SESSION['erro'] ?? null,
        ]);
        unset($_SESSION['mensagem'], $_SESSION['erro']);
    }

    public function __construct()
    {
        $this->funcionarioService = new FuncionarioService();
        $this->Mail = new SendMailHelper();
    }

    public function listarTodos()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $data['lista'] = $this->funcionarioService->getFuncionariosByEmpresa($idEmpresa);
        $this->view('gestor/funcionarios/funcionario_list', $data);
    }

    public function criar()
    {
        $this->gestorRequired();
        $this->getEmpresaIdDoGestor();
        $this->view('gestor/funcionarios/funcionario_create', []);

    }

    public function salvar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $dados = $_POST;
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf'] ?? '');
        $dados['numero_telefone'] = preg_replace('/\D/', '', $dados['numero_telefone'] ?? '');
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados);
        if (empty($erros['cpf']) && $this->funcionarioService->cpfExiste($dados['cpf'])) {
            $erros['cpf'] = 'Já existe um usuário com este CPF.';
        }
        if (empty($erros['email']) && $this->funcionarioService->emailExiste($dados['email'])) {
            $erros['email'] = 'Já existe um usuário com este e-mail.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $dados;
            $this->view('gestor/funcionarios/funcionario_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId($idEmpresa);

        $funcionario = new Funcionario(
            0,
            $dados['nome_completo'],
            new DateTimeImmutable($dados['data_nascimento']),
            $dados['cpf'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['numero_telefone'],
            $empresa,
            0,
            0,
            1
        );

        $this->funcionarioService->saveFuncionario($funcionario);

        $mensagem = "<h2>Seu acesso ao LudoSkill foi criado com sucesso!</h2>"
                . "<p>Olá, {$dados['nome_completo']}.</p>"
                . '<p>Seu cadastro de funcionário foi criado e aprovado. Use os dados abaixo para entrar:</p>'
                . "<p><strong>E-mail:</strong> {$dados['email']}<br><strong>Senha temporária:</strong> {$dados['senha_hash']}</p>"
                . "<p><a href=\"" . URL_BASE . "\">Acessar o LudoSkill</a></p>"
                . '<p>Por segurança, altere a senha depois do primeiro acesso.</p>';

        $this->Mail->sendMail($dados['email'], 'Acesso ao LudoSkill <NÃO RESPONDER>', $mensagem);

        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function desativar()
    {
        $this->gestorRequired();

        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $this->funcionarioService->desativarFuncionario($id, $idEmpresa);

        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function ativar()
    {
        $this->gestorRequired();

        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $this->funcionarioService->ativarFuncionario($id, $idEmpresa);

        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function excluir()
    {
        $this->desativar();
    }

    private function getEmpresaIdDoGestor(): int
    {
        $idEmpresa = $this->funcionarioService->getEmpresaIdByGestorUsuario(
            $_SESSION['usuario_logado']->getId()
        );

        if ($idEmpresa === null) {
            $this->redirect(URL_BASE . '/gestor/inicial');
        }

        return $idEmpresa;
    }

    public function equipaItem()
    {
        $this->funcionarioRequired();
        $idItem = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);
        if (!$idItem) $this->redirect(URL_BASE . '/funcionario/inicial');
        try {
            $tipo = (new InventarioService())->equipar($_SESSION['usuario_logado']->getId(), $idItem);
            if ($tipo === 'TEMA') $_SESSION['tema_equipado'] = $idItem;
            $_SESSION['mensagem'] = 'Item equipado com sucesso.';
        } catch (\Throwable $exception) {
            error_log($exception->__toString());
            $_SESSION['erro'] = $exception instanceof DomainException ? $exception->getMessage() : 'Não foi possível equipar o item.';
        }
        $this->redirect(URL_BASE . '/funcionario/inicial');
    }

    public function desequipaTema()
    {
        $this->funcionarioRequired();
        $idItem = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);
        if (!$idItem) $this->redirect(URL_BASE . '/funcionario/inicial');

        try {
            (new InventarioService())->desequiparTema($_SESSION['usuario_logado']->getId(), $idItem);
            unset($_SESSION['tema_equipado']);
            $_SESSION['mensagem'] = 'Tema desequipado com sucesso.';
        } catch (\Throwable $exception) {
            error_log($exception->__toString());
            $_SESSION['erro'] = $exception instanceof DomainException
                ? $exception->getMessage()
                : 'Não foi possível desequipar o tema.';
        }

        $this->redirect(URL_BASE . '/funcionario/inicial');
    }
}
