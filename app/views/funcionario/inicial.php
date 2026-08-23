<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/geralUsuario.css">
    <link rel="stylesheet" href="assets/css/inicialFuncionario.css">
    <title>LudoSkill</title>
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuFuncionario.html"); ?>
    </header>

    <main>
        <div class="container">

            <h1>Olá, [nome do funcionário]!</h1>
            <h2 class="mensagem">Continue trilhando seu caminho para a qualificação</h2>
            
            <section class="infobase">
                <div class="card">
                    <h3>Saldo de bolotas:</h3>
                    <span>B$[N° de bolotas]</span>
                </div>

                <div class="card">
                    <h3>Desempenho atual:</h3>
                    <span><i class="bi bi-star-fill brilhinho"></i> [Desempenho]</span>
                </div>
                
                <div id="loja" class="card verde">
                    <p>Loja</p>
                    <a class="brilhinho" href="">Acessar loja <i class="bi bi-caret-right-fill"></i></a>
                </div>
            </section>
            
            <section class="progresso">
                <div id="fase" class="card">
                    <h2>Continue de onde parou!</h2>
                    
                    <div class="card-secundario">
                        <h3>[Fase x]</h3>
                        <p>[Nome da fase]</p>
                        <p>[descrição da fase]</p>
            
                        <a href="" class="botao brilho">iniciar</a>
                    </div>
                </div>

                <div id="progresso" class="card">
                    <h2>Seu progresso</h2>
                    <!-- BARRA DE PROGRESSO -->
                    <p>[Módulo + fase x de y]</p>
                </div>
            </section>

            <section class="inventario">
                <div class="card">
                    <h2>Inventário</h2>
                    <p>Nossa, olha o que você já conquistou!</p>

                    <ul id="inventario">
                        <!-- usar uma estrutura de repetição com base no html abaixo para adicionar os itens -->
                        <li>
                            <div class="card-secundario">
                                <img src="" alt="imagem do item">
                                <h4>[Nome do item]</h4>
                                <p>[status do item (ativo ou não)]</p>
                                <a href="">[ativar/desativar]</a>
                            </div>
                        </li>
                    </ul>

                </div>
            </section>

        </div>
    </main>

</body>
</html>
