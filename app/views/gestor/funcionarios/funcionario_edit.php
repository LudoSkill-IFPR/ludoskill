<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Editar Funcionário</title>
</head>

<body>
    <header><?php include_once __DIR__ . '/../../includes/menuGestor.php'; ?></header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/gestor/funcionarios" class="botao brilho">Voltar</a>
                <div>
                    <h1>Editar Funcionário</h1>
                    <p class="mensagem">Atualize os dados do funcionário.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/gestor/funcionarios/atualizar" method="post">
                <input type="hidden" name="id_funcionario" value="<?= (int) ($funcionario['id_funcionario'] ?? 0) ?>">

                <?php foreach ([
                    'nome_completo' => ['Nome completo', 'text'],
                    'data_nascimento' => ['Data de nascimento', 'date'],
                    'cpf' => ['CPF', 'text'],
                    'email' => ['E-mail', 'email'],
                    'numero_telefone' => ['Número de telefone', 'text']
                ] as $campo => [$rotulo, $tipo]): ?>
                    <div class="formgroup">
                        <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                        <input class="card-secundario" type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($funcionario[$campo] ?? '') ?>">
                        <?php if (isset($erros[$campo])): ?>
                            <div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="formgroup">
                    <label for="senha_hash">Nova senha:</label>
                    <input class="card-secundario" type="password" id="senha_hash" name="senha_hash">
                    <small>Deixe em branco para manter a senha atual.</small>
                    <?php if (isset($erros['senha_hash'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['senha_hash']) ?></div>
                    <?php endif; ?>
                </div>

                <?php foreach ([
                    'bolotas_totais' => 'Bolotas totais',
                    'pontuacao_total' => 'Pontuação total',
                    'nivel' => 'Nível'
                ] as $campo => $rotulo): ?>
                    <div class="formgroup">
                        <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                        <input class="card-secundario" type="number" min="0" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($funcionario[$campo] ?? '0') ?>">
                        <?php if (isset($erros[$campo])): ?>
                            <div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="botao brilho">Atualizar</button>
            </form>
        </div>
    </main>
</body>

</html>
