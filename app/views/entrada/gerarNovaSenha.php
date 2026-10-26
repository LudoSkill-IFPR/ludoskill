<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/login.css">
    <title>LudoSkill - Nova senha</title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuEntrada.php'; ?></header>
    <main>
        <div class="card">
            <?php if (empty($token)): ?>
                <div class="recuperacao-invalida">
                    <h1>Link indisponível</h1>
                    <div class="alerta erro" role="alert"><?= htmlspecialchars($erro ?? 'Este link é inválido ou expirou.') ?></div>
                    <a class="botao brilho" href="<?= URL_BASE ?>/esqueci-senha">Solicitar outro link</a>
                </div>
            <?php else: ?>
                <form action="<?= URL_BASE ?>/redefinir-senha" method="post">
                    <img id="skillo" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="">
                    <h1>Criar nova senha</h1>
                    <p class="texto-apoio">Conta: <?= htmlspecialchars($email ?? '') ?></p>
                    <?php if (!empty($erro)): ?><div class="alerta erro" role="alert"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="formgroup">
                        <label for="nova_senha">Nova senha:</label>
                        <input class="botao" type="password" name="nova_senha" id="nova_senha" minlength="8" autocomplete="new-password" required>
                    </div>
                    <div class="formgroup">
                        <label for="confirmar_senha">Confirmar senha:</label>
                        <input class="botao" type="password" name="confirmar_senha" id="confirmar_senha" minlength="8" autocomplete="new-password" required>
                    </div>
                    <button class="botao brilho" type="submit">Salvar nova senha</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
