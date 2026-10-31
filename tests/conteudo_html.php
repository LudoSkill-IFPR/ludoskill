<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\helpers\ConteudoHtmlHelper;

$entrada = '<div><h2 style="text-align: center; color: red">Título</h2><script>alert(1)</script><p onclick="alert(2)">Texto <strong>importante</strong> <span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: red">formatado</span> <span style="font-family: Comic Sans MS; font-size: 999px; background-image: url(javascript:alert(4))">inválido</span></p><a href="javascript:alert(3)">ruim</a><a href="https://example.com">bom</a></div>';
$saida = ConteudoHtmlHelper::sanitizar($entrada);

foreach (['<script', 'onclick', 'javascript:', 'color:', 'Comic Sans MS', '999px', 'background-image'] as $trechoProibido) {
    if (stripos($saida, $trechoProibido) !== false) {
        throw new RuntimeException("Conteúdo perigoso não removido: {$trechoProibido}");
    }
}

foreach (['<h2 style="text-align: center;">', '<strong>importante</strong>', '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px;">formatado</span>', 'https://example.com'] as $trechoEsperado) {
    if (!str_contains($saida, $trechoEsperado)) {
        throw new RuntimeException("Conteúdo permitido foi removido: {$trechoEsperado}");
    }
}

echo "Sanitização de HTML validada.\n";
