<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Empresas</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao-voltar botao brilho" href="<?= URL_BASE ?>/administrador/inicial">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/empresas/cadastrar">Nova empresa</a>
                <div>
                    <h1>Listagem de Empresas</h1>
                    <p class="mensagem">Visualize as empresas cadastradas.</p>
                </div>
            </div>

            <?php if (!empty($erro)): ?>
                <span class="erro aviso-exclusao" role="alert"><?= htmlspecialchars($erro) ?></span>
            <?php endif; ?>

            <section class="card">
                <h2>Lista de Empresas</h2>
                <?php if (empty($lista)): ?>
                    <p>Nenhuma empresa cadastrada.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CNPJ</th>
                                <th>E-mail</th>
                                <th>Plano</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista as $empresa): ?>
                                <?php $aguardandoConfirmacao = isset($empresaConfirmacaoId) && (int) $empresaConfirmacaoId === $empresa->getId(); ?>
                                <tr>
                                    <td data-label="Nome"><?= htmlspecialchars($empresa->getNome()) ?></td>
                                    <td data-label="CNPJ"><?= htmlspecialchars($empresa->getCnpj()) ?></td>
                                    <td data-label="E-mail"><?= htmlspecialchars($empresa->getEmail()) ?></td>
                                    <td data-label="Plano"><?= htmlspecialchars($empresa->getPlano()) ?></td>
                                    <td data-label="Ações">
                                        <div id="acao">
                                            <a class="botao brilho" href="<?= URL_BASE ?>/administrador/empresas/editar?id=<?= $empresa->getId() ?>">Editar</a>
                                            <form
                                                action="<?= URL_BASE ?>/administrador/empresas/excluir"
                                                method="post"
                                                onsubmit="return confirm('<?= $aguardandoConfirmacao
                                                    ? 'Esta segunda confirmação excluirá a empresa, seus gestores, funcionários e todos os dados vinculados. Deseja continuar?'
                                                    : 'Deseja excluir esta empresa?' ?>')"
                                            >
                                                <input type="hidden" name="id" value="<?= $empresa->getId() ?>">
                                                <?php if ($aguardandoConfirmacao): ?>
                                                    <input type="hidden" name="confirmar_exclusao" value="1">
                                                    <button type="submit" class="botao brilho botao-desativar">Excluir empresa e usuários</button>
                                                <?php else: ?>
                                                    <button type="submit" class="botao brilhinho">Excluir</button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
