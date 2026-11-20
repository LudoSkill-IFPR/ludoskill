<?php

$conteudo = file_get_contents(__DIR__ . '/../app/repositories/InventarioRepository.php');

if (!preg_match(
    '/INSERT\s+INTO\s+Inventarios\s*\([^)]*data_aquisicao[^)]*\)\s*VALUES\s*\([^)]*CURRENT_DATE[^)]*\)/is',
    $conteudo
)) {
    throw new RuntimeException('A compra voltou a depender do valor padrão de Inventarios.data_aquisicao.');
}

if (!str_contains($conteudo, 'beginTransaction()')
    || !str_contains($conteudo, 'rollBack()')
    || !str_contains($conteudo, 'commit()')) {
    throw new RuntimeException('A compra não está protegida por uma transação completa.');
}

echo "Compatibilidade da compra na loja verificada.\n";
