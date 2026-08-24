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
        $data['lista'] = $this->funcionarioService->getFuncionarios();
        $this->view('administrador/funcionario/funcionario_list', $data);
    }

    public function listarFuncionario()
    {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/funcionarios');
        }

        $id = $_GET['id'];
        $data['funcionario'] = $this->funcionarioService->getFuncionarioById($id);
        $this->view('sistema/cadastros/funcionarios/funcionario_list', $data);
    }

    public function criar()
    {
        $this->view('administrador/funcionario/funcionario_create', []);
    }

    public function salvar()
    {
        $erros = Validador::validarFuncionario($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $_POST;
            $this->view('administrador/funcionario/funcionario_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId($_POST['id_empresa']);

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
        $this->listarTodos();
    }

    public function editar()
    {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/funcionarios');
        }

        $id = $_GET['id'];
        $data['funcionario'] = $this->funcionarioService->getFuncionarioById($id);
        $this->view('administrador/funcionario/funcionario_edit', $data);
    }

    public function excluir()
    {
        if (!isset($_POST['id'])) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $id = $_POST['id'];
        $this->funcionarioService->deleteFuncionario($id);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function atualizar()
    {
        $erros = Validador::validarFuncionario($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $_POST;
            $this->view('administrador/funcionario/funcionario_edit', $data); // corrige o typo "admnistrador"
            return;
        }

        // Busca a senha atual para não sobrescrever quando o campo vier em branco
        $funcionarioAtual = $this->funcionarioService->getFuncionarioById($_POST['id_funcionario']);
        $senha = !empty($_POST['senha_hash']) ? $_POST['senha_hash'] : $funcionarioAtual->getSenha();

        $empresa = (new Empresa())->setId((int) $_POST['id_empresa']); // usa setId() em vez de passar no construtor

        $funcionario = new Funcionario(
            (int) $_POST['id_funcionario'],
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $senha,
            $_POST['numero_telefone'],
            $empresa,
            (int) $_POST['bolotas_totais'],
            (int) $_POST['pontuacao_total'],
            (int) $_POST['nivel']
        );

        $this->funcionarioService->updateFuncionario($funcionario);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }
}
