<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Atividade;
use app\models\Modulo;
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
        $this->adminRequired();
        $data['lista'] = $this->atividadeService->getAtividades();
        $this->view('administrador/atividades/atividade_list', $data);
    }

    public function criar() {
        $this->adminRequired();
        $this->view('administrador/atividades/atividade_create', ['modulos' => $this->moduloService->getModulos()]);
    }

    public function salvar() {
        $this->adminRequired();
        $erros = Validador::validarAtividade($_POST);
        if (empty($erros['id_modulo']) && !$this->moduloService->getModuloById((int) $_POST['id_modulo'])) $erros['id_modulo'] = 'O módulo selecionado não existe.';
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['atividade'] = $_POST;
            $data['modulos'] = $this->moduloService->getModulos();
            $this->view('administrador/atividades/atividade_create', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setNome($_POST['nome']);
        $atividade->setTipoAtividade($_POST['tipo_atividade']);
        $atividade->setEstado($_POST['estado']);
        $atividade->setOrdem((int) $_POST['ordem']);
        $atividade->setTempoLimite((int) $_POST['tempo_limite']);

        $modulo = (new Modulo())->setId((int) $_POST['id_modulo']);
        $atividade->setModulo($modulo);

        $this->atividadeService->saveAtividade($atividade);
        $this->redirect(URL_BASE . '/administrador/atividades');
    }

    public function editar() {
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/atividades');
        }
        $data['atividade'] = $this->atividadeService->getAtividadeById($id);
        if (!$data['atividade']) $this->redirect(URL_BASE . '/administrador/atividades');
        $data['modulos'] = $this->moduloService->getModulos();
        $this->view('administrador/atividades/atividade_edit', $data);
    }

    public function excluir() {
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/atividades');
        }
        $this->atividadeService->deleteAtividade($id);
        $this->redirect(URL_BASE . '/administrador/atividades');
    }

    public function atualizar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->atividadeService->getAtividadeById($id)) $this->redirect(URL_BASE . '/administrador/atividades');
        $erros = Validador::validarAtividade($_POST);
        if (empty($erros['id_modulo']) && !$this->moduloService->getModuloById((int) $_POST['id_modulo'])) $erros['id_modulo'] = 'O módulo selecionado não existe.';
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $dados = $_POST;
            $dados['id_atividade'] = $id;
            $data['atividade'] = Atividade::arrayParaObjeto($dados);
            $data['modulos'] = $this->moduloService->getModulos();
            $this->view('administrador/atividades/atividade_edit', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setId($id);
        $atividade->setNome($_POST['nome']);
        $atividade->setTipoAtividade($_POST['tipo_atividade']);
        $atividade->setEstado($_POST['estado']);
        $atividade->setOrdem((int) $_POST['ordem']);
        $atividade->setTempoLimite((int) $_POST['tempo_limite']);

        $modulo = (new Modulo())->setId((int) $_POST['id_modulo']);
        $atividade->setModulo($modulo);

        $this->atividadeService->updateAtividade($atividade);
        $this->redirect(URL_BASE . '/administrador/atividades');
    }
}
