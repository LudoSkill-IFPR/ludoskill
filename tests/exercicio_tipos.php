<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\helpers\Validador;
use app\controllers\ExercicioController;

$questao = [
    'tipo' => 'exercicio',
    'descricao' => 'Conjunto de questões',
    'atividade_id' => 1,
    'conteudo' => json_encode([
        'tipo' => 'exercicio',
        'questoes' => [[
            'enunciado' => 'Qual é a resposta?',
            'alternativas' => ['A', 'B'],
            'alternativa_correta' => 0,
            'justificativa' => 'A alternativa A é a correta.',
        ]],
    ]),
];

$simulacao = [
    'tipo' => 'simulacao',
    'descricao' => 'Troca de bateria',
    'atividade_id' => 1,
    'conteudo' => json_encode([
        'tipo' => 'simulacao',
        'etapas' => [[
            'descricao' => 'Aqueça e remova a tampa traseira.',
        ]],
        'ferramentas' => ['luvas', 'aquecedor', 'palheta', 'chave_fenda', 'pinca', 'adesivo'],
        'peca_quebrada' => 'bateria',
        'componentes' => ['bateria', 'camera', 'chip'],
    ]),
];

if (Validador::validarExercicio($questao) !== []) {
    throw new RuntimeException('Uma questão válida foi rejeitada.');
}

if (Validador::validarExercicio($simulacao) !== []) {
    throw new RuntimeException('Uma simulação válida foi rejeitada.');
}

$simulacaoInvalida = $simulacao;
$simulacaoInvalida['conteudo'] = json_encode([
    'tipo' => 'simulacao',
    'etapas' => [[
        'descricao' => 'Aqueça e remova a tampa traseira.',
    ]],
    'ferramentas' => ['luvas'],
    'peca_quebrada' => 'bateria',
    'componentes' => ['bateria', 'camera', 'chip'],
]);

if (!isset(Validador::validarExercicio($simulacaoInvalida)['conteudo'])) {
    throw new RuntimeException('Uma simulação sem as ferramentas necessárias foi aceita.');
}

$reflection = new ReflectionClass(ExercicioController::class);
$controller = $reflection->newInstanceWithoutConstructor();
$decodificar = $reflection->getMethod('decodificarConteudo');

$legadoQuestao = $decodificar->invoke($controller, json_encode([[
    'enunciado' => 'Questão antiga',
    'alternativas' => ['A', 'B'],
    'alternativa_correta' => 0,
    'justificativa' => 'Justificativa',
]]));
$legadoSimulacao = $decodificar->invoke($controller, json_encode([[
    'tipo' => 's',
    'descricao' => 'Simulação antiga',
    'pecas_quebradas' => ['bateria'],
]]));

if (($legadoQuestao['tipo'] ?? null) !== 'exercicio') {
    throw new RuntimeException('JSON antigo de questões não foi reconhecido.');
}
if (($legadoSimulacao['tipo'] ?? null) !== 'simulacao') {
    throw new RuntimeException('JSON antigo de simulação não foi reconhecido.');
}
if (($legadoSimulacao['peca_quebrada'] ?? null) !== 'bateria') {
    throw new RuntimeException('A peça quebrada do JSON antigo não foi preservada.');
}

$montarSequencia = $reflection->getMethod('montarSequenciaSimulacao');
$sequenciaBateria = $montarSequencia->invoke($controller, json_decode($simulacao['conteudo'], true));
$acoesBateria = array_column($sequenciaBateria, 'acao');
$acoesEsperadas = [
    'ferramenta:luvas', 'ferramenta:aquecedor', 'ferramenta:palheta', 'remover:tampa',
    'ferramenta:chave_fenda', 'remover:protecao', 'ferramenta:pinca', 'remover:bateria',
    'ferramenta:adesivo', 'instalar:bateria', 'instalar:protecao', 'ferramenta:chave_fenda',
    'instalar:tampa',
];
if ($acoesBateria !== $acoesEsperadas) {
    throw new RuntimeException('A sequência prática para troca da bateria está incorreta.');
}

echo "Tipos de exercício validados.\n";
