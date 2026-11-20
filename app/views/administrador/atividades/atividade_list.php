<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Atividades</title>
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
                            <th>Estrelas</th>
                            <th>Bolotas</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $atividade): ?>
                        <tr>
                            <td data-label="ID"><?= $atividade->getId() ?></td>
                            <td data-label="Tipo da atividade"><?= htmlspecialchars($atividade->getTipoAtividade()) ?></td>
                            <td data-label="Nome"><?= htmlspecialchars($atividade->getNome()) ?></td>
                            <td data-label="Descrição"><?= htmlspecialchars(mb_strimwidth(strip_tags($atividade->getDescricao()), 0, 90, '…')) ?></td>
                            <td data-label="Estado">
                                <?php if ($atividade->getEstado() === 'ATIVO'): ?>
                                    <span class="badge badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Ordem"><?= $atividade->getOrdem() ?></td>
                            <td data-label="Tempo limite"><?= $atividade->getTempoLimite() ?> s</td>
                            <td data-label="Estrelas"><?= $atividade->getEstrelasRecompensa() ?></td>
                            <td data-label="Bolotas"><?= $atividade->getBolotasRecompensa() ?></td>
                            <td id="acao" data-label="Ações">
                                <a href="<?= URL_BASE ?>/administrador/atividades/editar?id=<?= $atividade->getId() ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                <?php if ($atividade->getEstado() === 'ATIVO'): ?>
                                    <form action="<?= URL_BASE ?>/administrador/atividades/desativar" method="post" onsubmit="return confirm('Deseja desativar esta atividade?')">
                                        <input type="hidden" name="id" value="<?= $atividade->getId() ?>">
                                        <button type="submit" class="botao brilhinho botao-desativar"><i class="bi bi-x-circle"></i> Desativar</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= URL_BASE ?>/administrador/atividades/ativar" method="post" onsubmit="return confirm('Deseja reativar esta atividade?')">
                                        <input type="hidden" name="id" value="<?= $atividade->getId() ?>">
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
