<?php
    use app\repositories\EmpresaRepository;
    use app\repositories\UsuarioRepository;
    use app\models\Usuario;

    $empresaRepository = new EmpresaRepository();
    $usuarioRepository = new UsuarioRepository();
    $funcionarios = $data['lista'];
?>

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
                <h2>Lista de Itens</h2>
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
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <?php
                            $nome_empresa = $empresaRepository->getEmpresaById($funcionario['id_empresa']);
                            $nome_empresa = $nome_empresa['nome'];
                            $usuario = $usuarioRepository->getUsuario($funcionario['id_usuario']);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nome_completo']) ?></td>
                            <td><?= htmlspecialchars($usuario['data_nascimento']) ?></td>
                            <td><?= htmlspecialchars($usuario['CPF']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['numero_telefone']) ?></td>
                            <td>
                                <?php if (($usuario['estado'] ?? 'ATIVO') === 'ATIVO'): ?>
                                    <span class="badge badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                    
                            <td id="acao">
                                <a href="<?= URL_BASE ?>/gestor/funcionarios/editar?id=<?= $funcionario['id_funcionario'] ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                
                                <form action="<?= URL_BASE ?>/gestor/funcionarios/excluir" method="post" onsubmit="return confirm('Deseja excluir o(a) funcionario(a) <?= $usuario['nome_completo']?>?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">
                                    <button type="submit" class="botao brilhinho"><i class="bi bi-trash"></i> Excluir</button>
                                </form>
                                <?php if (($usuario['estado'] ?? 'ATIVO') === 'ATIVO'): ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/desativar" method="post" onsubmit="return confirm('Deseja desativar o(a) funcionario(a) <?= htmlspecialchars($usuario['nome_completo']) ?>?')">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">
                                        <button type="submit" class="botao brilhinho botao-desativar"><i class="bi bi-person-x"></i> Desativar</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/ativar" method="post" onsubmit="return confirm('Deseja reativar o(a) funcionario(a) <?= htmlspecialchars($usuario['nome_completo']) ?>?')">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">
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
