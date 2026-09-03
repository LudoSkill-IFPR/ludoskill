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
    <link rel="stylesheet" href="<?= $caminho?>assets/css/404.css">
    <title>Lusdokils</title>
</head>
<body>
    <header>
        <h1>Erro <b>404</b></h1>
    </header>
    <main>
        <div>
            <img src="<?= $caminho?>assets/imagens/404.jpeg" alt="">
        </div>
        <div>
            <div>
                <h2>Ops, parece que a página que procura não existe.</h2>
                <p>Verifique a digitação do link ou volte para o menu inicial</p>
            </div>
            <a href="<?= URL_BASE?>"><button>Voltar</button></a>
        </div>
    </main>
</body>
</html>