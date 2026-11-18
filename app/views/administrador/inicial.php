<?php
$dadosEmpresas = $empresasComQuantidadeUsuarios ?? [];
$nomesEmpresas = array_column($dadosEmpresas, 'nome');
$quantidadesGestores = array_map('intval', array_column($dadosEmpresas, 'quantidade_gestores'));
$quantidadesFuncionarios = array_map('intval', array_column($dadosEmpresas, 'quantidade_funcionarios'));
$larguraGraficoEmpresas = max(720, count($dadosEmpresas) * 92);
$jsonSeguro = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/inicialAdministrador.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>LudoSkill - Administrador</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">

            <h1>Área do Administrador</h1>
            <p class="mensagem">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_logado']->getNomeCompleto()) ?>!</p>

            <section class="infobase">
                <div class="card">
                    <h3><i class="bi bi-briefcase-fill"></i><a href="<?= URL_BASE ?>/administrador/empresas"> Empresas assinantes</a></h3>
                    <p><?= $quantidadeEmpresas ?></p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-person-badge-fill"></i><a href="<?= URL_BASE ?>/administrador/gestores/">Gestores cadastrados</a></h3>
                    <p><?= $quantidadeGestores ?></p>
                </div>

                <div class="card modulo">

                    <div class="curso">
                        <h3><i class="bi bi-file-post"></i><a href="<?= URL_BASE ?>/administrador/modulos/">Módulos cadastrados:</a></h3>
                        <p><?= $quantidadeModulos ?></p>
                    </div>

                    <div class="curso">
                        <h3><a href="<?= URL_BASE ?>/administrador/atividades/">Atividades cadastradas:</a></h3>
                        <p></p><?= $quantidadeAtividades ?></p>
                    </div>

                    <div class="curso">
                        <h3><a href="<?= URL_BASE ?>/administrador/exercicios/">Exercícios cadastrados:</a></h3>
                        <p></p><?= $quantidadeExercicios ?></p>
                    </div>

                </div>

                <div class="card">
                    <h3><i class="bi bi-tags-fill"></i><a href="<?= URL_BASE ?>/administrador/itens/">Itens cadastrados</a></h3>
                    <p><?= $quantidadeItens ?></p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-envelope-paper-fill"></i><a href="<?= URL_BASE ?>/administrador/solicitacoes-acesso"> Solicitações de acesso</a></h3>
                    <p><?= $quantidadeSolicitacoes ?></p>
                </div>
            </section>

            <section class="grafico">
                <div id="grafico" class="card">
                    <h2><i class="bi bi-graph-up"></i> Usuários por empresa</h2>
                    <p class="grafico-sub">Gestores e funcionários cadastrados em cada empresa, da maior para a menor.</p>
                    <?php if (empty($empresasComQuantidadeUsuarios)): ?>
                        <p>Nenhuma empresa cadastrada.</p>
                    <?php else: ?>
                        <div class="chart-scroll-vertical" tabindex="0" aria-label="Área rolável do gráfico" style="max-height: 520px; overflow-y: auto;">
                            <div class="chart-container" style="height: <?= count($dadosEmpresas) * 56 + 90 ?>px">
                                <canvas id="usuariosPorEmpresa" role="img" aria-label="Gráfico da quantidade de gestores e funcionários por empresa"></canvas>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="legenda" class="card">
                    <h3><i class="bi bi-clipboard-data-fill"></i> Empresas com mais usuários</h3>
                    <?php if (empty($dadosEmpresas)): ?>
                        <p>Nenhuma empresa cadastrada.</p>
                    <?php else: ?>
                        <ol class="ranking-top-tres">
                            <?php foreach (array_slice($dadosEmpresas, 0, 3) as $empresa): ?>
                                <li class="card-secundario">
                                    <?= htmlspecialchars($empresa['nome']) ?>
                                    (<?= (int) $empresa['quantidade_usuarios'] ?>)
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <?php if (count($dadosEmpresas) > 3): ?>
                            <button type="button" id="alternarRanking" class="botao brilho" aria-expanded="false" aria-controls="rankingCompleto">Ver mais</button>
                            <div id="rankingCompleto" class="ranking-restante" hidden>
                                <ol start="4">
                                    <?php foreach (array_slice($dadosEmpresas, 3) as $empresa): ?>
                                        <li class="card-secundario">
                                            <?= htmlspecialchars($empresa['nome']) ?>
                                            (<?= (int) $empresa['quantidade_usuarios'] ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    <script>
        const canvasEmpresas = document.getElementById('usuariosPorEmpresa');
        if (canvasEmpresas) {
            const corTema = (nome, padrao) =>
                getComputedStyle(document.documentElement).getPropertyValue(nome).trim() || padrao;

            // Junta os dados e ordena pelo total de usuários (maior primeiro)
            const empresas = <?= json_encode($nomesEmpresas, $jsonSeguro) ?>.map((nome, i) => {
                const gestores = <?= json_encode($quantidadesGestores) ?>[i];
                const funcionarios = <?= json_encode($quantidadesFuncionarios) ?>[i];
                return {
                    nome,
                    gestores,
                    funcionarios,
                    total: gestores + funcionarios
                };
            }).sort((a, b) => b.total - a.total);

            // Escreve o total ("12 usuários") no fim de cada barra
            const rotulosTotal = {
                id: 'rotulosTotal',
                afterDatasetsDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    const ultima = chart.getDatasetMeta(chart.data.datasets.length - 1);
                    ctx.save();
                    ctx.font = '600 13px system-ui, sans-serif';
                    ctx.fillStyle = corTema('--texto-principal', '#fff');
                    ctx.textBaseline = 'middle';
                    ultima.data.forEach((barra, i) => {
                        const t = empresas[i].total;
                        ctx.fillText(`${t} usuário${t === 1 ? '' : 's'}`, barra.x + 8, barra.y);
                    });
                    ctx.restore();
                }
            };

            const graficoEmpresas = new Chart(canvasEmpresas, {
                type: 'bar',
                data: {
                    labels: empresas.map(e => e.nome),
                    datasets: [{
                            label: 'Gestores',
                            data: empresas.map(e => e.gestores),
                            backgroundColor: corTema('--grafico-verde', '#00c466'),
                            maxBarThickness: 26,
                            borderColor: corTema('--superficie', '#171122'),
                            borderWidth: {
                                right: 2
                            }
                        },
                        {
                            label: 'Funcionários',
                            data: empresas.map(e => e.funcionarios),
                            backgroundColor: corTema('--grafico-roxo', '#a900d6'),
                            maxBarThickness: 26,
                            borderRadius: {
                                topRight: 6,
                                bottomRight: 6
                            },
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            right: 110
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: corTema('--texto-suave', '#aaa')
                            },
                            grid: {
                                color: corTema('--borda-interface', 'rgba(255,255,255,.12)')
                            },
                            title: {
                                display: true,
                                text: 'Quantidade de usuários',
                                color: corTema('--texto-suave', '#aaa')
                            }
                        },
                        y: {
                            stacked: true,
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
                            position: 'top',
                            align: 'start',
                            labels: {
                                color: corTema('--texto-secundario', '#ddd'),
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 18
                            }
                        },
                        tooltip: {
                            callbacks: {
                                footer: (itens) => `Total: ${empresas[itens[0].dataIndex].total} usuário(s)`
                            }
                        }
                    }
                },
                plugins: [rotulosTotal]
            });

            // Atualiza as cores quando o tema muda
            window.addEventListener('ludoskill:tema-alterado', () => {
                const g = graficoEmpresas;
                g.data.datasets[0].backgroundColor = corTema('--grafico-verde', '#00c466');
                g.data.datasets[0].borderColor = corTema('--superficie', '#171122');
                g.data.datasets[1].backgroundColor = corTema('--grafico-roxo', '#a900d6');
                g.options.scales.y.ticks.color = corTema('--texto-principal', '#fff');
                g.options.scales.x.ticks.color = corTema('--texto-suave', '#aaa');
                g.options.scales.x.title.color = corTema('--texto-suave', '#aaa');
                g.options.scales.x.grid.color = corTema('--borda-interface', 'rgba(255,255,255,.12)');
                g.options.plugins.legend.labels.color = corTema('--texto-secundario', '#ddd');
                g.update();
            });
        }

        const botaoRanking = document.getElementById('alternarRanking');
        const rankingCompleto = document.getElementById('rankingCompleto');
        botaoRanking?.addEventListener('click', () => {
            const abrir = rankingCompleto.hidden;
            rankingCompleto.hidden = !abrir;
            botaoRanking.setAttribute('aria-expanded', String(abrir));
            botaoRanking.textContent = abrir ? 'Ver menos' : 'Ver mais';
        });
    </script>

</body>

</html>