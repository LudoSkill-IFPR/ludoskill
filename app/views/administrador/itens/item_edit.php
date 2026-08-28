<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Editar Item</title>
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
                    <h1>Edição de Itens</h1>
                    <p class="mensagem">Edite uma recompensa.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/administrador/itens/atualizar" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id_item'] ?? '') ?>">
                <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($item['imagem'] ?? '') ?>">

                <div class="formgroup">
                    <label for="nome">Nome:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= isset($item['nome']) ? htmlspecialchars($item['nome']) : '' ?>">

                    <?php if (isset($erros['nome'])): ?>
                        <div class="erro"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="estado">Estado:</label>
                    <select class="card-secundario" name="estado" id="estado" >
                        <option value="1" <?= (!isset($item['estado']) || $item['estado'] === '1' || $item['estado'] === '') ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= isset($item['estado']) && $item['estado'] === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                    <?php if (isset($erros['estado'])): ?>
                        <div class="erro"><?= $erros['estado'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup"> <!--Aqui deve ser select-->
                    <label for="tipo" >Tipo:</label>
                    <select class="card-secundario" name="tipo" id="tipo">
                        <option value="">Selecione um tipo</option>
                        <option value="TEMA" <?= isset($item['tipo']) && $item['tipo'] === 'TEMA' ? 'selected' : '' ?>>Tema</option>
                        <option value="VISUAL_ESQUILOSO" <?= isset($item['tipo']) && $item['tipo'] === 'VISUAL_ESQUILOSO' ? 'selected' : '' ?>>Visual Esquilo</option>
                    </select>
                    <?php if (isset($erros['tipo'])): ?>
                        <div class="erro"><?= $erros['tipo'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="preco" class="form-label">Preço:</label>
                    <input class="card-secundario" type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?= isset($item['preco']) ? htmlspecialchars($item['preco']) : '' ?>">
                    <?php if (isset($erros['preco'])): ?>
                        <div class="erro"><?= $erros['preco'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup" id="campo-imagem">
                    <label for="imagem">Imagem:</label>
                    <?php
                        $imagemAtual = $item['imagem'] ?? '';
                        $imagemUrl = !empty($imagemAtual)
                            ? URL_BASE . '/' . ltrim($imagemAtual, '/')
                            : '';
                    ?>
                    <?php if (!empty($imagemUrl)): ?>

                    <div>
                        <img src="<?= htmlspecialchars($imagemUrl) ?>"
                            alt="<?= htmlspecialchars($item['nome'] ?? '') ?>"
                            style="max-width: 120px; max-height: 120px; display: block; margin-bottom: 0.5rem;">
                        <small>
                            Deixe em branco para manter a imagem atual.
                        </small>
                    </div>
                    <?php endif; ?>

                    <input class="card-secundario" type="file" id="imagem" name="imagem">
                    <?php if (isset($erros['imagem'])): ?>
                        <div class="erro"><?= $erros['imagem'] ?></div>
                    <?php endif; ?>
                </div>

                <div id="info-tema">
                    Tema usa a imagem fixa do sistema e não aceita upload.
                </div>
                <button type="submit" class="botao brilho">Atualizar</button>
            </form>
        </div>
    </main>

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
