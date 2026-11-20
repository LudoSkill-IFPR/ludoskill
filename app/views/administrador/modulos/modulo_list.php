<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">

    <title>LudoSkill - Listar Módulos</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>

    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/administrador/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="<?= URL_BASE ?>/administrador/modulos/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Novo Módulo</a>

                <div>
                    <h1>Listagem de Módulos</h1>
                    <p class="mensagem">Visualize as etapas da trilha de aprendizado.</p>
                </div>
            </div>

            <section class="card">
                <h2>Lista de Módulos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Mínimo de Estrelas para Liberação</th>
                            <th>Estado</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $modulo): ?>
                            <tr>
                                <td data-label="ID"><?= $modulo->getId() ?></td>
                                <td data-label="Nome"><?= htmlspecialchars($modulo->getNome()) ?></td>
                                <td data-label="Descrição"><?= htmlspecialchars($modulo->getDescricao()) ?></td>
                                <td data-label="Mínimo de estrelas"><?= $modulo->getMinEstrelasLiberacao() ?></td>
                                <td data-label="Estado"><?php if ($modulo->getEstado() === 'ATIVO'): ?>
                                    <span class="badge badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativo</span>
                                <?php endif; ?></td>
                                <td id="acao" data-label="Ações">
                                    <a href="<?= URL_BASE ?>/administrador/modulos/editar?id=<?= $modulo->getId() ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                    <?php if ($modulo->getEstado() === 'ATIVO'): ?>
                                        <form action="<?= URL_BASE ?>/administrador/modulos/desativar" method="post" onsubmit="return confirm('Deseja desativar este módulo?')">
                                            <input type="hidden" name="id" value="<?= $modulo->getId() ?>">
                                            <button type="submit" class="botao brilhinho botao-desativar"><i class="bi bi-x-circle"></i> Desativar</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= URL_BASE ?>/administrador/modulos/ativar" method="post" onsubmit="return confirm('Deseja reativar este módulo?')">
                                            <input type="hidden" name="id" value="<?= $modulo->getId() ?>">
                                            <button type="submit" class="botao brilho botao-ativar"><i class="bi bi-check-circle"></i> Ativar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </section>

        </div>
    </main>
</body>

</html>
