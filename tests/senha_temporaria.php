<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\controllers\SolicitacaoAcessoController;

$reflexao = new ReflectionClass(SolicitacaoAcessoController::class);
$controller = $reflexao->newInstanceWithoutConstructor();
$metodo = $reflexao->getMethod('gerarSenhaTemporaria');

for ($i = 0; $i < 20; $i++) {
    $senha = $metodo->invoke($controller);
    if (strlen($senha) !== 12
        || !preg_match('/[A-Z]/', $senha)
        || !preg_match('/[a-z]/', $senha)
        || !preg_match('/\d/', $senha)
        || !preg_match('/[!@#$%&*?]/', $senha)) {
        throw new RuntimeException('A senha temporária não atende aos requisitos de segurança.');
    }
}

echo "Geração de senha temporária verificada.\n";
