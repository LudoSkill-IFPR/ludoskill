<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/login.css">

    <title>LudoSkill</title>
</head>
<body>
    <header>
        <nav>
            <!--volta para a pagina inicial-->
            <a id="homepage" href="<?= URL_BASE ?>" aria-label="Ir para a página inicial">
                <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="icone do ludoskill">
                <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="logo do ludoskill">
            </a>

            <!--decide o tema do site-->
            <div id="temas">
                <label for="temas">Temas</label>
                <select name="temas" id="temas" class="seletor">
                    <option hidden disabled value="padrao">Padrão</option>
                    <option value="escuro">Escuro</option>
                    <option value="claro">Claro</option>
                </select>
            </div>
        </nav>
    </header>

    <main>
        <div class="card">
            <form action="<?= URL_BASE ?>/logar" method="post">
                <img id="skillo" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="">
                <h1>Bem vindo de volta!</h1>
            
                <?php if (!empty($erro)): ?>
                    <div id="erro" role="alert"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                <?php if (!empty($sucesso)): ?>
                    <div class="alerta sucesso" role="status"><?= htmlspecialchars($sucesso) ?></div>
                <?php endif; ?>
            
                <div class="formgroup">
                    <label for="email">Email:</label>
                    <input class="botao" type="email" name="email" id="email" placeholder="skillo@bolotas.com" value="<?= htmlspecialchars($email ?? '') ?>" autocomplete="email" required>
                </div>
                <div class="formgroup">
                    <label for="senha">Senha:</label>
                    <input class="botao" type="password" name="senha" id="senha" placeholder="euAMObolotas123!" autocomplete="current-password" required>
                </div>

                <button class="botao brilho" type="submit">Entrar</button>
            
                <!-- <div class="formgroup">
                    <input class="lembrar" type="checkbox" name="lembrar">
                    <label class="lembrar" for="lembrar">Lembre de mim</label>
                </div> -->
            
                <a href="<?= URL_BASE ?>/esqueci-senha">Esqueceu a senha?</a> <!--Adicionar redirecionamento pra envio de email de recuperação -->
            
            </form>
        </div>
    </main>
</body>
</html>
