<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Funcionario;
use app\services\FuncionarioService;
use app\helpers\Validador;
use DateTimeImmutable;
use app\models\Empresa;

class FuncionarioController extends Controller
{
    private FuncionarioService $funcionarioService;

    public function inicial()
    {
        $this->autenticacaoRequired();

        $this->view('funcionario/inicial');
    }

    public function __construct()
    {
        $this->funcionarioService = new FuncionarioService();
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
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados);
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
            $_POST['nome_completo'],
            new DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha_hash'],
            $_POST['numero_telefone'],
            $empresa,
            0,
            0,
            1
        );

        $this->funcionarioService->saveFuncionario($funcionario);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function editar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $data['funcionario'] = $this->funcionarioService->getFuncionarioByIdAndEmpresa($id, $idEmpresa);
        if (!$data['funcionario']) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $this->view('gestor/funcionarios/funcionario_edit', $data);
    }

    public function excluir()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $this->funcionarioService->deleteFuncionario($id, $idEmpresa);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function atualizar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $idFuncionario = filter_input(INPUT_POST, 'id_funcionario', FILTER_VALIDATE_INT);
        if (!$idFuncionario || !$this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa)) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $dados = $_POST;
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados, false);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $dados;
            $this->view('gestor/funcionarios/funcionario_edit', $data);
            return;
        }

        $empresa = (new Empresa())->setId($idEmpresa);

        $funcionario = new Funcionario(
            $idFuncionario,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha_hash'] ?? '',
            $_POST['numero_telefone'],
            $empresa,
            (int) $_POST['bolotas_totais'],
            (int) $_POST['pontuacao_total'],
            (int) $_POST['nivel']
        );

        $this->funcionarioService->updateFuncionario($funcionario, $idEmpresa, $_POST['senha_hash'] ?? null);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
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
}
