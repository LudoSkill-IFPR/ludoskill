<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">

    <title>Cadastro de Módulos</title>
</head>
<body>
<!-- implementar aqui uma atenticacao para que apenas administrador possam acessar a pagina de cadastro de modulos -->

    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            
        <h1 class="">Cadastro de Módulos</h1>
        <p class="mensagem">Adicione uma nova etapa na trilha de aprendizado.</p>

            <form class="card" action="" method="post">
                <div class="formgroup">
                    <label for="nome" class="form-label">Nome:</label>
                    <input class="card-secundario" type="text" class="form-control" id="nome" name="nome" value="<?= isset($modulo['nome']) ? htmlspecialchars($modulo['nome']) : '' ?>">
                    <?php if (isset($erros['nome'])): ?>
                        <div class="text-danger"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao" class="form-label">Descrição:</label>
                    <textarea class="card-secundario" id="descricao" name="descricao"><?= isset($modulo['descricao']) ? htmlspecialchars($modulo['descricao']) : '' ?></textarea>
                    <?php if (isset($erros['descricao'])): ?>
                        <div class="text-danger"><?= $erros['descricao'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="formgroup">
                    <label for="min_estrelas_liberacao" class="form-label">Mínimo de Estrelas para Liberação:</label>
                    <input class="card-secundario" type="number" id="min_estrelas_liberacao" name="min_estrelas_liberacao" value="<?= isset($modulo['min_estrelas_liberacao']) ? htmlspecialchars($modulo['min_estrelas_liberacao']) : '' ?>">
                    <?php if (isset($erros['min_estrelas_liberacao'])): ?>
                        <div class="text-danger"><?= $erros['min_estrelas_liberacao'] ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="botao brilho">Salvar</button>
            
            </form>
        </div> 
    </main>
</body>
</html>
