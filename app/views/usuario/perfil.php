<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <title>LudoSkill - Perfil</title>
</head>

<body>
    <main>
        <div class="container">
            <section class="card">
                <h1><?= htmlspecialchars($usuario->getNomeCompleto()) ?></h1>
                <p><?= htmlspecialchars($usuario->getEmail()) ?></p>
                <p>CPF: <?= htmlspecialchars($usuario->getCpf()) ?></p>
                <p>Telefone: <?= htmlspecialchars($usuario->getNumeroTelefone() ?? '') ?></p>
                <p>Nascimento: <?= $usuario->getDataNascimento()?->format('d/m/Y') ?></p><a class="botao brilho" href="<?= URL_BASE ?>/perfil/editar">Editar perfil</a>
            </section>
            <?php if (method_exists($usuario, 'getEmpresa')): ?><section class="card">
                    <h2>Empresa</h2>
                    <p><?= htmlspecialchars($usuario->getEmpresa()->getNome()) ?></p>
                    <p>CNPJ: <?= htmlspecialchars($usuario->getEmpresa()->getCnpj()) ?></p>
                </section><?php endif; ?>
            <?php if ($resumo !== null): ?><section class="card">
                    <h2>Seu desempenho</h2>
                    <p><?= (int) $resumo['concluidas'] ?> de <?= (int) $resumo['total'] ?> atividades concluídas</p>
                    <p><?= (int) $resumo['estrelas'] ?> estrelas · média <?= number_format((float) $resumo['desempenho'], 1, ',', '.') ?></p>
                </section><?php endif; ?>
            <?php $inicio = $usuario->getPerfil() === 'admin' ? 'administrador' : $usuario->getPerfil(); ?><a class="botao" href="<?= URL_BASE ?>/<?= $inicio ?>/inicial">Voltar</a>
        </div>
    </main>
</body>

</html>