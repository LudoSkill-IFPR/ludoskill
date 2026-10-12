<?php $gestor = $gestor instanceof \app\models\Gestor ? $gestor : \app\models\Gestor::arrayParaObjeto($gestor); ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Editar Gestor</title>
</head>

<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/gestores">Voltar</a>
                <h1>Editar Gestor</h1>
            </div>
            <form class="card" action="<?= URL_BASE ?>/administrador/gestores/atualizar" method="post">
                <input type="hidden" name="id_gestor" value="<?= $gestor->getIdGestor() ?>">
                <?php foreach (['nome_completo' => ['Nome completo', 'text'], 'data_nascimento' => ['Data de nascimento', 'date'], 'cpf' => ['CPF', 'text'], 'email' => ['E-mail', 'email'], 'numero_telefone' => ['Número de telefone', 'text']] as $campo => [$rotulo, $tipo]): ?>
                    <div class="formgroup">
                        <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                        <?php $valor = match ($campo) { 'nome_completo' => $gestor->getNomeCompleto(), 'data_nascimento' => $gestor->getDataNascimento()?->format('Y-m-d') ?? '', 'cpf' => $gestor->getCpf(), 'email' => $gestor->getEmail(), 'numero_telefone' => $gestor->getNumeroTelefone() ?? '', default => '' }; ?>
                        <input class="card-secundario" type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>">
                        <?php if (isset($erros[$campo])): ?><div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="formgroup"><label for="senha">Nova senha:</label><input class="card-secundario" type="password" id="senha" name="senha" minlength="8"><small>Deixe em branco para manter a senha atual.</small><?php if (isset($erros['senha'])): ?><div class="erro"><?= htmlspecialchars($erros['senha']) ?></div><?php endif; ?></div>
                <div class="formgroup">
                    <label for="id_empresa">Empresa:</label>
                    <select class="card-secundario" id="id_empresa" name="id_empresa">
                        <option value="">Selecione uma empresa</option>
                        <?php foreach ($empresas as $empresa): ?><option value="<?= $empresa->getId() ?>" <?= $gestor->getEmpresa()->getId() === $empresa->getId() ? 'selected' : '' ?>><?= htmlspecialchars($empresa->getNome()) ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_empresa'])): ?><div class="erro"><?= htmlspecialchars($erros['id_empresa']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="botao brilho">Atualizar</button>
            </form>
        </div>
    </main>
</body>

</html>
