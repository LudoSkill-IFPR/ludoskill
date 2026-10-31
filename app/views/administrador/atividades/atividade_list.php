<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Atividades</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/administrador/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="<?= URL_BASE ?>/administrador/atividades/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Nova Atividade</a>

                <div>
                    <h1>Listagem de Atividade</h1>
                    <p class="mensagem">Visualize as etapas da trilha de aprendizado.</p>
                </div>
            </div>
    
            <section class="card">
                <h2>Lista de Atividades</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo da atividade</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Estado</th>
                            <th>Ordem</th>
                            <th>Tempo limite</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $atividade): ?>
                        <tr>
                            <td><?= $atividade->getId() ?></td>
                            <td><?= htmlspecialchars($atividade->getTipoAtividade()) ?></td>
                            <td><?= htmlspecialchars($atividade->getNome()) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth(strip_tags($atividade->getDescricao()), 0, 90, '…')) ?></td>
                            <td><?= htmlspecialchars($atividade->getEstado()) ?></td>
                            <td><?= $atividade->getOrdem() ?></td>
                            <td><?= $atividade->getTempoLimite() ?> s</td>
                            <td id="acao">
                                <a href="<?= URL_BASE ?>/administrador/atividades/editar?id=<?= $atividade->getId() ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                <form action="<?= URL_BASE ?>/administrador/atividades/excluir" method="post" onsubmit="return confirm('Deseja desativar esta atividade?')">
                                    <input type="hidden" name="id" value="<?= $atividade->getId() ?>">
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
