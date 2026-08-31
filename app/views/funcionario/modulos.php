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
                        <details class="card modulo-funcionario">
                            <summary>
                                <span><strong><?= htmlspecialchars($modulo['nome']) ?></strong><small><?= htmlspecialchars($modulo['descricao'] ?? '') ?></small></span>
                                <span class="abrir-modulo">Ver atividades</span>
                            </summary>
                            <div class="atividades-modulo">
                                <?php if (empty($modulo['atividades'])): ?>
                                    <p>Nenhuma atividade cadastrada neste módulo.</p>
                                <?php else: ?>
                                    <?php foreach ($modulo['atividades'] as $atividade): ?>
                                        <?php $resultado = $resultados[(int) $atividade['id_atividade']] ?? null; ?>
                                        <article class="card-secundario atividade-modulo">
                                            <div>
                                                <h2><?= htmlspecialchars($atividade['nome']) ?></h2>
                                                <p><?= htmlspecialchars($atividade['tipo_atividade']) ?></p>
                                                <?php if ($resultado): ?>
                                                    <p class="atividade-concluida">Melhor resultado: <?= (int) $resultado['pontuacao_obtida'] ?> pontos · <?= (int) $resultado['estrelas_obtidas'] ?> estrelas · <?= (int) $resultado['qtd_tentativas'] ?> tentativa(s)</p>
                                                <?php endif; ?>
                                            </div>
                                            <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/atividades/exercicios?id=<?= (int) $atividade['id_atividade'] ?>"><?= $resultado ? 'Refazer exercício' : 'Realizar exercício' ?></a>
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
