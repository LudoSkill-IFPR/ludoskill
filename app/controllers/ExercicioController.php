<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Exercicio;
use app\models\Atividade;
use app\services\ExercicioService;
use app\services\AtividadeService;
use app\services\FuncionarioAtividadeService;
use app\helpers\Validador;

class ExercicioController extends Controller{

    private ExercicioService $exercicioService;
    private AtividadeService $atividadeService;
    private FuncionarioAtividadeService $funcionarioAtividadeService;

    /**
     * Prepara os serviços usados tanto pelo CRUD administrativo quanto pela realização das atividades.
     */
    public function __construct() {
        $this->exercicioService = new ExercicioService();
        $this->atividadeService = new AtividadeService();
        $this->funcionarioAtividadeService = new FuncionarioAtividadeService();
    }

    /**
     * Exibe a listagem administrativa de exercícios.
     *
     * Restringe o acesso ao administrador, busca as entidades pelo service e as
     * envia para a view responsável pela tabela do CRUD.
     */
    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->exercicioService->getExercicios();
        $this->view('administrador/exercicios/exercicio_list', $data);
    }

    /**
     * Abre o formulário de criação de exercício.
     *
     * Além de exigir o perfil administrador, carrega todas as atividades para que
     * o usuário possa escolher a qual delas o novo conjunto de questões pertencerá.
     */
    public function criar(){
        $this->adminRequired();
        $this->view('administrador/exercicios/exercicio_create', ['atividades' => $this->getAtividadesComExercicios()]);
    }

    /**
     * Valida e salva um novo exercício enviado pelo formulário.
     *
     * Primeiro normaliza a lista dinâmica de questões e a transforma em JSON.
     * Depois aplica as validações de estrutura e confirma que a atividade existe.
     * Em caso de erro, reabre o formulário com os dados digitados; caso contrário,
     * monta as entidades Exercicio e Atividade, salva pelo service e volta à lista.
     */
    public function salvar(){
        $this->adminRequired();
        $dados = $_POST;
        $questoes = $this->normalizarQuestoes($dados['questoes'] ?? []);
        $dados['conteudo'] = json_encode($questoes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (empty($erros['atividade_id'])) {
            $atividadeSelecionada = $this->atividadeService->getAtividadeById((int) $dados['atividade_id']);
            if (!$atividadeSelecionada) {
                $erros['atividade_id'] = 'A atividade selecionada não existe.';
            } elseif ($atividadeSelecionada->getTipoAtividade() === 'TEXTO') {
                $erros['atividade_id'] = 'Atividades do tipo Texto não podem possuir exercícios.';
            }
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['questoes'] = $questoes;
            $data['atividades'] = $this->getAtividadesComExercicios();
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

    /**
     * Abre o formulário de edição de um exercício existente.
     *
     * Valida o ID recebido pela URL, busca o exercício e redireciona caso ele não
     * exista. O JSON salvo é decodificado para preencher os campos dinâmicos de
     * questões, e as atividades são carregadas para preencher o campo de vínculo.
     */
    public function editar(){
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        if (!$data['exercicio']) $this->redirect(URL_BASE . '/administrador/exercicios');
        $data['questoes'] = $this->decodificarConteudo($data['exercicio']->getConteudo());
        $data['atividades'] = $this->getAtividadesComExercicios();
        $this->view('administrador/exercicios/exercicio_edit', $data);
    }

    /**
     * Exclui um exercício pelo ID enviado em uma requisição POST.
     *
     * A ação é exclusiva do administrador. IDs inválidos levam de volta à lista;
     * IDs válidos são encaminhados ao service e, em seguida, ocorre o redirecionamento.
     */
    public function excluir(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $this->exercicioService->deleteExercicio($id);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

    /**
     * Valida e atualiza um exercício existente.
     *
     * Confirma o ID e a existência do registro, normaliza novamente as questões,
     * gera o JSON e executa as mesmas validações usadas no cadastro. Havendo erro,
     * preserva os valores do formulário; sem erros, monta a entidade com seu ID e
     * solicita a atualização ao service.
     */
    public function atualizar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->exercicioService->getExercicioById($id)) $this->redirect(URL_BASE . '/administrador/exercicios');
        $dados = $_POST;
        $questoes = $this->normalizarQuestoes($dados['questoes'] ?? []);
        $dados['conteudo'] = json_encode($questoes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (empty($erros['atividade_id'])) {
            $atividadeSelecionada = $this->atividadeService->getAtividadeById((int) $dados['atividade_id']);
            if (!$atividadeSelecionada) {
                $erros['atividade_id'] = 'A atividade selecionada não existe.';
            } elseif ($atividadeSelecionada->getTipoAtividade() === 'TEXTO') {
                $erros['atividade_id'] = 'Atividades do tipo Texto não podem possuir exercícios.';
            }
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['questoes'] = $questoes;
            $data['atividades'] = $this->getAtividadesComExercicios();
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

    /**
     * Prepara uma atividade para ser realizada pelo funcionário.
     *
     * Confirma o perfil, o ID da atividade e as regras de liberação. Em seguida,
     * reúne e decodifica os exercícios, registra no servidor o horário de início e
     * marca o progresso como iniciado antes de renderizar a tela de respostas.
     */
    public function acessarAtividade(){
        $this->funcionarioRequired();
        $idAtividade = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idAtividade) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $atividade = $this->atividadeService->getAtividadeById($idAtividade);
        if (!$atividade || !$this->funcionarioAtividadeService->podeAcessarAtividade($_SESSION['usuario_logado']->getId(), $idAtividade)) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }
        if ($atividade->getTipoAtividade() === 'TEXTO') {
            $this->redirect(URL_BASE . '/funcionario/atividades/conteudo?id=' . $idAtividade);
        }

        $exercicios = $this->montarExerciciosDaAtividade($idAtividade);
        if (empty($exercicios)) {
            $this->redirect(URL_BASE . '/funcionario/atividades/conteudo?id=' . $idAtividade);
        }
        $_SESSION['atividade_inicio'][$idAtividade] = microtime(true);
        $this->funcionarioAtividadeService->iniciarAtividade($_SESSION['usuario_logado']->getId(), $idAtividade);

        $this->view('funcionario/exercicios', [
            'atividade' => $atividade,
            'exercicios' => $exercicios
        ]);
    }

    /**
     * Corrige todas as respostas submetidas e registra o resultado da atividade.
     *
     * A função valida novamente o acesso e exige uma alternativa válida em cada
     * questão. Depois calcula percentual de acertos, tempo, estrelas e bolotas,
     * envia esses dados ao service de progresso e apresenta o feedback completo
     * ao funcionário, incluindo as justificativas armazenadas no JSON.
     */
    public function responderAtividade(){
        $this->funcionarioRequired();
        $idAtividade = filter_input(INPUT_POST, 'id_atividade', FILTER_VALIDATE_INT);
        if (!$idAtividade) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $atividade = $this->atividadeService->getAtividadeById($idAtividade);
        if (!$atividade || !$this->funcionarioAtividadeService->podeAcessarAtividade($_SESSION['usuario_logado']->getId(), $idAtividade)) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }
        if ($atividade->getTipoAtividade() === 'TEXTO') {
            $this->redirect(URL_BASE . '/funcionario/atividades/conteudo?id=' . $idAtividade);
        }

        $exercicios = $this->montarExerciciosDaAtividade($idAtividade);
        $respostas = is_array($_POST['respostas'] ?? null) ? $_POST['respostas'] : [];
        $total = 0;
        $acertos = 0;

        foreach ($exercicios as $exercicio) {
            foreach ($exercicio['questoes'] as $indice => $questao) {
                $total++;
                $idExercicio = (string) $exercicio['id_exercicio'];
                $resposta = filter_var(
                    $respostas[$idExercicio][$indice] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 0]]
                );
                if ($resposta === false || !array_key_exists($resposta, $questao['alternativas'] ?? [])) {
                    $this->view('funcionario/exercicios', [
                        'atividade' => $atividade,
                        'exercicios' => $exercicios,
                        'respostas' => $respostas,
                        'erro' => 'Responda todas as questões antes de finalizar.'
                    ]);
                    return;
                }
                if ((int) $resposta === (int) ($questao['alternativa_correta'] ?? -1)) {
                    $acertos++;
                }
            }
        }

        if ($total === 0) {
            $this->redirect(URL_BASE . '/funcionario/modulos');
        }

        $pontuacao = (int) round(($acertos / $total) * 100);
        $inicio = (float) ($_SESSION['atividade_inicio'][$idAtividade] ?? microtime(true));
        $tempoExecucao = max(0.001, microtime(true) - $inicio);
        $erros = $total - $acertos;
        $estrelas = max(0, 3 - $erros - ($tempoExecucao > $atividade->getTempoLimite() ? 1 : 0));
        $concluida = $pontuacao >= 66;
        $bolotas = $concluida ? $acertos * 10 : 0;
        unset($_SESSION['atividade_inicio'][$idAtividade]);

        $registro = $this->funcionarioAtividadeService->registrarResultado(
            $_SESSION['usuario_logado']->getId(),
            $idAtividade,
            $pontuacao,
            $estrelas,
            $bolotas,
            $tempoExecucao
        );

        $this->view('funcionario/exercicios', [
            'atividade' => $atividade,
            'exercicios' => $exercicios,
            'respostas' => $respostas,
            'resultado' => [
                'acertos' => $acertos,
                'total' => $total,
                'pontuacao' => $pontuacao,
                'estrelas' => $estrelas,
                'bolotas' => $registro->getBolotasObtidas(),
                'tentativas' => $registro->getQtdTentativas(),
                'tempo' => $tempoExecucao
            ]
        ]);
    }

    /**
     * Monta a estrutura de exercícios consumida pela view do funcionário.
     *
     * Busca os objetos vinculados à atividade e converte o conteúdo JSON de cada
     * um em um array de questões, preservando ID e descrição do conjunto.
     */
    private function montarExerciciosDaAtividade(int $idAtividade): array {
        $exercicios = [];
        foreach ($this->exercicioService->getExerciciosByAtividade($idAtividade) as $registro) {
            $exercicios[] = [
                'id_exercicio' => $registro->getId(),
                'descricao' => $registro->getDescricao(),
                'questoes' => $this->decodificarConteudo($registro->getConteudo())
            ];
        }
        return $exercicios;
    }

    /**
     * Lista apenas atividades que aceitam exercícios no formulário administrativo.
     */
    private function getAtividadesComExercicios(): array {
        return array_values(array_filter(
            $this->atividadeService->getAtividades(),
            static fn(Atividade $atividade): bool => $atividade->getTipoAtividade() !== 'TEXTO'
        ));
    }

    /**
     * Padroniza os campos enviados pelo formulário dinâmico antes de gerar o JSON.
     *
     * Ignora entradas que não sejam arrays, remove espaços desnecessários, reorganiza
     * os índices das alternativas e transforma o índice correto em inteiro válido.
     * A saída possui apenas enunciado, alternativas, alternativa correta e justificativa.
     */
    private function normalizarQuestoes(array $questoes): array {
        $normalizadas = [];

        foreach ($questoes as $exercicio) {
            if (!is_array($exercicio)) continue;

            $alternativas = array_values(array_map(
                fn($alternativa) => trim((string) $alternativa),
                is_array($exercicio['alternativas'] ?? null) ? $exercicio['alternativas'] : []
            ));

            $correta = filter_var(
                $exercicio['alternativa_correta'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0]]
            );

            $normalizadas[] = [
                'enunciado' => trim((string) ($exercicio['enunciado'] ?? '')),
                'alternativas' => $alternativas,
                'alternativa_correta' => $correta === false ? null : $correta,
                'justificativa' => trim((string) ($exercicio['justificativa'] ?? ''))
            ];
        }

        return $normalizadas;
    }

    /**
     * Decodifica o JSON salvo no exercício para um array PHP.
     *
     * Se o conteúdo não formar uma lista válida, devolve um array vazio para que a
     * camada de apresentação não tente percorrer um valor incompatível.
     */
    private function decodificarConteudo(string $conteudo): array {
        $questoes = json_decode($conteudo, true);
        return is_array($questoes) ? $questoes : [];
    }

}
