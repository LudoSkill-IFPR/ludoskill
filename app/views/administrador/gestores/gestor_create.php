<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Cadastrar Gestor</title>
</head>

<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/gestores">Voltar</a>
                <div>
                    <h1>Cadastro de Gestor</h1>
                    <p class="mensagem">Cadastre um gestor e vincule-o a uma empresa.</p>
                </div>
            </div>
            <form class="card" action="<?= URL_BASE ?>/administrador/gestores/salvar" method="post">
                <?php foreach (['nome_completo' => ['Nome completo', 'text'], 'data_nascimento' => ['Data de nascimento', 'date'], 'cpf' => ['CPF', 'text'], 'email' => ['E-mail', 'email'], 'senha' => ['Senha', 'password'], 'numero_telefone' => ['Número de telefone', 'text']] as $campo => [$rotulo, $tipo]): ?>
                    <div class="formgroup">
                        <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                        <input class="card-secundario" type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= $tipo !== 'password' ? htmlspecialchars($gestor[$campo] ?? '') : '' ?>">
                        <?php if (isset($erros[$campo])): ?><div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="formgroup">
                    <label for="id_empresa">Empresa:</label>
                    <select class="card-secundario" id="id_empresa" name="id_empresa">
                        <option value="">Selecione uma empresa</option>
                        <?php foreach ($empresas as $empresa): ?><option value="<?= (int) $empresa['id_empresa'] ?>" <?= (string) ($gestor['id_empresa'] ?? '') === (string) $empresa['id_empresa'] ? 'selected' : '' ?>><?= htmlspecialchars($empresa['nome']) ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_empresa'])): ?><div class="erro"><?= htmlspecialchars($erros['id_empresa']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        </div>
    </main>
</body>

</html>