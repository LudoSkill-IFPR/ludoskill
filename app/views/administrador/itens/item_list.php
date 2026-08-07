<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Itens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-3">
        <a href="<?= URL_BASE ?>/administrador/itens/cadastrar" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Item
        </a>
    </div>

    <div class="container">
        <h1 class="mt-5">Listagem de Itens</h1>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($lista ?? []) as $item): ?>
                    <?php
                        $imagem = $item['imagem'] ?? '';
                        $imagemUrl = !empty($imagem)
                            ? URL_BASE . '/' . ltrim($imagem, '/')
                            : '';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id_item'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['nome'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['preco'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($imagemUrl)): ?>
                                <img src="<?= htmlspecialchars($imagemUrl) ?>" alt="<?= htmlspecialchars($item['nome'] ?? '') ?>" style="max-width: 100px; max-height: 100px;">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 align-middle text-end">
                            <a href="<?= URL_BASE ?>/administrador/itens/editar?id=<?= $item['id_item'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="<?= URL_BASE ?>/administrador/itens/excluir" method="post" class="d-inline" onsubmit="return confirm('Deseja excluir este item?')">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id_item'] ?? '') ?>">
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