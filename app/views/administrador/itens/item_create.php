<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Cadastrar Itens</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/administrador/itens/" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Cadastro de Itens</h1>
                    <p class="mensagem">Adicione uma nova recompensa.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/administrador/itens/salvar" method="post" enctype="multipart/form-data">
                <div class="formgroup">
                    <label for="nome">Nome:</label>
                    <input type="text" class="card-secundario" id="nome" name="nome" value="<?= isset($item['nome']) ? htmlspecialchars($item['nome']) : '' ?>">

                    <?php if (isset($erros['nome'])): ?>
                        <div class="erro"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="estado" >Estado:</label>
                    <select name="estado" id="estado" class="card-secundario">
                        <option value="1" <?= (!isset($item['estado']) || $item['estado'] === '1' || $item['estado'] === '') ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= isset($item['estado']) && $item['estado'] === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>

                    <?php if (isset($erros['estado'])): ?>
                        <div class="erro"><?= $erros['estado'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup"> <!--Aqui deve ser select-->
                    <label for="tipo" >Tipo:</label>
                    <select name="tipo" id="tipo" class="card-secundario">
                        <option value="">Selecione um tipo</option>
                        <option value="TEMA" <?= isset($item['tipo']) && $item['tipo'] === 'TEMA' ? 'selected' : '' ?>>Tema</option>
                        <option value="VISUAL_ESQUILOSO" <?= isset($item['tipo']) && $item['tipo'] === 'VISUAL_ESQUILOSO' ? 'selected' : '' ?>>Visual Esquilo</option>
                    </select>

                    <?php if (isset($erros['tipo'])): ?>
                        <div class="erro"><?= $erros['tipo'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="preco" >Preço:</label>
                    <input type="number" step="0.01" class="card-secundario" id="preco" name="preco" value="<?= isset($item['preco']) ? htmlspecialchars($item['preco']) : '' ?>">

                    <?php if (isset($erros['preco'])): ?>
                        <div class="erro"><?= $erros['preco'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup" id="campo-imagem">
                    <label for="imagem" >Imagem:</label>
                    <input type="file" class="card-secundario" id="imagem" name="imagem">

                    <?php if (isset($erros['imagem'])): ?>
                        <div class="erro"><?= $erros['imagem'] ?></div>
                    <?php endif; ?>
                </div>

                <div id="info-tema">
                    Tema usa a imagem fixa do sistema e não aceita upload.
                </div>

                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipo = document.getElementById('tipo');
            const campoImagem = document.getElementById('campo-imagem');
            const inputImagem = document.getElementById('imagem');
            const infoTema = document.getElementById('info-tema');

            function atualizarTipoItem() {
                const eTema = tipo.value === 'TEMA';
                campoImagem.style.display = eTema ? 'none' : 'block';
                inputImagem.disabled = eTema;
                infoTema.classList.toggle('d-none', !eTema);
                if (eTema) {
                    inputImagem.value = '';
                }
            }

            tipo.addEventListener('change', atualizarTipoItem);
            atualizarTipoItem();
        });
    </script>
</body>
</html>
