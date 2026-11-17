<?php
$nomesFuncionarios = array_column($desempenho ?? [], 'nome_completo');
$mediasFuncionarios = array_map('floatval', array_column($desempenho ?? [], 'media_estrelas'));
$atividadesConcluidas = array_map('intval', array_column($desempenho ?? [], 'atividades_concluidas'));
$alturaGraficoFuncionarios = max(360, count($desempenho ?? []) * 54);
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
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
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
                        <div class="chart-scroll-vertical" tabindex="0" aria-label="Área rolável do gráfico">
                            <div class="chart-container" style="height: <?= $alturaGraficoFuncionarios ?>px">
                                <canvas id="desempenhoFuncionarios" role="img" aria-label="Gráfico de desempenho dos funcionários em ordem decrescente"></canvas>
                            </div>
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
            const cores = () => {
                const estilos = getComputedStyle(document.documentElement);
                return { texto: estilos.getPropertyValue('--texto-principal').trim() || '#fff', grade: estilos.getPropertyValue('--borda-interface').trim() || 'rgba(255,255,255,.15)', verde: estilos.getPropertyValue('--grafico-verde').trim() || '#00c466' };
            };
            const paleta = cores();
            const graficoDesempenho = new Chart(canvasDesempenho, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($nomesFuncionarios, $jsonSeguro) ?>,
                    datasets: [{
                        label: 'Desempenho médio (0 a 5)',
                        data: <?= json_encode($mediasFuncionarios, JSON_PRESERVE_ZERO_FRACTION) ?>,
                        backgroundColor: paleta.verde,
                        borderColor: paleta.verde,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, max: 5, ticks: { color: paleta.texto, stepSize: 1, font: { size: 13, weight: '600' } }, grid: { color: paleta.grade } },
                        y: { ticks: { color: paleta.texto, autoSkip: false, font: { size: 13, weight: '600' }, padding: 8 }, grid: { display: false } }
                    },
                    plugins: {
                        legend: { labels: { color: paleta.texto, font: { size: 14, weight: '600' }, usePointStyle: true } },
                        tooltip: { callbacks: { afterLabel: context => `Atividades concluídas: ${atividadesConcluidas[context.dataIndex]}` } }
                    }
                }
            });
            window.addEventListener('ludoskill:tema-alterado', () => {
                const nova = cores();
                graficoDesempenho.options.scales.x.ticks.color = nova.texto;
                graficoDesempenho.options.scales.x.grid.color = nova.grade;
                graficoDesempenho.options.scales.y.ticks.color = nova.texto;
                graficoDesempenho.options.plugins.legend.labels.color = nova.texto;
                graficoDesempenho.data.datasets[0].backgroundColor = nova.verde;
                graficoDesempenho.data.datasets[0].borderColor = nova.verde;
                graficoDesempenho.update();
            });
        }
    </script>

</body>
</html>
