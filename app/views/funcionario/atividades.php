<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - Atividades</title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>
    <main>
        <div class="container pagina-exercicios">
            <h1>Atividades</h1>
            <p class="mensagem">Escolha uma atividade para acessar seus exercícios.</p>
            <div class="lista-atividades-funcionario">
                <?php if (empty($lista)): ?>
                    <div class="card"><p>Nenhuma atividade cadastrada.</p></div>
                <?php else: ?>
                    <?php foreach ($lista as $atividade): ?>
                        <article class="card">
                            <h2><?= htmlspecialchars($atividade['nome']) ?></h2>
                            <p><?= htmlspecialchars($atividade['tipo_atividade']) ?></p>
                            <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/atividades/exercicios?id=<?= (int) $atividade['id_atividade'] ?>">Acessar exercícios</a>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
