<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/inicialAdministrador.css">

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

                    <h3>
                    <i class="bi bi-file-post"></i>

                    <a href="<?= URL_BASE ?>/administrador/modulos/">
                        Módulos cadastrados
                    </a>
                    <p><?= $quantidadeModulos ?></p>
                    <a href="<?= URL_BASE ?>/administrador/atividades/">
                        Atividades cadastradas <!--quanidadaeAtividades-->
                    </a>
                </h3>

                </div>

                <div class="card">
                    <h3><i class="bi bi-tags-fill"></i><a href="<?= URL_BASE ?>/administrador/itens/">Itens cadastrados</a></h3>
                    <p><?= $quantidadeItens ?></p>
                </div>
            </section>

            <section class="grafico">
                <div id="grafico" class="card verde">
                    <h2><i class="bi bi-graph-up"></i> Usuários por empresa</h2>
                    <?php if (empty($empresasComQuantidadeUsuarios)): ?>
                        <p>Nenhuma empresa cadastrada.</p>
                    <?php else: ?>
                        <?php foreach ($empresasComQuantidadeUsuarios as $empresa): ?>
                            <p class="card-secundario">
                                <strong><?= htmlspecialchars($empresa['nome']) ?></strong>:
                                <?= (int) $empresa['quantidade_usuarios'] ?> usuários
                                (<?= (int) $empresa['quantidade_gestores'] ?> gestores e
                                <?= (int) $empresa['quantidade_funcionarios'] ?> funcionários)
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Aqui vai vir uma espécie de legenda para o gráfico -->
                <div id="legenda" class="card">
                    <h3><i class="bi bi-clipboard-data-fill"></i> Empresas com mais usuários</h3>

                    <ol>
                        <?php foreach (array_slice($empresasComQuantidadeUsuarios ?? [], 0, 5) as $empresa): ?>
                            <li class="card-secundario">
                                <?= htmlspecialchars($empresa['nome']) ?>
                                (<?= (int) $empresa['quantidade_usuarios'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        </div>
    </main>

</body>
</html>
