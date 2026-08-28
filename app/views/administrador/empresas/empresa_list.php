<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Empresas</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main><div class="container">
        <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/inicial">Voltar</a><a class="botao brilho" href="<?= URL_BASE ?>/administrador/empresas/cadastrar">Nova empresa</a><div><h1>Listagem de Empresas</h1><p class="mensagem">Visualize as empresas cadastradas.</p></div></div>
        <?php if (!empty($erro)): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <section class="card"><h2>Lista de Empresas</h2>
            <?php if (empty($lista)): ?><p>Nenhuma empresa cadastrada.</p><?php else: ?>
                <table><thead><tr><th>Nome</th><th>CNPJ</th><th>E-mail</th><th>Plano</th><th>Ações</th></tr></thead><tbody>
                <?php foreach ($lista as $empresa): ?><tr><td><?= htmlspecialchars($empresa['nome']) ?></td><td><?= htmlspecialchars($empresa['cnpj']) ?></td><td><?= htmlspecialchars($empresa['email']) ?></td><td><?= htmlspecialchars($empresa['plano']) ?></td><td><div id="acao"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/empresas/editar?id=<?= (int) $empresa['id_empresa'] ?>">Editar</a><form action="<?= URL_BASE ?>/administrador/empresas/excluir" method="post" onsubmit="return confirm('Deseja excluir esta empresa?')"><input type="hidden" name="id" value="<?= (int) $empresa['id_empresa'] ?>"><button type="submit" class="botao brilhinho">Excluir</button></form></div></td></tr><?php endforeach; ?>
                </tbody></table>
            <?php endif; ?>
        </section>
    </div></main>
</body>
</html>
