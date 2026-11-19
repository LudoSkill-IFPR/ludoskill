<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/cadastroNaoAutorizado.css">

    <title>Cadastro não autorizado</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>
    <div class="barra-topo-voltar">
        <a href="<?= URL_BASE ?>/login" class="botao-voltar" aria-label="Voltar para o login">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <h1>Cadastro não liberado</h1>

        <p>Seu cadastro ainda não foi resgistrado no sistema, ou foi desativado pelo gestor.</p>

        <a class="botao brilho" href="<?= URL_BASE ?>/login">Volte para o login</a>
    </div>

    <script src="<?= URL_BASE ?>/assets/javascript/temas.js" data-home-url="<?= URL_BASE ?>/"></script>
</body>

</html>