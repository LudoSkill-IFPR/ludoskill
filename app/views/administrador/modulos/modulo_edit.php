<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Editar Módulo</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/administrador/modulos/" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Edição de Módulos</h1>
                    <p class="mensagem">Edite uma etapa da trilha de aprendizado.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/administrador/modulos/atualizar" method="post">

                <input type="hidden" name="id" value="<?= htmlspecialchars($modulo['id_modulo'] ?? '') ?>">

                <div class="formgroup">
                    <label for="nome">Nome:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= htmlspecialchars($modulo['nome'] ?? '') ?>">

                    <?php if (isset($erros['nome'])): ?>
                        <div class="erro"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao">Descrição:</label>
                    <textarea class="card-secundario" id="descricao" name="descricao"><?= htmlspecialchars($modulo['descricao'] ?? '') ?></textarea>

                    <?php if (isset($erros['descricao'])): ?>
                        <div class="erro"><?= $erros['descricao'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="formgroup">
                    <label for="min_estrelas_liberacao">Mínimo de Estrelas para Liberação:</label>
                    <input class="card-secundario" type="number" id="min_estrelas_liberacao" name="min_estrelas_liberacao" value="<?= htmlspecialchars($modulo['min_estrelas_liberacao'] ?? '') ?>">

                    <?php if (isset($erros['min_estrelas_liberacao'])): ?>
                        <div class="erro"><?= $erros['min_estrelas_liberacao'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="botao brilho">Salvar</button>
            
            </form>
        </div> 
    </main>
</body>
</html>
