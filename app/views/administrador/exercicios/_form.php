<?php
$questoesIniciais = $questoes ?? [[
    'questao' => '',
    'enunciado' => '',
    'alternativas' => ['', ''],
    'alternativa_correta' => null,
    'justificativa' => ''
]];
?>

<form class="card formulario-exercicio" action="<?= $acaoFormulario ?>" method="post" id="form-exercicio">
    <?php if (!empty($exercicio['id_exercicio'] ?? $exercicio['id'] ?? null)): ?>
        <input type="hidden" name="id" value="<?= (int) ($exercicio['id_exercicio'] ?? $exercicio['id']) ?>">
    <?php endif; ?>

    <div class="formgroup">
        <label for="descricao">Descrição do conjunto:</label>
        <input class="card-secundario" type="text" id="descricao" name="descricao" maxlength="255" required value="<?= htmlspecialchars($exercicio['descricao'] ?? '') ?>">
        <?php if (isset($erros['descricao'])): ?><div class="erro"><?= htmlspecialchars($erros['descricao']) ?></div><?php endif; ?>
    </div>

    <div class="formgroup">
        <label for="atividade_id">Atividade:</label>
        <select class="card-secundario" id="atividade_id" name="atividade_id" required>
            <option value="">Selecione uma atividade</option>
            <?php foreach (($atividades ?? []) as $atividade): ?>
                <option value="<?= (int) $atividade['id_atividade'] ?>" <?= (string) ($exercicio['atividade_id'] ?? $exercicio['id_atividade'] ?? '') === (string) $atividade['id_atividade'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($atividade['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($erros['atividade_id'])): ?><div class="erro"><?= htmlspecialchars($erros['atividade_id']) ?></div><?php endif; ?>
    </div>

    <?php if (isset($erros['conteudo'])): ?><div class="erro erro-conteudo"><?= htmlspecialchars($erros['conteudo']) ?></div><?php endif; ?>

    <div id="lista-questoes" aria-live="polite"></div>

    <div class="acoes-formulario">
        <button type="button" class="botao brilho" id="adicionar-questao">Adicionar questão</button>
        <button type="submit" class="botao brilho">Salvar</button>
    </div>
</form>

<template id="modelo-questao">
    <fieldset class="card-secundario bloco-questao">
        <div class="cabecalho-questao">
            <legend>Questão <span class="numero-questao"></span></legend>
            <button type="button" class="botao remover-questao">Remover questão</button>
        </div>

        <div class="formgroup">
            <label>Questão:</label>
            <input class="card-secundario campo-questao" type="text" maxlength="255" required>
        </div>

        <div class="formgroup">
            <label>Enunciado:</label>
            <textarea class="card-secundario campo-enunciado" required></textarea>
        </div>

        <div class="alternativas"></div>
        <button type="button" class="botao brilho adicionar-alternativa">Adicionar alternativa</button>

        <div class="formgroup">
            <label>Justificativa da alternativa correta:</label>
            <textarea class="card-secundario campo-justificativa" required></textarea>
        </div>
    </fieldset>
</template>

<script>
window.questoesIniciais = <?= json_encode(
    array_values($questoesIniciais),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;
</script>
<script src="<?= URL_BASE ?>/assets/javascript/exercicios.js"></script>
