<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <title>LudoSkill</title>
</head>
<body>
    <header>
        <nav>
            <a href="<?= URL_BASE ?>/" class="botao-voltar" aria-label="Voltar para a página inicial">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <!--volta para a pagina inicial-->
            <a id="homepage" href="<?= URL_BASE ?>/" aria-label="Ir para a página inicial">
                <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="icone do ludoskill">
                <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="logo do ludoskill">
            </a>

            <!--decide o tema do site-->
            <div id="controle-tema">
                <label for="seletor-tema">Tema</label>
                <select name="tema" id="seletor-tema" class="seletor" data-seletor-tema>
                    <option value="padrao">Padrão</option>
                    <option value="escuro">Escuro</option>
                    <option value="claro">Claro</option>
                </select>
            </div>
        </nav>
        <script src="<?= URL_BASE ?>/assets/javascript/temas.js" data-home-url="<?= URL_BASE ?>/"></script>
    </header>

    <main>
        <div class="card">
            <form action="<?= URL_BASE ?>/logar" method="post">
                <img id="skillo" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="">
                <h1>Bem vindo(a)!</h1>
            
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
                    <div class="senha-container">
                        <input class="botao" type="password" name="senha" id="senha" placeholder="euAMObolotas123!" autocomplete="current-password" required>
                        <button type="button" id="toggleSenha" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button class="botao brilho" type="submit">Entrar</button>

            
                <a href="<?= URL_BASE ?>/esqueci-senha" class="senha">Esqueceu a senha?</a> <!--Adicionar redirecionamento pra envio de email de recuperação -->
            
            </form>
        </div>
    </main>

    <script>

        const toggleSenhaButton = document.getElementById('toggleSenha')
        const senhaInput = document.getElementById('senha')

        toggleSenhaButton.addEventListener('click', function () {
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text'
                toggleSenhaButton.innerHTML = '<i class="bi bi-eye-slash"></i>'
            } else {
                senhaInput.type = 'password'
                toggleSenhaButton.innerHTML = '<i class="bi bi-eye"></i>'
            }
        })

    </script>
</body>
</html>
