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

    public function __construct() {
        $this->exercicioService = new ExercicioService();
        $this->atividadeService = new AtividadeService();
        $this->funcionarioAtividadeService = new FuncionarioAtividadeService();
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->exercicioService->getExercicios();
        $this->view('administrador/exercicios/exercicio_list', $data);
    }


    public function criar(){
        $this->adminRequired();
        $this->view('administrador/exercicios/exercicio_create', ['atividades' => $this->getAtividadesComExercicios()]);
    }

    //Valida e salva um novo exercício enviado pelo formulário.
    //Normaliza questões ou etapas de simulação e gera o JSON com o tipo no nível principal.
    //Depois aplica as validações de estrutura e confirma que a atividade existe.
    //Em caso de erro, reabre o formulário com os dados digitados; caso contrário, monta as entidades Exercicio e Atividade, salva pelo service e volta à lista.

    public function salvar(){
        $this->adminRequired();
        $dados = $_POST;
        $dados['tipo'] = $this->normalizarTipo((string) ($dados['tipo'] ?? 'exercicio'));
        $conteudo = $dados['tipo'] === 'simulacao'
            ? [
                'tipo' => 'simulacao',
                'etapas' => $this->normalizarEtapas($dados['etapas'] ?? []),
                'ferramentas' => $this->normalizarFerramentas($dados['ferramentas'] ?? []),
                'peca_quebrada' => (string) ($dados['peca_quebrada'] ?? ''),
                'componentes' => ['bateria', 'camera', 'chip'],
            ]
            : [
                'tipo' => 'exercicio',
                'questoes' => $this->normalizarQuestoes($dados['questoes'] ?? []),
            ];
        $dados['conteudo'] = json_encode($conteudo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (empty($erros['atividade_id'])) {
            $atividadeSelecionada = $this->atividadeService->getAtividadeById((int) $dados['atividade_id']);
            if (!$atividadeSelecionada) {
                $erros['atividade_id'] = 'A atividade selecionada não existe.';
            } elseif ($atividadeSelecionada->getTipoAtividade() === 'TEXTO') {
                $erros['atividade_id'] = 'Atividades do tipo Texto não podem possuir exercícios.';
            } elseif (!$this->tipoCompativelComAtividade((int) $dados['atividade_id'], $dados['tipo'])) {
                $erros['atividade_id'] = 'Uma atividade não pode misturar exercícios de questões e simulações.';
            }
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['tipo'] = $dados['tipo'];
            $data['conteudoExercicio'] = $conteudo;
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


    //Abre o formulário de edição de um exercício existente.
    //Valida o ID, decodifica o JSON conforme o tipo e preenche o formulário de edição correspondente.

    public function editar(){
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $data['exercicio'] = $this->exercicioService->getExercicioById($id);
        if (!$data['exercicio']) $this->redirect(URL_BASE . '/administrador/exercicios');
        $data['conteudoExercicio'] = $this->decodificarConteudo($data['exercicio']->getConteudo());
        $data['tipo'] = $data['conteudoExercicio']['tipo'];
        $data['atividades'] = $this->getAtividadesComExercicios();
        $this->view('administrador/exercicios/exercicio_edit', $data);
    }


    //Exclui um exercício pelo ID enviado em uma requisição POST.
    //A ação é exclusiva do administrador. IDs inválidos levam de volta à lista;
    //IDs válidos são encaminhados ao service e, em seguida, ocorre o redirecionamento.

    public function excluir(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/exercicios');
        }

        $this->exercicioService->deleteExercicio($id);
        $this->redirect(URL_BASE . '/administrador/exercicios');
    }

    //Valida e atualiza um exercício existente.
    //Confirma o ID, normaliza o conteúdo conforme o tipo e executa as mesmas validações do cadastro.
    public function atualizar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->exercicioService->getExercicioById($id)) $this->redirect(URL_BASE . '/administrador/exercicios');
        $dados = $_POST;
        $dados['tipo'] = $this->normalizarTipo((string) ($dados['tipo'] ?? 'exercicio'));
        $conteudo = $dados['tipo'] === 'simulacao'
            ? [
                'tipo' => 'simulacao',
                'etapas' => $this->normalizarEtapas($dados['etapas'] ?? []),
                'ferramentas' => $this->normalizarFerramentas($dados['ferramentas'] ?? []),
                'peca_quebrada' => (string) ($dados['peca_quebrada'] ?? ''),
                'componentes' => ['bateria', 'camera', 'chip'],
            ]
            : [
                'tipo' => 'exercicio',
                'questoes' => $this->normalizarQuestoes($dados['questoes'] ?? []),
            ];
        $dados['conteudo'] = json_encode($conteudo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $erros = Validador::validarExercicio($dados);
        if (empty($erros['atividade_id'])) {
            $atividadeSelecionada = $this->atividadeService->getAtividadeById((int) $dados['atividade_id']);
            if (!$atividadeSelecionada) {
                $erros['atividade_id'] = 'A atividade selecionada não existe.';
            } elseif ($atividadeSelecionada->getTipoAtividade() === 'TEXTO') {
                $erros['atividade_id'] = 'Atividades do tipo Texto não podem possuir exercícios.';
            } elseif (!$this->tipoCompativelComAtividade((int) $dados['atividade_id'], $dados['tipo'], $id)) {
                $erros['atividade_id'] = 'Uma atividade não pode misturar exercícios de questões e simulações.';
            }
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['exercicio'] = $dados;
            $data['tipo'] = $dados['tipo'];
            $data['conteudoExercicio'] = $conteudo;
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


    //Prepara uma atividade para ser realizada pelo funcionário.
    //Confirma o perfil, o ID da atividade e as regras de liberação. Em seguida, reúne e decodifica os exercícios, registra no servidor o horário de início e marca o progresso como iniciado antes de renderizar a tela de respostas.
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
        $_SESSION['atividade_inicio'][$idAtividade] ??= microtime(true);
        $this->funcionarioAtividadeService->iniciarAtividade($_SESSION['usuario_logado']->getId(), $idAtividade);

        $this->view($this->getViewRealizacao($exercicios), [
            'atividade' => $atividade,
            'exercicios' => $exercicios,
            'tempoRestante' => $this->calcularTempoRestante($atividade, $idAtividade),
        ]);
    }


    //Corrige todas as respostas submetidas e registra o resultado da atividade.
    //Corrige alternativas em exercícios ou a ordem das ações práticas em simulações e registra o resultado.

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
        $respostasSimulacao = is_array($_POST['respostas_simulacao'] ?? null) ? $_POST['respostas_simulacao'] : [];
        $total = 0;
        $acertos = 0;

        foreach ($exercicios as $exercicio) {
            $idExercicio = (string) $exercicio['id_exercicio'];
            if ($exercicio['tipo'] === 'simulacao') {
                $resposta = $respostasSimulacao[$idExercicio] ?? null;
                $acoes = is_array($resposta)
                    ? json_decode((string) ($resposta['acoes'] ?? ''), true)
                    : null;
                $sequencia = array_column($exercicio['conteudo']['sequencia'] ?? [], 'acao');
                if (!is_array($acoes) || $acoes === [] || $sequencia === []) {
                    $this->view($this->getViewRealizacao($exercicios), [
                        'atividade' => $atividade,
                        'exercicios' => $exercicios,
                        'respostas' => $respostas,
                        'respostasSimulacao' => $respostasSimulacao,
                        'erro' => 'Conclua todas as ações da simulação antes de finalizar.',
                        'tempoRestante' => $this->calcularTempoRestante($atividade, $idAtividade),
                    ]);
                    return;
                }

                $indiceEsperado = 0;
                foreach ($acoes as $acao) {
                    $total++;
                    if (is_string($acao) && ($sequencia[$indiceEsperado] ?? null) === $acao) {
                        $acertos++;
                        $indiceEsperado++;
                    }
                }
                $total += count($sequencia) - $indiceEsperado;
                if ($indiceEsperado !== count($sequencia)) {
                    $this->view($this->getViewRealizacao($exercicios), [
                        'atividade' => $atividade,
                        'exercicios' => $exercicios,
                        'respostas' => $respostas,
                        'respostasSimulacao' => $respostasSimulacao,
                        'erro' => 'A sequência de manutenção ainda não foi concluída.',
                        'tempoRestante' => $this->calcularTempoRestante($atividade, $idAtividade),
                    ]);
                    return;
                }
                continue;
            }

            foreach ($exercicio['conteudo']['questoes'] as $indice => $item) {
                $total++;
                $resposta = filter_var(
                    $respostas[$idExercicio][$indice] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 0]]
                );
                if ($resposta === false || !array_key_exists($resposta, $item['alternativas'] ?? [])) {
                    $this->view($this->getViewRealizacao($exercicios), [
                        'atividade' => $atividade,
                        'exercicios' => $exercicios,
                        'respostas' => $respostas,
                        'respostasSimulacao' => $respostasSimulacao,
                        'erro' => 'Responda todas as questões antes de finalizar.',
                        'tempoRestante' => $this->calcularTempoRestante($atividade, $idAtividade),
                    ]);
                    return;
                }
                if ((int) $resposta === (int) ($item['alternativa_correta'] ?? -1)) {
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
        $concluida = $pontuacao >= 66;
        $estrelas = $concluida
            ? max(0, $atividade->getEstrelasRecompensa() - $erros - ($tempoExecucao > $atividade->getTempoLimite() ? 1 : 0))
            : 0;
        $bolotas = $concluida ? $atividade->getBolotasRecompensa() : 0;
        unset($_SESSION['atividade_inicio'][$idAtividade]);

        $registro = $this->funcionarioAtividadeService->registrarResultado(
            $_SESSION['usuario_logado']->getId(),
            $idAtividade,
            $pontuacao,
            $estrelas,
            $bolotas,
            $tempoExecucao
        );

        $this->view($this->getViewRealizacao($exercicios), [
            'atividade' => $atividade,
            'exercicios' => $exercicios,
            'respostas' => $respostas,
            'respostasSimulacao' => $respostasSimulacao,
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

    //Calcula quantos segundos ainda restam no limite da atividade.
    //O início fica armazenado na sessão para que voltar ao conteúdo e abrir os exercícios novamente não reinicie o cronômetro da tentativa atual.

    private function calcularTempoRestante(Atividade $atividade, int $idAtividade): int {
        $inicio = (float) ($_SESSION['atividade_inicio'][$idAtividade] ?? microtime(true));
        $tempoDecorrido = microtime(true) - $inicio;
        return max(0, (int) ceil($atividade->getTempoLimite() - $tempoDecorrido));
    }


    //Monta a estrutura de exercícios consumida pela view do funcionário.
    //Busca os registros da atividade e converte cada JSON para a estrutura de exercício ou simulação.

    private function montarExerciciosDaAtividade(int $idAtividade): array {
        $exercicios = [];
        foreach ($this->exercicioService->getExerciciosByAtividade($idAtividade) as $registro) {
            $conteudo = $this->decodificarConteudo($registro->getConteudo());
            if ($conteudo['tipo'] === 'simulacao') {
                $conteudo['sequencia'] = $this->montarSequenciaSimulacao($conteudo);
            }
            $exercicios[] = [
                'id_exercicio' => $registro->getId(),
                'descricao' => $registro->getDescricao(),
                'tipo' => $conteudo['tipo'],
                'conteudo' => $conteudo,
            ];
        }
        return $exercicios;
    }

    //Lista apenas atividades que aceitam exercícios no formulário administrativo.

    private function getAtividadesComExercicios(): array {
        return array_values(array_filter(
            $this->atividadeService->getAtividades(),
            static fn(Atividade $atividade): bool => $atividade->getTipoAtividade() !== 'TEXTO'
        ));
    }


    //Padroniza os campos enviados pelo formulário dinâmico antes de gerar o JSON.
    //Ignora entradas que não sejam arrays, remove espaços desnecessários, reorganiza os índices das alternativas e transforma o índice correto em inteiro válido.
    //A saída possui apenas enunciado, alternativas, alternativa correta e justificativa.

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

    private function normalizarEtapas(array $etapas): array {
        $normalizadas = [];

        foreach ($etapas as $etapa) {
            if (!is_array($etapa)) continue;
            $normalizadas[] = ['descricao' => trim((string) ($etapa['descricao'] ?? ''))];
        }

        return $normalizadas;
    }

    private function normalizarFerramentas(array $ferramentas): array {
        $permitidas = ['chave_fenda', 'pinca', 'pino_ejetor', 'luvas', 'aquecedor', 'palheta', 'adesivo'];
        $recebidas = array_filter($ferramentas, 'is_string');
        return array_values(array_unique(array_intersect($permitidas, $recebidas)));
    }

    private function normalizarTipo(string $tipo): string {
        return match (strtolower(trim($tipo))) {
            'q', 'questao', 'exercicio' => 'exercicio',
            's', 'simulacao' => 'simulacao',
            default => strtolower(trim($tipo)),
        };
    }

    //Recupera a peça defeituosa de JSONs antigos, nos quais ela ficava dentro de uma etapa.
    private function getPecaQuebradaLegada(array $etapas): string {
        foreach ($etapas as $etapa) {
            $pecas = is_array($etapa['pecas_quebradas'] ?? null) ? $etapa['pecas_quebradas'] : [];
            foreach ($pecas as $peca) {
                if (in_array($peca, ['bateria', 'camera', 'chip'], true)) return $peca;
            }
        }
        return '';
    }

    //Monta o roteiro técnico usado pelo JavaScript e novamente conferido pelo PHP na correção.
    private function montarSequenciaSimulacao(array $conteudo): array {
        $peca = (string) ($conteudo['peca_quebrada'] ?? '');
        $ferramentas = $conteudo['ferramentas'] ?? [];
        $sequencia = [];
        $adicionarFerramenta = static function (array &$acoes, string $ferramenta, string $rotulo) use ($ferramentas): void {
            if (in_array($ferramenta, $ferramentas, true)) {
                $acoes[] = ['acao' => 'ferramenta:' . $ferramenta, 'instrucao' => 'Use ' . $rotulo . '.'];
            }
        };

        $adicionarFerramenta($sequencia, 'luvas', 'as luvas de proteção');
        $adicionarFerramenta($sequencia, 'aquecedor', 'o soprador térmico para amolecer a cola da tampa');
        $adicionarFerramenta($sequencia, 'palheta', 'a palheta para soltar a tampa');
        $sequencia[] = ['acao' => 'remover:tampa', 'instrucao' => 'Remova a tampa traseira.'];
        $adicionarFerramenta($sequencia, 'chave_fenda', 'a chave de fenda nos parafusos da proteção');
        $sequencia[] = ['acao' => 'remover:protecao', 'instrucao' => 'Remova a proteção interna.'];

        if ($peca === 'chip') {
            $adicionarFerramenta($sequencia, 'pino_ejetor', 'o pino ejetor para liberar o chip');
        } else {
            $adicionarFerramenta($sequencia, 'pinca', 'a pinça para retirar o componente com segurança');
        }
        $sequencia[] = ['acao' => 'remover:' . $peca, 'instrucao' => 'Retire a peça com defeito.'];
        if ($peca === 'bateria') {
            $adicionarFerramenta($sequencia, 'adesivo', 'o adesivo para fixar a bateria nova');
        }
        $sequencia[] = ['acao' => 'instalar:' . $peca, 'instrucao' => 'Instale a peça nova.'];
        $sequencia[] = ['acao' => 'instalar:protecao', 'instrucao' => 'Recoloque a proteção interna.'];
        $adicionarFerramenta($sequencia, 'chave_fenda', 'a chave de fenda para prender novamente a proteção');
        $sequencia[] = ['acao' => 'instalar:tampa', 'instrucao' => 'Recoloque a tampa traseira.'];
        return $sequencia;
    }

    private function getViewRealizacao(array $exercicios): string {
        if ($exercicios !== [] && array_reduce(
            $exercicios,
            static fn(bool $somenteSimulacoes, array $exercicio): bool => $somenteSimulacoes && $exercicio['tipo'] === 'simulacao',
            true
        )) {
            return 'funcionario/simulacao';
        }
        return 'funcionario/exercicios';
    }

    private function tipoCompativelComAtividade(int $idAtividade, string $tipo, ?int $idIgnorado = null): bool {
        foreach ($this->exercicioService->getExerciciosByAtividade($idAtividade) as $exercicio) {
            if ($idIgnorado !== null && $exercicio->getId() === $idIgnorado) continue;
            $conteudo = $this->decodificarConteudo($exercicio->getConteudo());
            if ($conteudo['tipo'] !== $tipo) return false;
        }
        return true;
    }

    //Se o conteúdo não formar uma lista válida, devolve um array vazio para que a camada de apresentação não tente percorrer um valor incompatível.

    private function decodificarConteudo(string $conteudo): array {
        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) return ['tipo' => 'exercicio', 'questoes' => []];

        if (!array_is_list($dados)) {
            $tipo = $this->normalizarTipo((string) ($dados['tipo'] ?? 'exercicio'));
            if ($tipo === 'simulacao') {
                $etapas = is_array($dados['etapas'] ?? null) ? array_values($dados['etapas']) : [];
                $pecaQuebrada = (string) ($dados['peca_quebrada'] ?? $this->getPecaQuebradaLegada($etapas));
                return [
                    'tipo' => 'simulacao',
                    'etapas' => array_map(
                        static fn($etapa): array => ['descricao' => is_array($etapa) ? (string) ($etapa['descricao'] ?? '') : ''],
                        $etapas
                    ),
                    'ferramentas' => $this->normalizarFerramentas(is_array($dados['ferramentas'] ?? null) ? $dados['ferramentas'] : []),
                    'peca_quebrada' => $pecaQuebrada,
                    'componentes' => array_values(array_intersect(
                        ['bateria', 'camera', 'chip'],
                        is_array($dados['componentes'] ?? null) ? $dados['componentes'] : ['bateria', 'camera', 'chip']
                    )),
                ];
            }
            return [
                'tipo' => 'exercicio',
                'questoes' => is_array($dados['questoes'] ?? null) ? array_values($dados['questoes']) : [],
            ];
        }

        $tipoLegado = (($dados[0]['tipo'] ?? 'q') === 's') ? 'simulacao' : 'exercicio';
        if ($tipoLegado === 'simulacao') {
            return [
                'tipo' => 'simulacao',
                'etapas' => array_map(
                    static fn($item, int $indice): array => [
                        'descricao' => is_array($item) ? ($item['descricao'] ?? '') : '',
                    ],
                    $dados,
                    array_keys($dados)
                ),
                'ferramentas' => ['luvas', 'aquecedor', 'palheta', 'chave_fenda', 'pinca', 'adesivo'],
                'peca_quebrada' => $this->getPecaQuebradaLegada($dados),
                'componentes' => ['bateria', 'camera', 'chip'],
            ];
        }

        return ['tipo' => 'exercicio', 'questoes' => $dados];
    }

}
