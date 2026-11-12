<?php
$nomesFuncionarios = array_column($desempenho ?? [], 'nome_completo');
$mediasFuncionarios = array_map('floatval', array_column($desempenho ?? [], 'media_estrelas'));
$atividadesConcluidas = array_map('intval', array_column($desempenho ?? [], 'atividades_concluidas'));
$jsonSeguro = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
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
                    <?php if (!empty($desempenho)): ?>
                        <div class="chart-container">
                            <canvas id="desempenhoFuncionarios" role="img" aria-label="Gráfico de desempenho dos funcionários em ordem decrescente"></canvas>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    <script>
        const canvasDesempenho = document.getElementById('desempenhoFuncionarios');
        if (canvasDesempenho) {
            const atividadesConcluidas = <?= json_encode($atividadesConcluidas) ?>;
            new Chart(canvasDesempenho, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($nomesFuncionarios, $jsonSeguro) ?>,
                    datasets: [{
                        label: 'Média de estrelas',
                        data: <?= json_encode($mediasFuncionarios, JSON_PRESERVE_ZERO_FRACTION) ?>,
                        backgroundColor: '#00c466',
                        borderColor: '#64f5ad',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, max: 3, ticks: { color: '#ffffff', stepSize: 1 }, grid: { color: 'rgba(255,255,255,.15)' } },
                        y: { ticks: { color: '#ffffff' }, grid: { display: false } }
                    },
                    plugins: {
                        legend: { labels: { color: '#ffffff' } },
                        tooltip: { callbacks: { afterLabel: context => `Atividades concluídas: ${atividadesConcluidas[context.dataIndex]}` } }
                    }
                }
            });
        }
    </script>

</body>
</html>
