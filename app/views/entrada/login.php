<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <title>LudoSkill</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>
    <div id="chuva-bolotas" class="chuva-bolotas" aria-hidden="true"></div>

    <div id="easter-egg" class="easter-egg" hidden>
        <img src="<?= URL_BASE ?>/assets/imagens/EsquiloEasterEgg.png" alt="Esquilo do LudoSkill">
        <p>Você achou o esquilo das bolotas! 🐿️</p>
    </div>

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

        toggleSenhaButton.addEventListener('click', function() {
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text'
                toggleSenhaButton.innerHTML = '<i class="bi bi-eye-slash"></i>'
            } else {
                senhaInput.type = 'password'
                toggleSenhaButton.innerHTML = '<i class="bi bi-eye"></i>'
            }
        })

        // Chuva de bolotas pequenas na tela de login
        const containerChuva = document.getElementById('chuva-bolotas');
        if (containerChuva) {
            const totalBolotas = 35;
            for (let i = 0; i < totalBolotas; i++) {
                const bolota = document.createElement('span');
                bolota.className = 'bolota-caindo';
                const tamanho = Math.floor(Math.random() * 30) + 16; // tamanho pequeno: entre 16px e 32px
                const duracao = (Math.random() * 3 + 3.5).toFixed(2); // tempo de queda: entre 3.5s e 6.5s
                const atraso = (-Math.random() * 6.5).toFixed(2); // atraso negativo para já começar chovendo
                const posicaoX = (Math.random() * 96 + 2).toFixed(2); // posição horizontal (2% a 98%)
                const opacidade = (Math.random() * 0.4 + 0.6).toFixed(2); // opacidade sutil entre 0.6 e 1.0

                bolota.style.left = posicaoX + '%';
                bolota.style.width = tamanho + 'px';
                bolota.style.height = tamanho + 'px';
                bolota.style.animationDuration = duracao + 's';
                bolota.style.animationDelay = atraso + 's';
                bolota.style.opacity = opacidade;

                containerChuva.appendChild(bolota);
            }


        }

        // Easter egg: clicar numa bolota mostra o esquilo
        const easterEgg = document.getElementById('easter-egg');

        document.addEventListener('click', (evento) => {
            // Se o clique foi num botão, link ou campo, deixa funcionar normalmente
            if (evento.target.closest('a, button, input, select, label, .card')) return;
            if (!easterEgg.hidden) return;

            // Confere se o clique caiu em cima de alguma bolota (com 8px de folga, porque elas estão caindo)
            for (const bolota of containerChuva.children) {
                const area = bolota.getBoundingClientRect();
                const folga = 8;
                if (evento.clientX >= area.left - folga && evento.clientX <= area.right + folga &&
                    evento.clientY >= area.top - folga && evento.clientY <= area.bottom + folga) {
                    bolota.remove();
                    easterEgg.hidden = false;
                    return;
                }
            }
        });

        // Fecha clicando no esquilo ou apertando Esc
        easterEgg.addEventListener('click', (evento) => {
            evento.stopPropagation();
            easterEgg.hidden = true;
        });
        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape') easterEgg.hidden = true;
        });
    </script>
</body>

</html>