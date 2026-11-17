<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\helpers\Validador;

$atividade = [
    'nome' => 'Atividade de teste',
    'descricao' => '<p>Conteúdo completo da atividade.</p>',
    'tipo_atividade' => 'INTRODUCAO',
    'estado' => 'ATIVO',
    'ordem' => '1',
    'tempo_limite' => '180',
    'estrelas_recompensa' => '5',
    'bolotas_recompensa' => '10',
    'id_modulo' => '1',
];

$erros = Validador::validarAtividade($atividade);
if (isset($erros['estrelas_recompensa'])) {
    throw new RuntimeException('O limite válido de 5 estrelas foi rejeitado.');
}

$atividade['estrelas_recompensa'] = '6';
$erros = Validador::validarAtividade($atividade);
if (!isset($erros['estrelas_recompensa'])) {
    throw new RuntimeException('Uma recompensa acima de 5 estrelas foi aceita.');
}

echo "Limite de estrelas da atividade verificado.\n";
