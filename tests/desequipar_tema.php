<?php

$repository = file_get_contents(__DIR__ . '/../app/repositories/InventarioRepository.php');
$service = file_get_contents(__DIR__ . '/../app/services/InventarioService.php');
$controller = file_get_contents(__DIR__ . '/../app/controllers/FuncionarioController.php');
$routes = file_get_contents(__DIR__ . '/../public/index.php');
$view = file_get_contents(__DIR__ . '/../app/views/funcionario/inicial.php');

$verificacoes = [
    'O repositório possui a operação de desequipar tema' => str_contains($repository, 'function desequiparTema'),
    'A operação aceita somente itens do tipo TEMA' => str_contains($repository, "it.tipo = 'TEMA'"),
    'A operação retira o estado em uso' => str_contains($repository, "SET i.em_uso = b'0'"),
    'O service encaminha a operação' => str_contains($service, 'desequiparTema($idUsuario, $idItem)'),
    'O controller remove o tema da sessão' => str_contains($controller, "unset(\$_SESSION['tema_equipado'])"),
    'A rota de desequipamento é POST' => str_contains($routes, "post('/funcionario/tema/desequipar'"),
    'A tela apresenta o botão Desequipar' => str_contains($view, '>Desequipar</button>'),
];

foreach ($verificacoes as $descricao => $resultado) {
    if (!$resultado) {
        fwrite(STDERR, "Falha: {$descricao}.\n");
        exit(1);
    }
}

echo "Fluxo de desequipar tema verificado com sucesso.\n";
