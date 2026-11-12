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
        <div class="container pagina-conteudo-atividade">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/modulos">Voltar</a>
                <div>
                    <h1><?= htmlspecialchars($atividade->getNome()) ?></h1>
                    <!-- <p class="mensagem"><?= htmlspecialchars($atividade->getTipoAtividade()) ?></p> -->
                </div>
            </div>

            <article class="card conteudo-atividade">
                <?php if ($descricao === ''): ?>
                    <p>Nenhum conteúdo introdutório foi cadastrado para esta atividade.</p>
                <?php else: ?>
                    <?= $descricao ?>
                <?php endif; ?>
            </article>

            <div class="acoes-conteudo-atividade">
                <?php if ($atividadeTexto): ?>
                    <?php if ($concluida): ?>
                        <p class="atividade-concluida">Leitura concluída.</p>
                    <?php else: ?>
                        <form action="<?= URL_BASE ?>/funcionario/atividades/concluir-texto" method="post">
                            <input type="hidden" name="id_atividade" value="<?= $atividade->getId() ?>">
                            <button class="botao brilho" type="submit">Concluir leitura</button>
                        </form>
                    <?php endif; ?>
                <?php elseif ($temExercicios && $atividade->getTipoAtividade() !== 'TEXTO'): ?>
                    <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/atividades/exercicios?id=<?= $atividade->getId() ?>">Exercícios</a>
                <?php elseif ($atividade->getTipoAtividade() === 'TEXTO'): ?>
                    <p class="erro">Esta atividade textual possui exercícios vinculados e precisa ser corrigida pelo administrador.</p>
                <?php else: ?>
                    <p class="mensagem">Os exercícios desta atividade ainda não foram cadastrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
