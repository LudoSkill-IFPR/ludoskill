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
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Funcionários</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="/administrador/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="<?= URL_BASE ?>/gestor/funcionarios/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Novo Funcionário</a>

                <div>
                    <h1>Listagem de Funcionários</h1>
                    <p class="mensagem">Visualize os aprendizes cadastrados.</p>
                </div>
            </div>
    
            <section class="card">
                <h2>Lista de Itens</h2>
                <table id="funcionario">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data de Nascimento</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Numero de Telefone</th>
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
                    
                            <td id="acao">
                                <a href="<?= URL_BASE ?>/gestor/funcionarios/editar?id=<?= $funcionario['id_funcionario'] ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                
                                <form action="<?= URL_BASE ?>/gestor/funcionarios/excluir" method="post" onsubmit="return confirm('Deseja excluir o(a) funcionario(a) <?= $usuario['nome_completo']?>?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">
                                    <button type="submit" class="botao brilhinho"><i class="bi bi-trash"></i> Excluir</button>
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
