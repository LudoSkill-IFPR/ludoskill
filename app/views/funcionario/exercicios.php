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
                <div><h1><?= htmlspecialchars($atividade['nome']) ?></h1><p class="mensagem">Responda todas as questões da atividade.</p></div>
            </div>

            <?php if (isset($erro)): ?><div class="erro erro-conteudo"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
            <?php if (isset($resultado)): ?>
                <section class="card resumo-resultado">
                    <h2>Resultado da atividade</h2>
                    <p><?= (int) $resultado['acertos'] ?> de <?= (int) $resultado['total'] ?> respostas corretas</p>
                    <p><?= (int) $resultado['pontuacao'] ?> pontos · <?= (int) $resultado['estrelas'] ?> estrelas · <?= (int) $resultado['bolotas'] ?> bolotas</p>
                    <p>Tentativa <?= (int) $resultado['tentativas'] ?> · <?= number_format((float) $resultado['tempo'], 2, ',', '.') ?> segundos</p>
                </section>
            <?php endif; ?>

            <?php if (empty($exercicios)): ?>
                <div class="card"><p>Nenhum exercício foi cadastrado nesta atividade.</p></div>
            <?php else: ?>
                <form action="<?= URL_BASE ?>/funcionario/atividades/exercicios/responder" method="post">
                    <input type="hidden" name="id_atividade" value="<?= (int) $atividade['id_atividade'] ?>">
                    <?php $numeroGlobal = 0; ?>
                    <?php foreach ($exercicios as $exercicio): ?>
                        <section class="card conjunto-exercicios">
                            <h2><?= htmlspecialchars($exercicio['descricao']) ?></h2>
                            <?php foreach ($exercicio['questoes'] as $indiceExercicio => $exercicio): ?>
                                <?php
                                $numeroGlobal++;
                                $idExercicio = (string) $indiceExercicio;
                                $selecionada = $respostas[$idExercicio][$indiceExercicio] ?? null;
                                $correta = (int) ($exercicio['alternativa_correta'] ?? -1);
                                ?>
                                <fieldset class="card-secundario exercicio-funcionario">
                                    <legend>Questão <?= $numeroGlobal ?></legend>
                                    <p><?= nl2br(htmlspecialchars($exercicio['enunciado'] ?? '')) ?></p>
                                    <div class="opcoes-exercicio">
                                        <?php foreach (($exercicio['alternativas'] ?? []) as $indice => $alternativa): ?>
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
                                            <p><?= nl2br(htmlspecialchars($exercicio['justificativa'] ?? '')) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </fieldset>
                            <?php endforeach; ?>
                        </section>
                    <?php endforeach; ?>
                    <?php if (!isset($resultado)): ?><button class="botao brilho finalizar-atividade" type="submit">Finalizar atividade</button><?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
