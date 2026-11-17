<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\controllers\ExercicioController;
use app\helpers\Validador;

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
        'peca_quebrada' => 'bateria',
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
    'peca_quebrada' => 'tela',
]);
if (!isset(Validador::validarExercicio($simulacaoInvalida)['conteudo'])) {
    throw new RuntimeException('Uma simulação com peça inválida foi aceita.');
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
$originalSimulacao = $decodificar->invoke($controller, json_encode([
    'tipo' => 's',
    'componentes' => ['bateria' => true, 'camera' => false, 'chip' => true],
    'instrucoes' => ['Retire a câmera'],
]));

if (($legadoQuestao['tipo'] ?? null) !== 'exercicio') {
    throw new RuntimeException('JSON antigo de questões não foi reconhecido.');
}
if (($legadoSimulacao['peca_quebrada'] ?? null) !== 'bateria') {
    throw new RuntimeException('A peça quebrada do JSON legado não foi preservada.');
}
if (($originalSimulacao['peca_quebrada'] ?? null) !== 'camera') {
    throw new RuntimeException('O JSON da simulação original não foi convertido.');
}

$montarEtapas = $reflection->getMethod('montarEtapasSimulacao');
$etapas = $montarEtapas->invoke($controller, 'bateria');
if (count($etapas) !== 6 || !str_contains($etapas[2]['descricao'], 'álcool')) {
    throw new RuntimeException('As etapas fixas da troca de bateria estão incorretas.');
}

$avaliar = $reflection->getMethod('avaliarSimulacao');
$trocaBateria = [
    'ferramenta:aquecedor', 'interagir:tampa',
    'ferramenta:palheta', 'interagir:tampa',
    'ferramenta:alcool', 'interagir:bateria',
    'ferramenta:palheta', 'interagir:bateria',
    'instalar:bateria', 'instalar:tampa',
];
$resultadoBateria = $avaliar->invoke($controller, $trocaBateria, 'bateria');
if (!$resultadoBateria['concluida'] || $resultadoBateria['erros'] !== 0) {
    throw new RuntimeException('A troca correta da bateria não foi concluída.');
}

$trocaCameraComReparoExtra = [
    'ferramenta:aquecedor', 'interagir:tampa',
    'ferramenta:palheta', 'interagir:tampa',
    'ferramenta:pinca', 'interagir:camera', 'instalar:camera',
    'ferramenta:solda', 'interagir:chip',
    'ferramenta:pinca', 'interagir:chip', 'instalar:chip',
    'instalar:tampa',
];
$resultadoReparo = $avaliar->invoke($controller, $trocaCameraComReparoExtra, 'camera');
if (!$resultadoReparo['concluida'] || $resultadoReparo['erros'] !== 1) {
    throw new RuntimeException('A simulação não permitiu corrigir uma peça danificada por engano.');
}

echo "Tipos e regras da simulação validados.\n";
