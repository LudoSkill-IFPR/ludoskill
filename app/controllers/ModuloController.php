<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Modulo;
use app\services\ModuloService;
use app\services\AtividadeService;
use app\services\FuncionarioAtividadeService;
use app\helpers\Validador;

class ModuloController extends Controller
{
    private ModuloService $moduloService;
    private AtividadeService $atividadeService;
    private FuncionarioAtividadeService $funcionarioAtividadeService;

    public function __construct() {
        $this->moduloService = new ModuloService();
        $this->atividadeService = new AtividadeService();
        $this->funcionarioAtividadeService = new FuncionarioAtividadeService();
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->moduloService->getModulos();
        $this->view('/administrador/modulos/modulo_list', $data);
    }

    public function listarTodosFuncionario() {
        $this->funcionarioRequired();
        $data['lista'] = $this->moduloService->getModulos();
        foreach ($data['lista'] as &$modulo) {
            $modulo['atividades'] = $this->atividadeService->getAtividadesByModulo((int) $modulo['id_modulo']);
        }
        unset($modulo);
        $data['resultados'] = $this->funcionarioAtividadeService->getResultadosByUsuario(
            $_SESSION['usuario_logado']->getId()
        );
        $this->view('/funcionario/modulos', $data);
    }

    public function listarModulo(){
        $this->adminRequired();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }

        $data['modulo'] = $this->moduloService->getModuloById($id);
        if (!$data['modulo']) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }
        $this->view('/administrador/modulos/modulo_list', $data);
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
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }

        $this->moduloService->deleteModulo($id);
        $this->redirect(URL_BASE . '/administrador/modulos');
    }

    public function atualizar(){
        $this->adminRequired();
        $id = $_POST['id'] ?? $_POST['id_modulo'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT) || !$this->moduloService->getModuloById((int) $id)) {
            $this->redirect(URL_BASE . '/administrador/modulos');
        }
        $_POST['id'] = $id;

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
