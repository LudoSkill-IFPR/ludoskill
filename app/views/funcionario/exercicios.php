<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - <?= htmlspecialchars($atividade['nome']) ?></title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>
    <main>
        <div class="container pagina-exercicios">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/modulos">Voltar</a>
                <div>
                    <h1><?= htmlspecialchars($atividade['nome']) ?></h1>
                    <p class="mensagem">Responda às questões da atividade.</p>
                </div>
            </div>

            <?php if (empty($exercicios)): ?>
                <div class="card"><p>Nenhum exercício foi cadastrado nesta atividade.</p></div>
            <?php else: ?>
                <?php $numeroGlobal = 0; ?>
                <?php foreach ($exercicios as $exercicio): ?>
                    <section class="card conjunto-exercicios">
                        <h2><?= htmlspecialchars($exercicio['descricao']) ?></h2>
                        <?php foreach ($exercicio['questoes'] as $questao): ?>
                            <?php
                            $numeroGlobal++;
                            $alternativas = is_array($questao['alternativas'] ?? null) ? $questao['alternativas'] : [];
                            $correta = (int) ($questao['alternativa_correta'] ?? -1);
                            ?>
                            <article class="card-secundario questao-funcionario" data-correta="<?= $correta ?>">
                                <h3><?= $numeroGlobal ?>. <?= htmlspecialchars($questao['questao'] ?? '') ?></h3>
                                <p><?= nl2br(htmlspecialchars($questao['enunciado'] ?? '')) ?></p>
                                <div class="opcoes-questao">
                                    <?php foreach ($alternativas as $indice => $alternativa): ?>
                                        <label class="opcao-exercicio">
                                            <input type="radio" name="resposta-<?= $numeroGlobal ?>" value="<?= (int) $indice ?>">
                                            <span><?= htmlspecialchars($alternativa) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="botao brilho verificar-resposta">Verificar resposta</button>
                                <div class="resultado-questao" hidden>
                                    <strong class="status-resposta"></strong>
                                    <p><?= nl2br(htmlspecialchars($questao['justificativa'] ?? '')) ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= URL_BASE ?>/assets/javascript/exerciciosFuncionario.js"></script>
</body>
</html>
