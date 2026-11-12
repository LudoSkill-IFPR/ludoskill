<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - <?= htmlspecialchars($atividade->getNome()) ?></title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>
    <main>
        <div class="container pagina-exercicios">
            <div id="topo">
                <a class="botao brilho" id="voltar-conteudo" href="<?= URL_BASE ?>/funcionario/atividades/conteudo?id=<?= $atividade->getId() ?>">Voltar ao conteúdo</a>
                <div><h1><?= htmlspecialchars($atividade->getNome()) ?></h1><p class="mensagem">Responda todas as questões da atividade.</p></div>
            </div>

            <?php if (!isset($resultado)): ?>
                <?php $segundosRestantes = max(0, (int) $tempoRestante); ?>
                <section class="card cronometro-atividade" id="cronometro-atividade" role="timer" aria-live="polite">
                    <div>
                        <span id="rotulo-timer">Tempo restante</span>
                        <small id="mensagem-timer">O tempo continua contando ao voltar para o conteúdo.</small>
                    </div>
                    <strong id="valor-timer" data-segundos="<?= $segundosRestantes ?>"><?= sprintf('%02d:%02d', intdiv($segundosRestantes, 60), $segundosRestantes % 60) ?></strong>
                </section>
            <?php endif; ?>

            <?php if (isset($erro)): ?><div class="erro erro-conteudo"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
            <?php if (isset($resultado)): ?>
                <section class="card resumo-resultado">
                    <h2>Resultado da atividade</h2>
                    <p><?= (int) $resultado['acertos'] ?> de <?= (int) $resultado['total'] ?> respostas corretas</p>
                    <p><?= (int) $resultado['pontuacao'] ?> pontos · <?= (int) $resultado['estrelas'] ?> estrelas · <?= (int) $resultado['bolotas'] ?> bolotas registradas nesta atividade</p>
                    <p>Tentativa <?= (int) $resultado['tentativas'] ?> · <?= number_format((float) $resultado['tempo'], 2, ',', '.') ?> segundos</p>
                </section>
            <?php endif; ?>

            <?php if (empty($exercicios)): ?>
                <div class="card"><p>Nenhum exercício foi cadastrado nesta atividade.</p></div>
            <?php else: ?>
                <form action="<?= URL_BASE ?>/funcionario/atividades/exercicios/responder" method="post" id="form-atividade">
                    <input type="hidden" name="id_atividade" value="<?= $atividade->getId() ?>">
                    <?php $numeroGlobal = 0; ?>
                    <?php foreach ($exercicios as $exercicio): ?>
                        <section class="card conjunto-exercicios">
                            <h2><?= htmlspecialchars($exercicio['descricao']) ?></h2>
                            <?php foreach ($exercicio['questoes'] as $indiceExercicio => $questao): ?>
                                <?php
                                $numeroGlobal++;
                                $idExercicio = (string) $exercicio['id_exercicio'];
                                $selecionada = $respostas[$idExercicio][$indiceExercicio] ?? null;
                                $correta = (int) ($questao['alternativa_correta'] ?? -1);
                                ?>
                                <fieldset class="card-secundario exercicio-funcionario">
                                    <legend>Questão <?= $numeroGlobal ?></legend>
                                    <p><?= nl2br(htmlspecialchars($questao['enunciado'] ?? '')) ?></p>
                                    <div class="opcoes-exercicio">
                                        <?php foreach (($questao['alternativas'] ?? []) as $indice => $alternativa): ?>
                                            <?php
                                            $classes = 'opcao-exercicio';
                                            if (isset($resultado) && $indice === $correta) $classes .= ' alternativa-correta-resultado';
                                            if (isset($resultado) && (int) $selecionada === $indice && $indice !== $correta) $classes .= ' alternativa-incorreta-resultado';
                                            ?>
                                            <label class="<?= $classes ?>">
                                                <input type="radio" name="respostas[<?= (int) $idExercicio ?>][<?= (int) $indiceExercicio ?>]" value="<?= (int) $indice ?>" <?= (string) $selecionada === (string) $indice ? 'checked' : '' ?> <?= isset($resultado) ? 'disabled' : 'required' ?>>
                                                <span><?= htmlspecialchars($alternativa) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (isset($resultado)): ?>
                                        <div class="resultado-exercicio <?= (int) $selecionada === $correta ? 'resposta-correta' : 'resposta-incorreta' ?>">
                                            <strong><?= (int) $selecionada === $correta ? 'Resposta correta!' : 'Resposta incorreta.' ?></strong>
                                            <p><?= nl2br(htmlspecialchars($questao['justificativa'] ?? '')) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </fieldset>
                            <?php endforeach; ?>
                        </section>
                    <?php endforeach; ?>
                    <?php if (!isset($resultado)): ?><button class="botao brilho finalizar-atividade" type="submit">Corrigir respostas</button><?php endif; ?>
                </form>
                <?php if (isset($resultado)): ?>
                    <div class="acoes-pos-correcao">
                        <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/modulos">Concluir exercício</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
    (() => {
        const cronometro = document.getElementById('cronometro-atividade');
        const valorTimer = document.getElementById('valor-timer');
        if (cronometro && valorTimer) {
            const rotuloTimer = document.getElementById('rotulo-timer');
            const mensagemTimer = document.getElementById('mensagem-timer');
            const segundosIniciais = Math.max(0, Number.parseInt(valorTimer.dataset.segundos || '0', 10));
            const instanteFinal = Date.now() + (segundosIniciais * 1000);
            let intervalo;

            const atualizarTimer = () => {
                const segundos = Math.max(0, Math.ceil((instanteFinal - Date.now()) / 1000));
                const minutos = Math.floor(segundos / 60);
                const segundosDoMinuto = segundos % 60;
                valorTimer.textContent = `${String(minutos).padStart(2, '0')}:${String(segundosDoMinuto).padStart(2, '0')}`;

                cronometro.classList.toggle('timer-alerta', segundos > 0 && segundos <= 60);
                cronometro.classList.toggle('timer-esgotado', segundos === 0);

                if (segundos === 0) {
                    rotuloTimer.textContent = 'Tempo esgotado';
                    mensagemTimer.textContent = 'Você ainda pode enviar as respostas, mas o tempo será considerado no resultado.';
                    if (intervalo) window.clearInterval(intervalo);
                }
            };

            atualizarTimer();
            if (segundosIniciais > 0) intervalo = window.setInterval(atualizarTimer, 1000);
        }

        const form = document.getElementById('form-atividade');
        if (!form) return;
        const chave = 'ludoskill:atividade:<?= $_SESSION['usuario_logado']->getId() ?>:<?= $atividade->getId() ?>';
        <?php if (isset($resultado)): ?>localStorage.removeItem(chave);<?php else: ?>
        const salvas = JSON.parse(localStorage.getItem(chave) || '{}');
        form.querySelectorAll('input[type="radio"]').forEach(input => {
            if (salvas[input.name] === input.value) input.checked = true;
            input.addEventListener('change', () => {
                salvas[input.name] = input.value;
                localStorage.setItem(chave, JSON.stringify(salvas));
            });
        });
        window.addEventListener('beforeunload', event => {
            if (form.dataset.enviando !== '1') { event.preventDefault(); event.returnValue = ''; }
        });
        document.getElementById('voltar-conteudo')?.addEventListener('click', () => {
            form.dataset.enviando = '1';
        });
        form.addEventListener('submit', () => { form.dataset.enviando = '1'; });
        <?php endif; ?>
    })();
    </script>
</body>
</html>
