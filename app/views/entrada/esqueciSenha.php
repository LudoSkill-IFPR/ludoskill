<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h1>Esqueci minha senha</h1>
    <form action="<?= URL_BASE ?>/esqueci-senha" method="post">
        <label for="email">Digite seu email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Enviar</button>
    </form>
    
</body>
</html>