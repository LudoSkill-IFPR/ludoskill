<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Funcionários</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuGestor.php"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/gestor/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="<?= URL_BASE ?>/gestor/funcionarios/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Novo Funcionário</a>

                <div>
                    <h1>Listagem de Funcionários</h1>
                    <p class="mensagem">Visualize os aprendizes cadastrados.</p>
                </div>
            </div>
    
            <section class="card">
                <h2>Lista de Funcionários</h2>
                <table id="funcionario">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data de Nascimento</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Numero de Telefone</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $funcionario): ?>
                        <tr>
                            <td><?= htmlspecialchars($funcionario->getNomeCompleto()) ?></td>
                            <td><?= htmlspecialchars($funcionario->getDataNascimento()?->format('d/m/Y') ?? '') ?></td>
                            <td><?= htmlspecialchars($funcionario->getCpf()) ?></td>
                            <td><?= htmlspecialchars($funcionario->getEmail()) ?></td>
                            <td><?= htmlspecialchars($funcionario->getNumeroTelefone() ?? '') ?></td>
                            <td>
                                <?php if ($funcionario->isAtivo()): ?>
                                    <span class="badge badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                    
                            <td id="acao">
                                <a href="<?= URL_BASE ?>/gestor/funcionarios/editar?id=<?= $funcionario->getIdFuncionario() ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                <?php if ($funcionario->isAtivo()): ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/desativar" method="post" onsubmit="return confirm('Deseja desativar este funcionário?')">
                                        <input type="hidden" name="id" value="<?= $funcionario->getIdFuncionario() ?>">
                                        <button type="submit" class="botao brilhinho botao-desativar"><i class="bi bi-person-x"></i> Desativar</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/ativar" method="post" onsubmit="return confirm('Deseja reativar este funcionário?')">
                                        <input type="hidden" name="id" value="<?= $funcionario->getIdFuncionario() ?>">
                                        <button type="submit" class="botao brilho botao-ativar"><i class="bi bi-person-check"></i> Ativar</button>
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
