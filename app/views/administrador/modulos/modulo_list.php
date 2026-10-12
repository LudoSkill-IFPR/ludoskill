<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Módulos</title>
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
                            <td><?= $modulo->getId() ?></td>
                            <td><?= htmlspecialchars($modulo->getNome()) ?></td>
                            <td><?= htmlspecialchars($modulo->getDescricao()) ?></td>
                            <td><?= $modulo->getMinEstrelasLiberacao() ?></td>
                            <td><?= htmlspecialchars($modulo->getEstado()) ?></td>
                            <td id="acao">
                                <a href="<?= URL_BASE ?>/administrador/modulos/editar?id=<?= $modulo->getId() ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                <form action="<?= URL_BASE ?>/administrador/modulos/excluir" method="post" onsubmit="return confirm('Deseja desativar este módulo?')">
                                    <input type="hidden" name="id" value="<?= $modulo->getId() ?>">
                                    <button type="submit" class="botao brilhinho"><i class="bi bi-x-circle"></i> Desativar</button>
                                </form>
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
