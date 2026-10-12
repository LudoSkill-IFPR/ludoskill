<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
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

                <input type="hidden" name="id" value="<?= $modulo->getId() ?>">

                <div class="formgroup">
                    <label for="nome">Nome:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= htmlspecialchars($modulo->getNome()) ?>">

                    <?php if (isset($erros['nome'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['nome']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao">Descrição:</label>
                    <textarea class="card-secundario" id="descricao" name="descricao"><?= htmlspecialchars($modulo->getDescricao()) ?></textarea>

                    <?php if (isset($erros['descricao'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['descricao']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="formgroup">
                    <label for="min_estrelas_liberacao">Mínimo de Estrelas para Liberação:</label>
                    <input class="card-secundario" type="number" id="min_estrelas_liberacao" name="min_estrelas_liberacao" value="<?= $modulo->getMinEstrelasLiberacao() ?>">

                    <?php if (isset($erros['min_estrelas_liberacao'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['min_estrelas_liberacao']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="formgroup"><label for="estado">Estado:</label><select class="card-secundario" id="estado" name="estado"><option value="ATIVO" <?= $modulo->getEstado() === 'ATIVO' ? 'selected' : '' ?>>Ativo</option><option value="INATIVO" <?= $modulo->getEstado() === 'INATIVO' ? 'selected' : '' ?>>Inativo</option></select></div>

                <button type="submit" class="botao brilho">Salvar</button>
            
            </form>
        </div> 
    </main>
</body>
</html>
