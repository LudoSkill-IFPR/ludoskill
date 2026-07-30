<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Exercicio;
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
        $data['lista'] = $this->exercicioService->getExercicios();
        $this->view('exercicios/exercicios_list', $data);
    }

    public function listarExercicio(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/exercicios');
        }

        $id = $_GET['id']; 
        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        $this->view('exercicios/exercicio_show', $data);
    }

    public function criar(){
        $this->view('exercicios/exercicio_create', []);
    }

    public function salvar(){
        $erros = Validador::validarExercicio($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $_POST;
            $this->view('exercicios/exercicio_create', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setDescricao($_POST['descricao']);
        $exercicio->setConteudo($_POST['conteudo']);

        $atividade = $this->atividadeService->getAtividadeById($_POST['atividade_id']);
        $exercicio->setAtividade($atividade);

        $this->exercicioService->saveExercicio($exercicio);
        $this->redirect(URL_BASE . '/exercicios');
    }

    public function editar(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/exercicios');
        }

        $id = $_GET['id'];

        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        $this->view('exercicios/exercicio_edit', $data);
    }

    public function excluir(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/exercicios');
        }

        $id = $_GET['id'];
        $this->exercicioService->deleteExercicio($id);
        $this->redirect(URL_BASE . '/exercicios');
    }

    public function atualizar(){
        $erros = Validador::validarExercicio($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $_POST;
            $this->view('exercicios/exercicio_edit', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setId($_POST['id']);
        $exercicio->setDescricao($_POST['descricao']);
        $exercicio->setConteudo($_POST['conteudo']);

        $atividade = $this->atividadeService->getAtividadeById($_POST['atividade_id']);
        $exercicio->setAtividade($atividade);

        $this->exercicioService->updateExercicio($exercicio);
        $this->redirect(URL_BASE . '/exercicios');
    }

}