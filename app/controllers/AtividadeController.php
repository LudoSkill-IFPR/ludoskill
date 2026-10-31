<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Atividade;
use app\models\Modulo;
use app\services\AtividadeService;
use app\services\ModuloService;
use app\helpers\Validador;
use app\helpers\ConteudoHtmlHelper;
use app\services\FuncionarioAtividadeService;
use app\services\ExercicioService;

class AtividadeController extends Controller 
{
    
    private AtividadeService $atividadeService;
    private ModuloService $moduloService;
    private FuncionarioAtividadeService $funcionarioAtividadeService;
    private ExercicioService $exercicioService;

    public function __construct(){
        $this->atividadeService = new AtividadeService();
        $this->moduloService = new ModuloService();
        $this->funcionarioAtividadeService = new FuncionarioAtividadeService();
        $this->exercicioService = new ExercicioService();
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
        $dados = $_POST;
        $dados['descricao'] = ConteudoHtmlHelper::sanitizar((string) ($dados['descricao'] ?? ''));
        $erros = Validador::validarAtividade($dados);
        if (empty($erros['id_modulo']) && !$this->moduloService->getModuloById((int) $dados['id_modulo'])) $erros['id_modulo'] = 'O módulo selecionado não existe.';
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['atividade'] = $dados;
            $data['modulos'] = $this->moduloService->getModulos();
            $this->view('administrador/atividades/atividade_create', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setNome($dados['nome']);
        $atividade->setDescricao($dados['descricao']);
        $atividade->setTipoAtividade($dados['tipo_atividade']);
        $atividade->setEstado($dados['estado']);
        $atividade->setOrdem((int) $dados['ordem']);
        $atividade->setTempoLimite((int) $dados['tempo_limite']);

        $modulo = (new Modulo())->setId((int) $dados['id_modulo']);
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
        $dados = $_POST;
        $dados['descricao'] = ConteudoHtmlHelper::sanitizar((string) ($dados['descricao'] ?? ''));
        $erros = Validador::validarAtividade($dados);
        if (empty($erros['id_modulo']) && !$this->moduloService->getModuloById((int) $dados['id_modulo'])) $erros['id_modulo'] = 'O módulo selecionado não existe.';
        if (($dados['tipo_atividade'] ?? '') === 'TEXTO' && $this->exercicioService->existeExercicioNaAtividade($id)) {
            $erros['tipo_atividade'] = 'Remova os exercícios vinculados antes de alterar o tipo para Texto.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $dados['id_atividade'] = $id;
            $data['atividade'] = Atividade::arrayParaObjeto($dados);
            $data['modulos'] = $this->moduloService->getModulos();
            $this->view('administrador/atividades/atividade_edit', $data);
            return;
        }

        $atividade = new Atividade();
        $atividade->setId($id);
        $atividade->setNome($dados['nome']);
        $atividade->setDescricao($dados['descricao']);
        $atividade->setTipoAtividade($dados['tipo_atividade']);
        $atividade->setEstado($dados['estado']);
        $atividade->setOrdem((int) $dados['ordem']);
        $atividade->setTempoLimite((int) $dados['tempo_limite']);

        $modulo = (new Modulo())->setId((int) $dados['id_modulo']);
        $atividade->setModulo($modulo);

        $this->atividadeService->updateAtividade($atividade);
        $this->redirect(URL_BASE . '/administrador/atividades');
    }

    public function visualizarFuncionario(): void
    {
        $this->funcionarioRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect(URL_BASE . '/funcionario/modulos');

        $atividade = $this->atividadeService->getAtividadeById($id);
        $podeAcessar = $atividade && $this->funcionarioAtividadeService->podeAcessarAtividade(
            $_SESSION['usuario_logado']->getId(),
            $id
        );
        if (!$podeAcessar) $this->redirect(URL_BASE . '/funcionario/modulos');

        $temExercicios = $this->exercicioService->existeExercicioNaAtividade($id);
        $atividadeTexto = $atividade->getTipoAtividade() === 'TEXTO' && !$temExercicios;
        $resultado = $this->funcionarioAtividadeService->getResultadosByUsuario(
            $_SESSION['usuario_logado']->getId()
        )[$id] ?? null;
        $concluida = $resultado && $resultado->getEstado() === 'CONCLUIDO';

        if ($atividadeTexto && !$concluida) {
            $_SESSION['atividade_inicio'][$id] ??= microtime(true);
            $this->funcionarioAtividadeService->iniciarAtividade(
                $_SESSION['usuario_logado']->getId(),
                $id
            );
        }

        $this->view('funcionario/atividade', [
            'atividade' => $atividade,
            'descricao' => ConteudoHtmlHelper::sanitizar($atividade->getDescricao()),
            'temExercicios' => $temExercicios,
            'atividadeTexto' => $atividadeTexto,
            'concluida' => $concluida,
        ]);
    }

    public function concluirTexto(): void
    {
        $this->funcionarioRequired();
        $id = filter_input(INPUT_POST, 'id_atividade', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect(URL_BASE . '/funcionario/modulos');

        $atividade = $this->atividadeService->getAtividadeById($id);
        $podeAcessar = $atividade && $this->funcionarioAtividadeService->podeAcessarAtividade(
            $_SESSION['usuario_logado']->getId(),
            $id
        );
        $atividadeTextoSemExercicios = $atividade
            && $atividade->getTipoAtividade() === 'TEXTO'
            && !$this->exercicioService->existeExercicioNaAtividade($id);

        if (!$podeAcessar || !$atividadeTextoSemExercicios) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $inicio = (float) ($_SESSION['atividade_inicio'][$id] ?? microtime(true));
        $tempoExecucao = max(0.001, microtime(true) - $inicio);
        $this->funcionarioAtividadeService->concluirAtividadeTexto(
            $_SESSION['usuario_logado']->getId(),
            $id,
            $tempoExecucao
        );
        unset($_SESSION['atividade_inicio'][$id]);

        $this->redirect(URL_BASE . '/funcionario/atividades/conteudo?id=' . $id);
    }
}
