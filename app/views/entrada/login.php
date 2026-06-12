<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/homepage.css">
    <title>LudoSkill</title>
</head>
<body>
    <header>
        <div id="logo">
            <div id="logozinha"><img src="assets/imagens/skillo.png" alt=""></div>
            <a href="../"><h2>LudoSkill</h2></a>
        </div>

        <div>
            <label for="temas" class="botao">Temas</label>
            <select name="temas" id="temas" class="botao">
                <option value="padrao">Padrão</option>
                <option value="escuro">Escuro</option>
                <option value="claro">Claro</option>
            </select>
        </div>
        
    </header>
    <main>
        <form action="" method="post">
            <img src="assets/imagens/skillo.png" alt=""><br>
        
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" placeholder="skillo@bolotas.com"><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha" id="senha" placeholder="euAMObolotas123!">

            <div class="espacinho"></div>

            <button id="enviar">Entrar</button><br>
            <div id="centraliza">
                <div><a href="">Esqueceu a senha?</a></div>
            </div>
            
        </form>
    </main>
</body>
</html>