<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-3">
        <a href="<?= URL_BASE ?>/administrador/empresas/cadastrar" class="btn btn-primary mb-3">
            <i class="bi bi-plus"></i> Nova Empresa
        </a>
    </div>

    <div class="container">
        <h2>Listagem de Empresas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Email</th>
                    <th>Plano</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($lista ?? []) as $empresa): ?>
                    <tr>
                        <td><?= htmlspecialchars($empresa['nome']) ?></td>
                        <td><?= htmlspecialchars($empresa['cnpj']) ?></td>
                        <td><?= htmlspecialchars($empresa['email']) ?></td>
                        <td><?= htmlspecialchars($empresa['plano']) ?></td>
                        <td class="px-4 py-3 align-middle text-end">
                            <a href="<?= URL_BASE ?>/administrador/empresas/editar?id=<?= $empresa['id_empresa'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="<?= URL_BASE ?>/administrador/empresas/excluir" method="post" class="d-inline" onsubmit="return confirm('Deseja excluir esta empresa?')">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($empresa['id_empresa'] ?? '') ?>">
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