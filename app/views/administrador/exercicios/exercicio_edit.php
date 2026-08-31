<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - Editar Exercícios</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/exercicios">Voltar</a>
                <div>
                    <h1>Edição de Exercícios</h1>
                    <p class="mensagem">Edite as questões armazenadas no JSON.</p>
                </div>
            </div>
            <?php $acaoFormulario = URL_BASE . '/administrador/exercicios/atualizar'; ?>
            <?php include __DIR__ . '/_form.php'; ?>
        </div>
    </main>
</body>
</html>
