<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/login.css">
    <title>LudoSkill - Recuperar senha</title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuEntrada.php'; ?></header>
    <main>
        <div class="card">
            <form action="<?= URL_BASE ?>/recuperar-senha" method="post">
                <img id="skillo" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="">
                <h1>Recuperar senha</h1>
                <p class="texto-apoio">Informe seu e-mail para receber o link de recuperação.</p>
                <?php if (!empty($erro)): ?><div class="alerta erro" role="alert"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
                <?php if (!empty($sucesso)): ?><div class="alerta sucesso" role="status"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
                <div class="formgroup">
                    <label for="email">E-mail:</label>
                    <input class="botao" type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" autocomplete="email" required>
                </div>
                <button class="botao brilho" type="submit">Enviar link</button>
                <a href="<?= URL_BASE ?>/login">Voltar para o login</a>
            </form>
        </div>
    </main>
</body>
</html>
