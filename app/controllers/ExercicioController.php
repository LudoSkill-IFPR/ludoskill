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
        $dados = $_POST;
        $questoes = $this->normalizarQuestoes($dados['questoes'] ?? []);
        $dados['conteudo'] = json_encode($questoes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['questoes'] = $questoes;
            $data['atividades'] = $this->atividadeService->getAtividades();
            $this->view('administrador/exercicios/exercicio_create', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setDescricao(trim($dados['descricao']));
        $exercicio->setConteudo($dados['conteudo']);

        $atividade = (new Atividade())->setId((int) $dados['atividade_id']);
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
        $data['questoes'] = $this->decodificarConteudo($data['exercicio']['conteudo']);
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
        $dados = $_POST;
        $questoes = $this->normalizarQuestoes($dados['questoes'] ?? []);
        $dados['conteudo'] = json_encode($questoes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['questoes'] = $questoes;
            $data['atividades'] = $this->atividadeService->getAtividades();
            $this->view('administrador/exercicios/exercicio_edit', $data);
            return;
        }

        $exercicio = new Exercicio();
        $exercicio->setId($id);
        $exercicio->setDescricao(trim($dados['descricao']));
        $exercicio->setConteudo($dados['conteudo']);

        $atividade = (new Atividade())->setId((int) $dados['atividade_id']);
        $exercicio->setAtividade($atividade);

        $this->exercicioService->updateExercicio($exercicio);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

    public function acessarAtividade(){
        $this->funcionarioRequired();
        $idAtividade = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idAtividade) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $atividade = $this->atividadeService->getAtividadeById($idAtividade);
        if (!$atividade) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $registros = $this->exercicioService->getExerciciosByAtividade($idAtividade);
        $exercicios = [];

        foreach ($registros as $registro) {
            $exercicios[] = [
                'id_exercicio' => $registro['id_exercicio'],
                'descricao' => $registro['descricao'],
                'questoes' => $this->decodificarConteudo($registro['conteudo'])
            ];
        }

        $this->view('funcionario/exercicios', [
            'atividade' => $atividade,
            'exercicios' => $exercicios
        ]);
    }

    private function normalizarQuestoes(array $questoes): array {
        $normalizadas = [];

        foreach ($questoes as $questao) {
            if (!is_array($questao)) continue;

            $alternativas = array_values(array_map(
                fn($alternativa) => trim((string) $alternativa),
                is_array($questao['alternativas'] ?? null) ? $questao['alternativas'] : []
            ));

            $correta = filter_var(
                $questao['alternativa_correta'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0]]
            );

            $normalizadas[] = [
                'questao' => trim((string) ($questao['questao'] ?? '')),
                'enunciado' => trim((string) ($questao['enunciado'] ?? '')),
                'alternativas' => $alternativas,
                'alternativa_correta' => $correta === false ? null : $correta,
                'justificativa' => trim((string) ($questao['justificativa'] ?? ''))
            ];
        }

        return $normalizadas;
    }

    private function decodificarConteudo(string $conteudo): array {
        $questoes = json_decode($conteudo, true);
        return is_array($questoes) ? $questoes : [];
    }

}
