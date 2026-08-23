<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/geralUsuario.css">
    
    <title>LudoSkill - Loja</title>
</head>

<body>
    <header>
        <?php include_once (__DIR__. "/../includes/menuFuncionario.html") ?>
    </header>

    <main>
        <h1>Loja Super-Mega-Maneira</h1>
        <h2>Para você deixar sua interface e esquilo mais esquilosos</h2>
        
        <div class="saldo">
            <img src="" alt="icone de bolotas">
            <span>Saldo: B$[quantidade de bolotas]</span>
        </div>

        <section class="filtros">
            <h3>Filtros</h3>
            
            <!-- deve ser assíncrono -->
            <form action="">
                <h4>Ordenar por:</h4>
                <div class="card">
                    <input type="radio" name="ordenar"><label for="ordenar">Menor preço</label>
                    <input type="radio" name="ordenar"><label for="ordenar">Maior preço</label>
                    <input type="radio" name="ordenar"><label for="ordenar">Odem alfabética A-Z</label>
                    <input type="radio" name="ordenar"><label for="ordenar">Odem alfabética Z-A</label>
                </div>

                <h4>Filtrar por:</h4>
                <div class="card">
                    <input type="checkbox" name="naoPossui"><label for="naoPossui">Não possui</label>
                    <input type="checkbox" name="possui"><label for="possui">Possui</label>
                    <input type="checkbox" name="tema"><label for="tema">Temas de interface</label>
                    <input type="checkbox" name="estilo"><label for="estilo">Estilo para o esquilo</label>
                </div>
            </form>
        </section>

        <section class="itens">
            <h3>Itens</h3>
            <ul>
                <!-- usar uma estrutura de repetição com base no html abaixo para adicionar os itens -->
                <li>
                    <div class="card">
                        <img src="" alt="">
                        <h4>[tipo produto+nome]</h4>
                        <span>B$[preço do item]</span>
                        
                        <a href="">Comprar</a>
                    </div>
                </li>
            </ul>
        </section>
    </main>

</body>
</html>