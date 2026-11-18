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
                <div class="card">
                    <h3>Taxa de conclusão</h3>
                    <p><?= number_format($taxaConclusao, 1, ',', '.') ?>%</p>
                </div>

            </section>

            <section class="grafico">
                <div id="grafico" class="card">
                    <h2><i class="bi bi-graph-up"></i> Desempenho dos Funcionários</h2>
                    <p class="grafico-sub">Média de estrelas por atividade concluída, do maior para o menor.</p>

                    <?php if (empty($desempenho)): ?>
                        <p>Nenhum funcionário ativo cadastrado.</p>
                    <?php else: ?>
                        <ul class="legenda-faixas">
                            <li><span style="background:#16a34a"></span>Bom desempenho</li>
                            <li><span style="background:#d97706"></span>Em evolução</li>
                            <li><span style="background:#dc2626"></span>Precisa de atenção</li>
                        </ul>
                        <div class="chart-scroll-vertical" tabindex="0" aria-label="Área rolável do gráfico">
                            <div class="chart-container" style="height: <?= count($desempenho) * 56 + 70 ?>px">
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
            const NOTA_MAXIMA = 5; // estrelas máximas por atividade (ajuste se for 3)

            // Junta os dados e ordena do maior para o menor
            const dados = <?= json_encode($nomesFuncionarios, $jsonSeguro) ?>.map((nome, i) => ({
                nome,
                media: <?= json_encode($mediasFuncionarios, JSON_PRESERVE_ZERO_FRACTION) ?>[i],
                concluidas: <?= json_encode($atividadesConcluidas) ?>[i]
            })).sort((a, b) => b.media - a.media);

            // Faixas de desempenho (cor + texto, nunca só a cor)
            const faixa = (media) => {
                const p = media / NOTA_MAXIMA;
                if (p >= 0.7) return {
                    cor: '#16a34a',
                    texto: 'Bom desempenho'
                };
                if (p >= 0.4) return {
                    cor: '#d97706',
                    texto: 'Em evolução'
                };
                return {
                    cor: '#dc2626',
                    texto: 'Precisa de atenção'
                };
            };

            const corTema = (nome, padrao) =>
                getComputedStyle(document.documentElement).getPropertyValue(nome).trim() || padrao;

            // Escreve "4,2 ★ · 8 atividades" no fim de cada barra
            const rotulos = {
                id: 'rotulos',
                afterDatasetsDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    ctx.save();
                    ctx.font = '600 13px system-ui, sans-serif';
                    ctx.fillStyle = corTema('--texto-principal', '#fff');
                    ctx.textBaseline = 'middle';
                    chart.getDatasetMeta(0).data.forEach((barra, i) => {
                        const d = dados[i];
                        const txt = `${d.media.toFixed(1).replace('.', ',')} ★ · ${d.concluidas} atividade${d.concluidas === 1 ? '' : 's'}`;
                        ctx.fillText(txt, barra.x + 8, barra.y);
                    });
                    ctx.restore();
                }
            };

            const grafico = new Chart(canvasDesempenho, {
                type: 'bar',
                data: {
                    labels: dados.map(d => d.nome),
                    datasets: [{
                        data: dados.map(d => d.media),
                        backgroundColor: dados.map(d => faixa(d.media).cor),
                        borderRadius: 6,
                        maxBarThickness: 26
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            right: 150
                        }
                    },
                    scales: {
                        x: {
                            min: 0,
                            max: NOTA_MAXIMA,
                            ticks: {
                                stepSize: 1,
                                color: corTema('--texto-suave', '#aaa')
                            },
                            grid: {
                                color: corTema('--borda-interface', 'rgba(255,255,255,.12)')
                            },
                            title: {
                                display: true,
                                text: 'Média de estrelas',
                                color: corTema('--texto-suave', '#aaa')
                            }
                        },
                        y: {
                            ticks: {
                                color: corTema('--texto-principal', '#fff'),
                                font: {
                                    size: 14,
                                    weight: '600'
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Média: ${dados[c.dataIndex].media.toFixed(1).replace('.', ',')} de ${NOTA_MAXIMA} estrelas`,
                                afterLabel: (c) => `${faixa(dados[c.dataIndex].media).texto} · ${dados[c.dataIndex].concluidas} atividade(s) concluída(s)`
                            }
                        }
                    }
                },
                plugins: [rotulos]
            });

            // Atualiza as cores do texto quando o tema muda
            window.addEventListener('ludoskill:tema-alterado', () => {
                grafico.options.scales.y.ticks.color = corTema('--texto-principal', '#fff');
                grafico.options.scales.x.ticks.color = corTema('--texto-suave', '#aaa');
                grafico.options.scales.x.title.color = corTema('--texto-suave', '#aaa');
                grafico.options.scales.x.grid.color = corTema('--borda-interface', 'rgba(255,255,255,.12)');
                grafico.update();
            });
        }
    </script>

</body>

</html>