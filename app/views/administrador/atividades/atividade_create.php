<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Cadastrar Atividade</title>
</head>

<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/atividades">Voltar</a>
                <div>
                    <h1>Cadastro de Atividade</h1>
                    <p class="mensagem">Cadastre uma atividade e vincule-a a um modulo.</p>
                </div>
            </div>
            <form class="card" action="<?= URL_BASE ?>/administrador/atividades/salvar" method="post">
                <?php foreach (['nome' => ['Nome da atividade', 'text']] as $campo => [$rotulo, $tipo]): ?>
                    <div class="formgroup">
                        <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                        <input class="card-secundario" type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= $tipo !== 'password' ? htmlspecialchars($atividade[$campo] ?? '') : '' ?>">
                        <?php if (isset($erros[$campo])): ?><div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="formgroup">
                    <label for="tipo_atividade">Tipo de atividade:</label>
                    <select class="card-secundario" id="tipo_atividade" name="tipo_atividade">
                        <option value="">Selecione um tipo</option>
                        <?php foreach (['INTRODUCAO' => 'Introducao', 'REVISAO' => 'Revisao', 'SIMULACAO' => 'Simulacao'] as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= ($atividade['tipo_atividade'] ?? '') === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['tipo_atividade'])): ?><div class="erro"><?= htmlspecialchars($erros['tipo_atividade']) ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="hidden" value="0" id="estrelas" name="estrelas">
                    <input type="hidden" value="0" id="pontuacao" name="pontuacao">
                    <input type="hidden" value="PENDENTE" id="estado" name="estado">
                </div>
                <div class="formgroup">
                    <label for="id_modulo">Modulo:</label>
                    <select class="card-secundario" id="id_modulo" name="id_modulo">
                        <option value="">Selecione um modulo</option>
                        <?php foreach ($modulos as $modulo): ?><option value="<?= (int) $modulo['id_modulo'] ?>" <?= (string) ($atividade['id_modulo'] ?? '') === (string) $modulo['id_modulo'] ? 'selected' : '' ?>><?= htmlspecialchars($modulo['nome']) ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_modulo'])): ?><div class="erro"><?= htmlspecialchars($erros['id_modulo']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        </div>
    </main>
</body>

</html>
