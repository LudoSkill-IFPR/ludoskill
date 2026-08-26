<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/inicialGestor.css">

    <title>LudoSkill - Gestor</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuFuncionario.php"); ?>
    </header>

    <main>
        <div class="container">

            <h1>Área do Gestor</h1>
            <p class="mensagem">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_logado']->getNomeCompleto()) ?>!</p>

            <section class="infobase">
                <div class="card">
                    <h3><i class="bi bi-people-fill"></i> Funcionários cadastrados</h3>
                    <p><?= $quantidadeFuncionarios ?></p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-building"></i> Empresa</h3>
                    <p><?= htmlspecialchars($empresa['nome_empresa'] ?? 'N/A') ?></p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-bar-chart-fill"></i> Dashboard</h3>
                    <p>Seus funcionários em ação</p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-gear-fill"></i> Configurações</h3>
                    <a href="<?= URL_BASE ?>/gestor/configuracoes">Gerenciar</a>
                </div>
            </section>

            <section class="grafico">
                <div id="grafico" class="card verde">
                    <h2><i class="bi bi-graph-up"></i> Desempenho dos Funcionários</h2>
                    <p>Gráfico de desempenho virá aqui</p>
                </div>

            </section>
        </div>
    </main>

</body>
</html>