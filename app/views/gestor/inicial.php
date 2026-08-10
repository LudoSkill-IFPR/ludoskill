<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>LudoSkill - Gestor</title>
</head>
<body>

    <h1>Área do Gestor</h1>

    <p>
        Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_logado']->getNomeCompleto()) ?>!
    </p>

</body>
</html>