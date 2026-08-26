<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../assets/css/inicialAdministrador.css">

    <title>LudoSkill - Administrador</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">

            <h1>Área do Administrador</h1>
            <p class="mensagem">Bem vindo, !</p>

            <section class="infobase">
                <div class="card">
                    <h3><i class="bi bi-briefcase-fill"></i> Empresas assinantes</h3>
                    <p>[numero de empresas no sistema]</p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-person-badge-fill"></i><a href="<?= URL_BASE ?>/administrador/gestores/">Gestores cadastrados</a></h3>
                    <p>[numero de gestores no sistema]</p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-file-post"></i><a href="<?= URL_BASE ?>/administrador/modulos/">Módulos cadastrados</a></h3>
                    <p>[quantidade de módulos no sistema]</p>
                </div>

                <div class="card">
                    <h3><i class="bi bi-tags-fill"></i><a href="<?= URL_BASE ?>/administrador/itens/">Itens cadastrados</a></h3>
                    <p>[quantidade de itens no sistema]</p>
                </div>
            </section>

            <section class="grafico">
                <div id="grafico" class="card verde">
                    <h2><i class="bi bi-graph-up"></i> Gestores por empresa</h2>
                    [gráfico virá aqui]
                </div>

                <!-- Aqui vai vir uma espécie de legenda para o gráfico -->
                <div id="legenda" class="card">
                    <h3><i class="bi bi-clipboard-data-fill"></i> Empresas mais presentes</h3>

                    <ol>
                        <li class="card-secundario">
                        </li>
                        <li class="card-secundario">
                        </li>
                        <li class="card-secundario">
                        </li>
                        <li class="card-secundario">
                        </li>
                        <li class="card-secundario">
                        </li>
                    </ol>
                </div>
            </section>
        </div>
    </main>

</body>
</html>
