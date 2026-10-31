<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - Módulos</title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>
    <main>
        <div class="container pagina-modulos">
            <h1>Módulos</h1>
            <p class="mensagem">Clique em um módulo para visualizar suas atividades.</p>
            <section class="lista-modulos">
                <?php if (empty($lista)): ?>
                    <div class="card"><p>Nenhum módulo cadastrado.</p></div>
                <?php else: ?>
                    <?php foreach ($lista as $modulo): ?>
                        <?php $moduloEntidade = $modulo['entidade']; ?>
                        <details class="card modulo-funcionario">
                            <summary>
                                <span><strong><?= htmlspecialchars($moduloEntidade->getNome()) ?></strong><small><?= htmlspecialchars($moduloEntidade->getDescricao()) ?></small></span>
                                <span class="abrir-modulo">Ver atividades</span>
                            </summary>
                            <div class="atividades-modulo">
                                <?php if (empty($modulo['atividades'])): ?>
                                    <p>Nenhuma atividade cadastrada neste módulo.</p>
                                <?php else: ?>
                                    <?php foreach ($modulo['atividades'] as $registroAtividade): ?>
                                        <?php $atividade = $registroAtividade['entidade']; $resultado = $resultados[$atividade->getId()] ?? null; ?>
                                        <article class="card-secundario atividade-modulo">
                                            <div>
                                                <h2><?= htmlspecialchars($atividade->getNome()) ?></h2>
                                                <!-- <p><?= htmlspecialchars($atividade->getTipoAtividade()) ?></p> -->
                                                <?php if ($resultado): ?>
                                                    <p class="atividade-concluida">Melhor resultado: <?= $resultado->getPontuacaoObtida() ?> pontos · <?= $resultado->getEstrelasObtidas() ?> estrelas · <?= $resultado->getQtdTentativas() ?> tentativa(s)</p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($registroAtividade['liberada']): ?>
                                                <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/atividades/conteudo?id=<?= $atividade->getId() ?>"><?= $resultado ? 'Rever atividade' : 'Ver atividade' ?></a>
                                            <?php else: ?><span class="botao">Bloqueada</span><?php endif; ?>
                                        </article>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
