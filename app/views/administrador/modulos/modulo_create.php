<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">

    <title>LudoSkill - Cadastrar Módulo</title>
</head>
<body>
<!-- implementar aqui uma atenticacao para que apenas administrador possam acessar a pagina de cadastro de modulos -->

    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/administrador/modulos/" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Cadastro de Módulos</h1>
                    <p class="mensagem">Adicione uma nova etapa na trilha de aprendizado.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/administrador/modulos/salvar" method="post">
                <div class="formgroup">
                    <label for="nome">Nome:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= isset($modulo['nome']) ? htmlspecialchars($modulo['nome']) : '' ?>">
                    <?php if (isset($erros['nome'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['nome']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao">Descrição:</label>
                    <textarea class="card-secundario" id="descricao" name="descricao"><?= isset($modulo['descricao']) ? htmlspecialchars($modulo['descricao']) : '' ?></textarea>
                    <?php if (isset($erros['descricao'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['descricao']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="formgroup">
                    <label for="min_estrelas_liberacao">Mínimo de Estrelas para Liberação:</label>
                    <input class="card-secundario" type="number" id="min_estrelas_liberacao" name="min_estrelas_liberacao" value="<?= isset($modulo['min_estrelas_liberacao']) ? htmlspecialchars($modulo['min_estrelas_liberacao']) : '' ?>">
                    <?php if (isset($erros['min_estrelas_liberacao'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['min_estrelas_liberacao']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="formgroup"><label for="estado">Estado:</label><select class="card-secundario" id="estado" name="estado"><option value="ATIVO" <?= ($modulo['estado'] ?? 'ATIVO') === 'ATIVO' ? 'selected' : '' ?>>Ativo</option><option value="INATIVO" <?= ($modulo['estado'] ?? '') === 'INATIVO' ? 'selected' : '' ?>>Inativo</option></select></div>
                
                <button type="submit" class="botao brilho">Salvar</button>
            
            </form>
        </div> 
    </main>
</body>
</html>
