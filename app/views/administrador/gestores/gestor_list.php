<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>Gestores</title>
</head>

<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/inicial">Voltar</a><a class="botao brilho" href="<?= URL_BASE ?>/administrador/gestores/cadastrar">Novo gestor</a></div>
            <h1>Gestores</h1>
            <?php if (empty($lista)): ?><p>Nenhum gestor cadastrado.</p><?php else: ?><table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Empresa</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($lista as $gestor): ?><tr>
                                <td><?= htmlspecialchars($gestor['nome_completo']) ?></td>
                                <td><?= htmlspecialchars($gestor['email']) ?></td>
                                <td><?= htmlspecialchars($gestor['nome_empresa']) ?></td>
                                <td>
                                    
                                    <?php if (($gestor['estado'] ?? 'ATIVO') === 'ATIVO'): ?>
                                        <span class="badge badge-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div id="acao">
                                        <a class="botao brilho" href="<?= URL_BASE ?>/administrador/gestores/editar?id=<?= (int) $gestor['id_gestor'] ?>">Editar</a>
                                        <?php if (($gestor['estado'] ?? 'ATIVO') === 'ATIVO'): ?>
                                            <form method="post" action="<?= URL_BASE ?>/administrador/gestores/desativar" onsubmit="return confirm('Deseja desativar este gestor?')">
                                                <input type="hidden" name="id" value="<?= (int) $gestor['id_gestor'] ?>">
                                                <button class="botao brilhinho botao-desativar" type="submit">Desativar</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="<?= URL_BASE ?>/administrador/gestores/ativar" onsubmit="return confirm('Deseja reativar este gestor?')">
                                                <input type="hidden" name="id" value="<?= (int) $gestor['id_gestor'] ?>">
                                                <button class="botao brilho botao-ativar" type="submit">Ativar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr><?php endforeach; ?></tbody>
                </table><?php endif; ?>
        </div>
    </main>
</body>

</html>