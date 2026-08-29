<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Exercicio;
use app\models\Atividade;
use app\services\ExercicioService;
use app\services\AtividadeService;
use app\helpers\Validador;

class ExercicioController extends Controller{

    private ExercicioService $exercicioService;
    private AtividadeService $atividadeService;

    public function __construct() {
        $this->exercicioService = new ExercicioService();
        $this->atividadeService = new AtividadeService();
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->exercicioService->getExercicios();
        $this->view('administrador/exercicios/exercicio_list', $data);
    }

    public function listarExercicio(){


        $this->adminRequired();

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $id = $_GET['id']; 
        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        $this->view('exercicios/exercicio_list', $data);
    }

    public function criar(){
        $this->adminRequired();
        $this->view('administrador/exercicios/exercicio_create', ['atividades' => $this->atividadeService->getAtividades()]);
    }

    public function salvar(){
        $this->adminRequired();
        $erros = Validador::validarExercicio($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $_POST;
            $data['atividades'] = $this->atividadeService->getAtividades();
            $this->view('administrador/exercicios/exercicio_create', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setDescricao($_POST['descricao']);
        $exercicio->setConteudo($_POST['conteudo']);

        $atividade = (new Atividade())->setId((int) $_POST['atividade_id']);
        $exercicio->setAtividade($atividade);

        $this->exercicioService->saveExercicio($exercicio);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

    public function editar(){
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        if (!$data['exercicio']) $this->redirect(URL_BASE . '/administrador/exercicios');
        $data['atividades'] = $this->atividadeService->getAtividades();
        $this->view('administrador/exercicios/exercicio_edit', $data);
    }

    public function excluir(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $this->exercicioService->deleteExercicio($id);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

    public function atualizar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->exercicioService->getExercicioById($id)) $this->redirect(URL_BASE . '/administrador/exercicios');
        $erros = Validador::validarExercicio($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $_POST;
            $data['atividades'] = $this->atividadeService->getAtividades();
            $this->view('administrador/exercicios/exercicio_edit', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setId($id);
        $exercicio->setDescricao($_POST['descricao']);
        $exercicio->setConteudo($_POST['conteudo']);

        $atividade = (new Atividade())->setId((int) $_POST['atividade_id']);
        $exercicio->setAtividade($atividade);

        $this->exercicioService->updateExercicio($exercicio);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

}
