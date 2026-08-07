<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <div>
            <h1 class="mt-5">Editar Item</h1>
            <form action="<?= URL_BASE ?>/administrador/itens/atualizar" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id_item'] ?? '') ?>">
                <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($item['imagem'] ?? '') ?>">

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= isset($item['nome']) ? htmlspecialchars($item['nome']) : '' ?>">
                    <?php if (isset($erros['nome'])): ?>
                        <div class="text-danger"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="1" <?= (!isset($item['estado']) || $item['estado'] === '1' || $item['estado'] === '') ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= isset($item['estado']) && $item['estado'] === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                    <?php if (isset($erros['estado'])): ?>
                        <div class="text-danger"><?= $erros['estado'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3"> <!--Aqui deve ser select-->
                    <label for="tipo" class="form-label">Tipo:</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="">Selecione um tipo</option>
                        <option value="TEMA" <?= isset($item['tipo']) && $item['tipo'] === 'TEMA' ? 'selected' : '' ?>>Tema</option>
                        <option value="VISUAL_ESQUILOSO" <?= isset($item['tipo']) && $item['tipo'] === 'VISUAL_ESQUILOSO' ? 'selected' : '' ?>>Visual Esquilo</option>
                    </select>
                    <?php if (isset($erros['tipo'])): ?>
                        <div class="text-danger"><?= $erros['tipo'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="preco" class="form-label">Preço:</label>
                    <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?= isset($item['preco']) ? htmlspecialchars($item['preco']) : '' ?>">
                    <?php if (isset($erros['preco'])): ?>
                        <div class="text-danger"><?= $erros['preco'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3" id="campo-imagem">
                    <label for="imagem" class="form-label">Imagem:</label>
                    <?php
                        $imagemAtual = $item['imagem'] ?? '';
                        $imagemUrl = !empty($imagemAtual)
                            ? URL_BASE . '/' . ltrim($imagemAtual, '/')
                            : '';
                    ?>
                    <?php if (!empty($imagemUrl)): ?>
                        <div class="mb-2">
                            <img src="<?= htmlspecialchars($imagemUrl) ?>"
                                alt="<?= htmlspecialchars($item['nome'] ?? '') ?>"
                                style="max-width: 120px; max-height: 120px; display: block; margin-bottom: 0.5rem;">

                            <small class="text-muted">
                                Deixe em branco para manter a imagem atual.
                            </small>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="imagem" name="imagem">
                    <?php if (isset($erros['imagem'])): ?>
                        <div class="text-danger"><?= $erros['imagem'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="alert alert-light border d-none" id="info-tema">
                    Tema usa a imagem fixa do sistema e não aceita upload.
                </div>
                <button type="submit" class="btn btn-primary">Atualizar</button>
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