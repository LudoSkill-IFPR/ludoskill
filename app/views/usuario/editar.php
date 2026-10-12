<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Editar perfil</title>
</head>

<body>
    <main>
        <div class="container">
            <h1>Editar perfil</h1>
            <form class="card" action="<?= URL_BASE ?>/perfil/atualizar" method="post">
                <?php $valores = $dados ?? [];
                foreach (['nome_completo' => ['Nome completo', 'text', $usuario->getNomeCompleto()], 'data_nascimento' => ['Data de nascimento', 'date', $usuario->getDataNascimento()?->format('Y-m-d') ?? ''], 'email' => ['E-mail', 'email', $usuario->getEmail()], 'numero_telefone' => ['Telefone', 'text', $usuario->getNumeroTelefone() ?? '']] as $campo => [$rotulo, $tipo, $padrao]): ?><div class="formgroup"><label for="<?= $campo ?>"><?= $rotulo ?></label><input class="card-secundario" id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $tipo ?>" value="<?= htmlspecialchars($valores[$campo] ?? $padrao) ?>"><?php if (isset($erros[$campo])): ?><div class="erro"><?= htmlspecialchars($erros[$campo]) ?></div><?php endif; ?></div><?php endforeach; ?>
                <div class="formgroup"><label for="senha">Nova senha</label><input class="card-secundario" id="senha" name="senha" type="password" minlength="8"><small>Deixe em branco para manter a atual.</small><?php if (isset($erros['senha'])): ?><div class="erro"><?= htmlspecialchars($erros['senha']) ?></div><?php endif; ?></div><button class="botao brilho" type="submit">Salvar</button><a class="botao" href="<?= URL_BASE ?>/perfil">Cancelar</a>
            </form>
        </div>
    </main>
</body>

</html>