<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Gestor;
use app\models\Empresa;
use app\services\GestorService;
use app\helpers\Validador;

class GestorController extends Controller {

    private GestorService $gestorService;
    public function __construct() {
        $this->gestorService = new GestorService();
    }

    public function listarTodos() {
        $data['lista'] = $this->gestorService->getGestores();
        $this->view('gestores/gestores_list', $data);
    }

    public function listarGestor() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $data['gestor'] = $this->gestorService->getGestorById($id);
        $this->view('gestores/gestor_show', $data);
    }

    public function criar() {
        $data['empresas'] = $this->gestorService->getEmpresas();
        $this->view('gestores/gestor_create', $data);
    }

    public function salvar() {
        $erros = Validador::validarGestor($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->gestorService->getEmpresas();
            $this->view('gestores/gestor_create', $data);
            return;
        }

        $empresa = new Empresa($_POST['id_empresa'], '', '', '', '', '', '');
        $gestor = new Gestor(
            0,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->saveGestor($gestor);
        $this->redirect(URL_BASE . '/gestores');
    }

    public function editar() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $data['gestor'] = $this->gestorService->getGestorById($id);
        $data['empresas'] = $this->gestorService->getEmpresas();
        $this->view('gestores/gestor_edit', $data);
    }

    public function excluir() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $this->gestorService->deleteGestor($id);
        $this->redirect(URL_BASE . '/gestores');
    }

    public function atualizar() {
        $erros = Validador::validarGestor($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->gestorService->getEmpresas();
            $this->view('gestores/gestor_edit', $data);
            return;
        }

        $empresa = new Empresa($_POST['id_empresa'], '', '', '', '', '', '');
        $gestor = new Gestor(
            $_POST['id_usuario'],
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->updateGestor($gestor);
        $this->redirect(URL_BASE . '/gestores');
    }
}