<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>LudoSkill - Funcionário</title>
</head>
<body>

    <h1>Área do Funcionário</h1>

    <p>
        Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_logado']->getNomeCompleto()) ?>!
    </p>

</body>
</html>