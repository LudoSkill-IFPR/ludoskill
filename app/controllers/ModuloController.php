<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Modulo;
use app\services\ModuloService;
use app\helpers\Validador;

class ModuloController extends Controller
{
    private ModuloService $moduloService;

    public function __construct() {
        $this->moduloService = new ModuloService();
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->moduloService->getModulos();
        $this->view('/administrador/modulos/modulo_list', $data);
    }

    public function listarTodosFuncionario() {
        $this->autenticacaoRequired();
        $data['lista'] = $this->moduloService->getModulos();
        $this->view('/funcionario/modulos', $data);
    }

    public function listarModulo(){
        $this->adminRequired();

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }

        $id = $_GET['id']; 
        $data['modulo'] = $this->moduloService->getModuloById($id);
        $this->view('/administrador/modulos/modulo_show', $data);
    }

    public function criar(){
        $this->adminRequired();
        $this->view('/administrador/modulos/modulo_create', []);
    }

    public function salvar(){
        $this->adminRequired();
        $erros = Validador::validarModulo($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['modulo'] = $_POST;
            $this->view('/administrador/modulos/modulo_create', $data);
            return;
        }

        $modulo = new Modulo();
        $modulo->setNome($_POST['nome']);
        $modulo->setDescricao($_POST['descricao']);
        $modulo->setMinEstrelasLiberacao($_POST['min_estrelas_liberacao']);

        $this->moduloService->saveModulo($modulo);
        $this->redirect(URL_BASE . '/administrador/modulos');
    }

    public function editar(){
        $this->adminRequired();
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }

        $id = $_GET['id'];
        $data['modulo'] = $this->moduloService->getModuloById($id);
        $this->view('/administrador/modulos/modulo_edit', $data);
    }

    public function excluir(){
        $this->adminRequired();
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (empty($id)) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }

        $this->moduloService->deleteModulo($id);
        $this->redirect(URL_BASE . '/administrador/modulos');
    }

    public function atualizar(){
        $this->adminRequired();
        $id = $_POST['id'] ?? $_POST['id_modulo'] ?? null;
        if ($id !== null) {
            $_POST['id'] = $id;
        }

        $erros = Validador::validarModulo($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['modulo'] = $_POST;
            $this->view('/administrador/modulos/modulo_edit', $data);
            return;
        }

        $modulo = new Modulo();
        $modulo->setId((int) $_POST['id']);
        $modulo->setNome($_POST['nome']);
        $modulo->setDescricao($_POST['descricao']);
        $modulo->setMinEstrelasLiberacao((int) $_POST['min_estrelas_liberacao']);

        $this->moduloService->updateModulo($modulo);
        $this->redirect(URL_BASE . '/administrador/modulos');
    }
}