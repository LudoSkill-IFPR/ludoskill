<?php
use app\repositories\FuncionarioRepository;
use app\helpers\Auth;

Auth::funcionario_required();

$funcionarioRepository = new FuncionarioRepository();

$usuario = $_SESSION['usuario_logado'];

$funcionarios = $funcionarioRepository->getFuncionarios();
$funcionario = "";

foreach ($funcionarios as $func) {
    if($usuario->getId() == $func['id_usuario']){
        $funcionario = $func;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../assets/css/lojaFuncionario.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>LudoSkill - Loja</title>
</head>

<body>
    <header>
        <?php include_once (__DIR__. "/../includes/menuFuncionario.php") ?>
    </header>

    <main>
        <div class="container">
            <div class="topo">
                <div>
                    <h1>Loja Super-Hiper-Mega-Maneira</h1>
                    <h2 class="mensagem">Para você deixar sua interface e esquilo mais esquilosos</h2>
                </div>
                
                <div class="card verde">
                    <p><img style="width: 20px;" src="../assets/imagens/bolotona.png" alt="icone de bolotas">Saldo de bolotas:</p>
                    <p>B$ <?= htmlspecialchars($funcionario['bolotas_totais'] ?? 0) ?></p>
                </div>
            </div>

            <div id="loja">
                <section class="card">
                    <h3><i class="bi bi-funnel-fill"></i> Filtros</h3>
                
                    <!-- deve ser assíncrono -->
                    <form action="">
                        <h4>Ordenar por:</h4>
                        <div class="card-secundario">
                            <div class="formgroup">
                                <input type="radio" name="ordenar"><label for="ordenar">Menor preço</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" name="ordenar"><label for="ordenar">Maior preço</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" name="ordenar"><label for="ordenar">Odem alfabética A-Z</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" name="ordenar"><label for="ordenar">Odem alfabética Z-A</label>
                            </div>
                        </div>

                        <h4>Filtrar por:</h4>

                        <div class="card-secundario">
                            <div class="formgroup">
                                <input type="checkbox" name="naoPossui"><label for="naoPossui">Não possui</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" name="possui"><label for="possui">Possui</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" name="tema"><label for="tema">Temas de interface</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" name="estilo"><label for="estilo">Estilo para o esquilo</label>
                            </div>
                        
                        </div>
                    </form>
                </section>

                <section id="itens">
                    <h3>Itens</h3>
                    <ul>
                        <?php foreach (($lista ?? []) as $item): ?>
                            <li>
                                <div class="card">
                                    <img src="../public/<?= htmlspecialchars($item['imagem'] ?? '') ?>" alt="imagem do item" width="200px" height="200px">
                                    <h4><?= htmlspecialchars($item['nome'] ?? '') ?></h4>
                                    <p>B$<?= htmlspecialchars($item['preco'] ?? '') ?></p>
                                    <?php if($item['preco'] > $funcionario['bolotas_totais']){ ?>
                                        <a disabled class="botao" onclick="comprar(<?= $item['id_item']?>, <?= $item['preco']?>)"><i class="bi bi-ban"></i> Bolotas Insuficientes</a>
                                    <?php }else{ ?>
                                        <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/comprar?id=<?= $item['id_item'] ?>"><i class="bi bi-bag-plus-fill"></i> Comprar</a>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <!-- usar uma estrutura de repetição com base no html abaixo para adicionar os itens -->
                        <!-- <li>
                            <div class="card">
                                <img src="" alt="imagem do item">
                                <h4>[tipo produto+nome]</h4>
                                <p>B$[preço do item]</p>
                
                                <a class="botao brilho" href=""><i class="bi bi-bag-plus-fill"></i> Comprar</a>
                            </div>
                        </li> -->
                    </ul>
                </section>
                
            </div>

        </div>
    </main>
</body>
</html>
