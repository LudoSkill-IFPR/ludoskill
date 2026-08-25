<?php
use app\helpers\Auth;
Auth::funcionario_required();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/geralUsuario.css">
    
    <title>LudoSkill - Perfil</title>
</head>

<body>
    <header>
        <?php include_once (__DIR__. "/../includes/menuFuncionario.html") ?>
    </header>

    <main>
        <h1>Olá, [nome do Funcionário]</h1>

        <div class="card">
            <section class="pefil">
                <img src="" alt="">
            </section>

            <section>
                <h2></h2>
                <p></p>

                <h2></h2>
                <p></p>

                <h2></h2>
                <p></p>

                <a href=""></a>
            </section>
        </div>

        <div class="card">
            <h2></h2>
            <span>
                <p></p>
            </span>

            <span>
                <p></p>
            </span>
        </div>

        <div class="card">
            <img src="" alt="">
            <h2></h2>
            <span></span>
            <span></span>
        </div>
    </main>
</body>
</html>