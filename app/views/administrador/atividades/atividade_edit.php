<?php use app\helpers\TinyMCEHelper; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Editar Atividade</title>
</head>
<body>
    <header><?php include_once __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/atividades">Voltar</a>
                <div>
                    <h1>Edição de Atividade</h1>
                    <p class="mensagem">Edite uma atividade da trilha de aprendizado.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/administrador/atividades/atualizar" method="post">
                <input type="hidden" name="id" value="<?= $atividade->getId() ?>">

                <div class="formgroup">
                    <label for="nome">Nome da atividade:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= htmlspecialchars($atividade->getNome()) ?>">
                    <?php if (isset($erros['nome'])): ?><div class="erro"><?= htmlspecialchars($erros['nome']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao">Descrição completa:</label>
                    <textarea id="descricao" name="descricao"><?= htmlspecialchars($atividade->getDescricao(), ENT_QUOTES, 'UTF-8') ?></textarea>
                    <!-- <small>Apresente o conteúdo que será abordado antes dos exercícios.</small> -->
                    <?php if (isset($erros['descricao'])): ?><div class="erro"><?= htmlspecialchars($erros['descricao']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="tipo_atividade">Tipo de atividade:</label>
                    <select class="card-secundario" id="tipo_atividade" name="tipo_atividade">
                        <option value="">Selecione um tipo</option>
                        <?php foreach (['INTRODUCAO' => 'Introdução', 'REVISAO' => 'Revisão', 'SIMULACAO' => 'Simulação', 'TEXTO' => 'Texto'] as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= $atividade->getTipoAtividade() === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['tipo_atividade'])): ?><div class="erro"><?= htmlspecialchars($erros['tipo_atividade']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup"><label for="estado">Estado:</label><select class="card-secundario" id="estado" name="estado"><option value="ATIVO" <?= $atividade->getEstado() === 'ATIVO' ? 'selected' : '' ?>>Ativo</option><option value="INATIVO" <?= $atividade->getEstado() === 'INATIVO' ? 'selected' : '' ?>>Inativo</option></select></div>
                <div class="formgroup"><label for="ordem">Ordem:</label><input class="card-secundario" type="number" min="1" id="ordem" name="ordem" value="<?= $atividade->getOrdem() ?>"><?php if (isset($erros['ordem'])): ?><div class="erro"><?= htmlspecialchars($erros['ordem']) ?></div><?php endif; ?></div>
                <div class="formgroup"><label for="tempo_limite">Tempo limite (segundos):</label><input class="card-secundario" type="number" min="1" id="tempo_limite" name="tempo_limite" value="<?= $atividade->getTempoLimite() ?>"><?php if (isset($erros['tempo_limite'])): ?><div class="erro"><?= htmlspecialchars($erros['tempo_limite']) ?></div><?php endif; ?></div>

                <div class="formgroup">
                    <label for="id_modulo">Módulo:</label>
                    <select class="card-secundario" id="id_modulo" name="id_modulo">
                        <option value="">Selecione um módulo</option>
                        <?php foreach ($modulos as $modulo): ?>
                            <option value="<?= $modulo->getId() ?>" <?= $atividade->getModulo()->getId() === $modulo->getId() ? 'selected' : '' ?>><?= htmlspecialchars($modulo->getNome()) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_modulo'])): ?><div class="erro"><?= htmlspecialchars($erros['id_modulo']) ?></div><?php endif; ?>
                </div>

                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        </div>
    </main>
    <?= TinyMCEHelper::render('#descricao') ?>
</body>
</html>
