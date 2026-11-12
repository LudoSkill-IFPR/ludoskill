<?php
    use app\repositories\EmpresaRepository;
    use app\repositories\UsuarioRepository;
    use app\models\Usuario;

    $empresaRepository = new EmpresaRepository();
    $usuarioRepository = new UsuarioRepository();
    $funcionarios = $data['lista'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Funcionarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-3">
        <a href="<?= URL_BASE ?>/gestor/funcionarios/cadastrar" class="btn btn-primary mb-3">
            <i class="bi bi-plus"></i> Novo Funcionario
        </a>
    </div>

    <div class="container">
        <h2>Listagem de Funcionarios</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Nascimento</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Numero de Telefone</th>
                    <th>Empresa</th>
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

                        <td class="px-4 py-3 align-middle text-end">
                            <a href="<?= URL_BASE ?>/gestor/funcionarios/editar?id=<?= $funcionario['id_funcionario'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="<?= URL_BASE ?>/gestor/funcionarios/excluir" method="post" class="d-inline" onsubmit="return confirm('Deseja excluir o(a) funcionario(a) <?= $usuario['nome_completo']?>?')">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>