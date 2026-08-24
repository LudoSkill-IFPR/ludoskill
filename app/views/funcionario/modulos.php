<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/geralUsuario.css">
    
    <title>LudoSkill - Módulos</title>
</head>
<body>
    
    <header>
        <?php include_once (__DIR__. "/../includes/menuFuncionario.php") ?>
    </header>
    
    <!-- tenho que ver como caralhos funciona o span e onde usar ele -->
    <main>
        <h1>Módulos</h1>
        <h2>Acompanhe o seu progresso nos módulos de treinamento</h2>
        
        <section class="modulos">
            <div class="card">
                <div class="container">
                    <img src="" alt="icone do módulo">
                </div>
                <h3>[N° módulo + nome]</h3>
                <p>fundamentos e conceitos básicos</p>
            </div>

            <div class="card">
                <div class="container">
                    <img src="" alt="cadeado">
                </div>

                <h3>[N° próximo módulo + nome próximo módulo]</h3>
                <p>[pequena descrição do módulo]</p>
            </div>

            <div class="card">
                <div class="container">
                    <img src="" alt="icone estrela">
                </div>
                <h3>Desbloqueie o próximo módulo!</h3>
                <p>Atinja [N° de estrelas] estrelas para dar um novo passo no seu aprendizado.</p>
            </div>
        </section>
        
        <section class="progresso">
            <div class="card">
                <h3>Progresso atual</h3>
                <span>[N° módulo + nome]</span>
                <!-- barra de progresso -->
                <p>Fases a serem concluídas: <strong>[N° fases]</strong></p>
            </div>

            <div class="card">
                <div class="container">
                    <img src="" alt="icone estrela">
                </div>
                <h3>Próximo módulo</h3>
                <p>Conquiste mais [N° de estrelas] estrelas para desbloquear</p>
                <span>
                    <strong>[N° próximo módulo + nome próximo módulo]</strong>
                    <p>[pequena descrição do módulo]</p>
                </span>
            </div>
        </section>
    </main>

</body>
</html>