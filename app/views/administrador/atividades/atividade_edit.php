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
                <input type="hidden" name="id" value="<?= htmlspecialchars($atividade['id_atividade'] ?? $atividade['id'] ?? '') ?>">

                <div class="formgroup">
                    <label for="nome">Nome da atividade:</label>
                    <input class="card-secundario" type="text" id="nome" name="nome" value="<?= htmlspecialchars($atividade['nome'] ?? '') ?>">
                    <?php if (isset($erros['nome'])): ?><div class="erro"><?= htmlspecialchars($erros['nome']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="tipo_atividade">Tipo de atividade:</label>
                    <select class="card-secundario" id="tipo_atividade" name="tipo_atividade">
                        <option value="">Selecione um tipo</option>
                        <?php foreach (['INTRODUCAO' => 'Introdução', 'REVISAO' => 'Revisão', 'SIMULACAO' => 'Simulação'] as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= ($atividade['tipo_atividade'] ?? '') === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['tipo_atividade'])): ?><div class="erro"><?= htmlspecialchars($erros['tipo_atividade']) ?></div><?php endif; ?>
                </div>

                <input type="hidden" name="estado" value="<?= htmlspecialchars($atividade['estado'] ?? 'PENDENTE') ?>">
                <input type="hidden" name="pontuacao" value="<?= htmlspecialchars($atividade['pontuacao'] ?? '0') ?>">
                <input type="hidden" name="estrelas" value="<?= htmlspecialchars($atividade['estrelas'] ?? '0') ?>">

                <div class="formgroup">
                    <label for="id_modulo">Módulo:</label>
                    <select class="card-secundario" id="id_modulo" name="id_modulo">
                        <option value="">Selecione um módulo</option>
                        <?php foreach ($modulos as $modulo): ?>
                            <option value="<?= (int) $modulo['id_modulo'] ?>" <?= (string) ($atividade['id_modulo'] ?? '') === (string) $modulo['id_modulo'] ? 'selected' : '' ?>><?= htmlspecialchars($modulo['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_modulo'])): ?><div class="erro"><?= htmlspecialchars($erros['id_modulo']) ?></div><?php endif; ?>
                </div>

                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        </div>
    </main>
</body>
</html>
