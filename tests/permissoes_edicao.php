<?php

$rotas = file_get_contents(__DIR__ . '/../public/index.php');
$rotasProibidas = [
    '/administrador/gestores/editar',
    '/administrador/gestores/atualizar',
    '/gestor/funcionarios/editar',
    '/gestor/funcionarios/atualizar',
    '/funcionario/editar',
    '/funcionario/atualizar',
];

foreach ($rotasProibidas as $rota) {
    if (str_contains($rotas, $rota)) {
        throw new RuntimeException("A rota de edição removida ainda está cadastrada: {$rota}");
    }
}

if (!str_contains($rotas, "'/perfil/editar'")) {
    throw new RuntimeException('A rota de autoedição do perfil não foi encontrada.');
}

if (!str_contains($rotas, "'/perfil/atualizar'")) {
    throw new RuntimeException('A rota de atualização do próprio perfil não foi encontrada.');
}

echo "Permissões de edição verificadas.\n";
