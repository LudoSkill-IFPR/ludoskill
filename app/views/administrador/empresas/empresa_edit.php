<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Editar Empresa</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main><div class="container">
        <div id="topo"><a class="botao brilho" href="<?= URL_BASE ?>/administrador/empresas">Voltar</a><div><h1>Edição de Empresa</h1><p class="mensagem">Atualize os dados da empresa.</p></div></div>
        <form class="card" action="<?= URL_BASE ?>/administrador/empresas/atualizar" method="post">
            <input type="hidden" name="id" value="<?= (int) ($empresa['id_empresa'] ?? $empresa['id'] ?? 0) ?>">
            <div class="formgroup"><label for="nome">Nome:</label><input class="card-secundario" type="text" id="nome" name="nome" value="<?= htmlspecialchars($empresa['nome'] ?? '') ?>"><?php if (isset($erros['nome'])): ?><div class="erro"><?= htmlspecialchars($erros['nome']) ?></div><?php endif; ?></div>
            <div class="formgroup"><label for="cnpj">CNPJ:</label><input class="card-secundario" type="text" id="cnpj" name="cnpj" maxlength="18" value="<?= htmlspecialchars($empresa['cnpj'] ?? '') ?>"><?php if (isset($erros['cnpj'])): ?><div class="erro"><?= htmlspecialchars($erros['cnpj']) ?></div><?php endif; ?></div>
            <div class="formgroup"><label for="email">E-mail:</label><input class="card-secundario" type="email" id="email" name="email" value="<?= htmlspecialchars($empresa['email'] ?? '') ?>"><?php if (isset($erros['email'])): ?><div class="erro"><?= htmlspecialchars($erros['email']) ?></div><?php endif; ?></div>
            <div class="formgroup"><label for="plano">Plano:</label><select class="card-secundario" id="plano" name="plano"><option value="">Selecione um plano</option><?php foreach (['BASICO' => 'Básico', 'INTERMEDIARIO' => 'Intermediário', 'AVANCADO' => 'Avançado', 'CORPORATIVO' => 'Corporativo'] as $valor => $rotulo): ?><option value="<?= $valor ?>" <?= ($empresa['plano'] ?? '') === $valor ? 'selected' : '' ?>><?= $rotulo ?></option><?php endforeach; ?></select><?php if (isset($erros['plano'])): ?><div class="erro"><?= htmlspecialchars($erros['plano']) ?></div><?php endif; ?></div>
            <button type="submit" class="botao brilho">Atualizar</button>
        </form>
    </div></main>
</body>
</html>
