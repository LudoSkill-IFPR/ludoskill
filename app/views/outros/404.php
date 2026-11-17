<?php
    $num_base = substr_count(URL_BASE, '/');;
    $link = substr_count($_SERVER['REQUEST_URI'], '/');
    $voltas = $link - $num_base;
    $caminho = "";
    for ($i=0; $i <= $voltas; $i++) { 
        $caminho .= "../";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/404.css">
    <title>LudoSkill - Página não encontrada</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>
<body>
    <div class="barra-topo-voltar">
        <a class="botao-voltar botao brilho" href="<?= URL_BASE ?>/"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
    <header>
        <h1>Erro <b>404</b></h1>
    </header>
    <main>
        <div>
            <img src="<?= URL_BASE ?>/assets/imagens/404.jpeg" alt="Ilustração de página não encontrada">
        </div>
        <div>
            <div>
                <h2>Ops, parece que a página que procura não existe.</h2>
                <p>Verifique a digitação do link ou volte para o menu inicial</p>
            </div>
            <a class="botao brilho" href="<?= URL_BASE ?>/">Voltar ao início</a>
        </div>
    </main>
    <script src="<?= URL_BASE ?>/assets/javascript/temas.js" data-home-url="<?= URL_BASE ?>/"></script>
</body>
</html>
