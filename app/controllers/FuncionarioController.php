<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Funcionario;
use app\services\FuncionarioService;
use app\helpers\Validador;

class FuncionarioController extends Controller {
    private FuncionarioService $funcionarioService;

    public function __construct() {
        $this->funcionarioService = new FuncionarioService();
    }

    public function listarTodos() {
        $data['lista'] = $this->funcionarioService->getFuncionarios();
        $this->view('funcionarios/funcionarios_list', $data);
    }

    public function listarFuncionario() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/funcionarios');
        }

        $id = $_GET['id'];
        $data['funcionario'] = $this->funcionarioService->getFuncionarioById($id);
        $this->view('funcionarios/funcionario_show', $data);
    }

    public function criar() {
        $this->view('funcionarios/funcionario_create', []);
    }

    public function salvar() {
        $erros = Validador::validarFuncionario($_POST);
        print_r($erros);
        if (!empty($erros)) {
            print("to aqui");
            $data['erros'] = $erros;
            $data['funcionario'] = $_POST;
            $this->view('sistema/cadastros/funcionarios/funcionario_create', $data);
            return;
        }

        $empresa = new \app\models\Empresa($_POST['empresa_id'], '', '', '', '', '', '');
        $funcionario = new Funcionario(
            0,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa,
            0,
            0,
            1
        );
        print("to aqui");
        $this->funcionarioService->saveFuncionario($funcionario);
        $this->redirect(URL_BASE . '/funcionario/salvar');
    }

    public function editar() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/funcionarios');
        }

        $id = $_GET['id'];
        $data['funcionario'] = $this->funcionarioService->getFuncionarioById($id);
        $this->view('funcionarios/funcionario_edit', $data);
    }

    public function excluir() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/funcionarios');
        }

        $id = $_GET['id'];
        $this->funcionarioService->deleteFuncionario($id);
        $this->redirect(URL_BASE . '/funcionarios');
    }

    public function atualizar() {
        $erros = Validador::validarFuncionario($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $_POST;
            $this->view('funcionarios/funcionario_edit', $data);
            return;
        }

        $empresa = new \app\models\Empresa($_POST['empresa_id'], '', '', '', '', '', '');
        $funcionario = new Funcionario(
            $_POST['id'],
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa,
            $_POST['bolotas_totais'],
            $_POST['pontuacao_total'],
            $_POST['nivel']
        );

        $this->funcionarioService->updateFuncionario($funcionario);
        $this->redirect(URL_BASE . '/funcionarios');
    }
}