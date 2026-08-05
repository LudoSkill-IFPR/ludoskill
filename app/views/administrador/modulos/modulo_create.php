<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Módulos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<!-- implementar aqui uma atenticacao para que apenas administradores possam acessar a pagina de cadastro de modulos -->
    <div class="container">
        <div>

            <h1 class="mt-5">Cadastro de Módulos</h1>
            <form action="<?= URL_BASE ?>/administradores/modulos/salvar" method="post">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= isset($modulo['nome']) ? htmlspecialchars($modulo['nome']) : '' ?>">
                    <?php if (isset($erros['nome'])): ?>
                        <div class="text-danger"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição:</label>
                    <textarea class="form-control" id="descricao" name="descricao"><?= isset($modulo['descricao']) ? htmlspecialchars($modulo['descricao']) : '' ?></textarea>
                    <?php if (isset($erros['descricao'])): ?>
                        <div class="text-danger"><?= $erros['descricao'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="min_estrelas_liberacao" class="form-label">Mínimo de Estrelas para Liberação:</label>
                    <input type="number" class="form-control" id="min_estrelas_liberacao" name="min_estrelas_liberacao" value="<?= isset($modulo['min_estrelas_liberacao']) ? htmlspecialchars($modulo['min_estrelas_liberacao']) : '' ?>">
                    <?php if (isset($erros['min_estrelas_liberacao'])): ?>
                        <div class="text-danger"><?= $erros['min_estrelas_liberacao'] ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
            
        </div>
    </div>
    
</body>
</html>


