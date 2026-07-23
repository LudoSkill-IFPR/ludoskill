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
        $data['lista'] = $this->moduloService->getModulos();
        $this->view('modulos/modulos_list', $data);
    }

    public function listarModulos(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/modulos');
        }

        $id = $_GET['id']; 
        $data['modulo'] = $this->moduloService->getModuloById($id);
        $this->view('modulos/modulo_show', $data);
    }

    public function criar(){
        $this->view('modulos/modulo_create', []);
    }

    public function salvar(){
        $erros = Validador::validarModulo($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['modulo'] = $_POST;
            $this->view('modulos/modulo_create', $data);
            return;
        }

        $modulo = new Modulo();
        $modulo->setNome($_POST['nome']);
        $modulo->setDescricao($_POST['descricao']);
        $modulo->setMinEstrelasLiberacao($_POST['min_estrelas_liberacao']);

        $this->moduloService->saveModulo($modulo);
        $this->redirect(URL_BASE . '/modulos');
    }

    public function editar(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/modulos');
        }

        $id = $_GET['id'];
        $data['modulo'] = $this->moduloService->getModuloById($id);
        $this->view('modulos/modulo_edit', $data);
    }

    public function excluir(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/modulos');
        }

        $id = $_GET['id'];
        $this->moduloService->deleteModulo($id);
        $this->redirect(URL_BASE . '/modulos');
    }

    public function atualizar(){
        $erros = Validador::validarModulo($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['modulo'] = $_POST;
            $this->view('modulos/modulo_edit', $data);
            return;
        }

        $modulo = new Modulo();
        $modulo->setId($_POST['id']);
        $modulo->setNome($_POST['nome']);
        $modulo->setDescricao($_POST['descricao']);
        $modulo->setMinEstrelasLiberacao($_POST['min_estrelas_liberacao']);

        $this->moduloService->updateModulo($modulo);
        $this->redirect(URL_BASE . '/modulos');
    }
}