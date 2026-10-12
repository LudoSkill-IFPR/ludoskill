<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/inicialGestor.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>LudoSkill - Gestor</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuGestor.php"); ?>
    </header>

    <main>
        <div class="container">

            <h1>Área do Gestor</h1>
            <p class="mensagem">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_logado']->getNomeCompleto()) ?>!</p>

            <section class="infobase">
                <div class="card">
                    <h3><i class="bi bi-people-fill"></i><a href="<?= URL_BASE ?>/gestor/funcionarios/">Funcionários cadastrados</a></h3>
                    <p><?= $quantidadeFuncionarios ?></p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-building"></i> Empresa</h3>
                    <p><?= htmlspecialchars($empresa?->getNome() ?? 'N/A') ?></p>
                </div>
                <div class="card"><h3>Taxa de conclusão</h3><p><?= number_format($taxaConclusao, 1, ',', '.') ?>%</p></div>

            </section>

            <section class="grafico">
                <div id="grafico" class="card verde">
                    <h2><i class="bi bi-graph-up"></i> Desempenho dos Funcionários</h2>
                    <?php if (empty($desempenho)): ?><p>Nenhum funcionário ativo cadastrado.</p><?php endif; ?>
                    <?php foreach ($desempenho as $key => $registro): ?>
                        <div class="funcionario">
                            <div class="nome">
                                <?= $key + 1 ?> - <?= htmlspecialchars($registro['nome_completo']) ?>
                                (<?= number_format((float) $registro['media_estrelas'], 1, ',', '.') ?> estrelas)
                            </div>
                            <div class="desempenho">
                                <div class="progresso" style="width: <?= min(100, ((float) $registro['media_estrelas'] / 3) * 100) ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </section>
        </div>
    </main>

</body>
</html>
