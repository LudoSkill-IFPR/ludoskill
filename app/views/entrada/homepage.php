<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/arruma.css">
    <link rel="icon" type="x-icon" href="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png">
    
    <title>LudoSkill</title>
</head>
<body>
    <header>
        <?php include_once(__DIR__ . '/../includes/menuEntrada.php'); ?>
    </header>

    <main>
        <div id="apresentacao">
            <div class="container">
                <h1>Qualificação de funcionários por <strong>gamificação</strong></h1>

                <p>E se aprender a consertar celulares pudesse ser tão envolvente quanto passar de fase em um jogo?<br><br>Conheça o LudoSkill, um sistema que transforma a qualificação de funcionários em uma jornada de aprendizado interativa, com desafios, atividades práticas e recompensas. <br>Aqui, cada etapa é uma oportunidade de desenvolver habilidades, testar conhecimentos e conquistar novas etapas rumo à evolução profissional.<br>Seja você um novo técnico querendo aprimorar suas habilidades ou alguém curioso para descobrir uma nova forma de aprender, o LudoSkill tem um desafio para você!<br>Prepare-se para aprender, jogar e evoluir. Sua próxima fase começa aqui!</p>

                <!-- <a class="botao brilho" href="">Orçamentos</a> -->
            </div>

            <div class="container mascote-cena">
                <span class="objeto-flutuante bolota bolota-um" aria-hidden="true"></span>
                <span class="objeto-flutuante bolota bolota-dois" aria-hidden="true"></span>
                <span class="objeto-flutuante estrela" aria-hidden="true"></span>
                <span class="objeto-flutuante losango" aria-hidden="true"></span>
                <div class="sombra-esquilo" aria-hidden="true"></div>
                <img class="esquilo-animado" src="<?= URL_BASE ?>/assets/imagens/esquilo-grande.png" alt="Mascote esquilo do LudoSkill">
            </div>
        </div>
    </main>
</body>
</html>
