<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Atividade;
use app\services\AtividadeService;
use app\services\ModuloService;
use app\helpers\Validador;

class AtividadeController extends Controller 
{
    
    private AtividadeService $atividadeService;
    private ModuloService $moduloService;

    public function __construct(){
        $this->atividadeService = new AtividadeService();
        $this->moduloService = new ModuloService();
    }

    public function listarTodos() {
        $data['lista'] = $this->atividadeService->getAtividades();
        $this->view('atividades/atividades_list', $data);
    }

    //
    public function listarAtividade() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/atividades');
        }

        $id = $_GET['id'];
        $data['atividade'] = $this->atividadeService->getAtividadeById($id);
        $this->view('atividades/atividade_show', $data);
    }

    public function criar() {
        $this->view('atividades/atividade_create', []);
    }

    public function salvar() {
        $erros = Validador::validarAtividade($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['atividade'] = $_POST;
            $this->view('atividades/atividade_create', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setNome($_POST['nome']);
        $atividade->setTipoAtividade($_POST['tipo_atividade']);
        $atividade->setEstado($_POST['estado']);
        $atividade->setPontuacao($_POST['pontuacao']);
        $atividade->setEstrelas($_POST['estrelas']);

        // Aqui você precisaria criar um objeto Modulo com base no ID do módulo enviado no formulário
        // Supondo que você tenha um método para buscar o módulo pelo ID
        $modulo = $this->moduloService->getModuloById($_POST['modulo_id']);
        $atividade->setModulo($modulo);

        $this->atividadeService->saveAtividade($atividade);
        $this->redirect(URL_BASE . '/atividades');
    }

    public function editar() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/atividades');
        }

        $id = $_GET['id'];
        $data['atividade'] = $this->atividadeService->getAtividadeById($id);
        $this->view('atividades/atividade_edit', $data);
    }

    public function excluir() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/atividades');
        }

        $id = $_GET['id'];
        $this->atividadeService->deleteAtividade($id);
        $this->redirect(URL_BASE . '/atividades');
    }

    public function atualizar(){
        
        $erros = Validador::validarAtividade($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['atividade'] = $_POST;
            $this->view('atividades/atividade_edit', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setId($_POST['id']);
        $atividade->setNome($_POST['nome']);
        $atividade->setTipoAtividade($_POST['tipo_atividade']);
        $atividade->setEstado($_POST['estado']);
        $atividade->setPontuacao($_POST['pontuacao']);
        $atividade->setEstrelas($_POST['estrelas']);

        // Aqui você precisaria criar um objeto Modulo com base no ID do módulo enviado no formulário
        // Supondo que você tenha um método para buscar o módulo pelo ID
        $modulo = $this->moduloService->getModuloById($_POST['modulo_id']);
        $atividade->setModulo($modulo);

        $this->atividadeService->updateAtividade($atividade);
        $this->redirect(URL_BASE . '/atividades');
    }
}