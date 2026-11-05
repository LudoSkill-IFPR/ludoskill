<?php
$dadosEmpresas = $empresasComQuantidadeUsuarios ?? [];
$nomesEmpresas = array_column($dadosEmpresas, 'nome');
$quantidadesGestores = array_map('intval', array_column($dadosEmpresas, 'quantidade_gestores'));
$quantidadesFuncionarios = array_map('intval', array_column($dadosEmpresas, 'quantidade_funcionarios'));
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

                <div class="card">
                    <h3><i class="bi bi-file-post"></i><a href="<?= URL_BASE ?>/administrador/modulos/">Módulos cadastrados</a></h3>
                    <p><?= $quantidadeModulos ?></p>

                    <h3><a href="<?= URL_BASE ?>/administrador/atividades/">Atividades cadastradas</a></h3>
                    <h3><a href="<?= URL_BASE ?>/administrador/exercicios/">Exercícios cadastrados</a></h3>

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
                <div id="grafico" class="card verde">
                    <h2><i class="bi bi-graph-up"></i> Usuários por empresa</h2>
                    <?php if (empty($empresasComQuantidadeUsuarios)): ?>
                        <p>Nenhuma empresa cadastrada.</p>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="usuariosPorEmpresa" role="img" aria-label="Gráfico da quantidade de gestores e funcionários por empresa"></canvas>
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
            new Chart(canvasEmpresas, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($nomesEmpresas, $jsonSeguro) ?>,
                    datasets: [
                        { label: 'Gestores', data: <?= json_encode($quantidadesGestores) ?>, backgroundColor: '#46d48f' },
                        { label: 'Funcionários', data: <?= json_encode($quantidadesFuncionarios) ?>, backgroundColor: '#c874df' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true, ticks: { color: '#ffffff' }, grid: { display: false } },
                        y: { stacked: true, beginAtZero: true, ticks: { color: '#ffffff', precision: 0 }, grid: { color: 'rgba(255,255,255,.15)' } }
                    },
                    plugins: { legend: { labels: { color: '#ffffff' } } }
                }
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
