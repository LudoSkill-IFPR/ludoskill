<?php

$raiz = dirname(__DIR__);
$rotas = file_get_contents($raiz . '/public/index.php');

$cruds = [
    'módulos' => [
        'base' => '/administrador/modulos',
        'view' => '/app/views/administrador/modulos/modulo_list.php',
        'repository' => '/app/repositories/ModuloRepository.php',
        'service' => '/app/services/ModuloService.php',
        'controller' => '/app/controllers/ModuloController.php',
        'sufixo' => 'Modulo',
    ],
    'atividades' => [
        'base' => '/administrador/atividades',
        'view' => '/app/views/administrador/atividades/atividade_list.php',
        'repository' => '/app/repositories/AtividadeRepository.php',
        'service' => '/app/services/AtividadeService.php',
        'controller' => '/app/controllers/AtividadeController.php',
        'sufixo' => 'Atividade',
    ],
    'itens' => [
        'base' => '/administrador/itens',
        'view' => '/app/views/administrador/itens/item_list.php',
        'repository' => '/app/repositories/ItemRepository.php',
        'service' => '/app/services/ItemService.php',
        'controller' => '/app/controllers/ItemController.php',
        'sufixo' => 'Item',
    ],
    'gestores' => [
        'base' => '/administrador/gestores',
        'view' => '/app/views/administrador/gestores/gestor_list.php',
        'repository' => '/app/repositories/GestorRepository.php',
        'service' => '/app/services/GestorService.php',
        'controller' => '/app/controllers/GestorController.php',
        'sufixo' => 'Gestor',
    ],
    'funcionários' => [
        'base' => '/gestor/funcionarios',
        'view' => '/app/views/gestor/funcionarios/funcionario_list.php',
        'repository' => '/app/repositories/FuncionarioRepository.php',
        'service' => '/app/services/FuncionarioService.php',
        'controller' => '/app/controllers/FuncionarioController.php',
        'sufixo' => 'Funcionario',
    ],
];

foreach ($cruds as $nome => $arquivos) {
    $view = file_get_contents($raiz . $arquivos['view']);
    $repository = file_get_contents($raiz . $arquivos['repository']);
    $service = file_get_contents($raiz . $arquivos['service']);
    $controller = file_get_contents($raiz . $arquivos['controller']);

    foreach (['desativar', 'ativar'] as $acao) {
        $rota = $arquivos['base'] . '/' . $acao;
        if (!str_contains($rotas, "'{$rota}'") || !str_contains($view, $rota)) {
            throw new RuntimeException("O CRUD de {$nome} não possui rota e botão para {$acao}.");
        }

        $metodoEspecifico = $acao . $arquivos['sufixo'];
        if (!str_contains($repository, "function {$metodoEspecifico}")
            || !str_contains($service, "function {$metodoEspecifico}")
            || !str_contains($controller, "function {$acao}")) {
            throw new RuntimeException("O fluxo de {$acao} do CRUD de {$nome} está incompleto.");
        }
    }
}

echo "Ativação e desativação dos CRUDs verificadas.\n";
