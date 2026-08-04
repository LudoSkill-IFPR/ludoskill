<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Módulos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Listagem de Módulos</h1>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Mínimo de Estrelas para Liberação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($lista ?? []) as $modulo): ?>
                    <tr>
                        <td><?= htmlspecialchars($modulo['id_modulo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($modulo['nome'] ?? '') ?></td>
                        <td><?= htmlspecialchars($modulo['descricao'] ?? '') ?></td>
                        <td><?= htmlspecialchars($modulo['min_estrelas_liberacao'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>
</html>