<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/inicialFuncionario.css">
    <title>LudoSkill</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <header>
        <?php include_once(__DIR__ . "/../includes/menuFuncionario.php"); ?>
    </header>

    <main>
        <div class="container">
            <div id="foto" style="background-image: url('<?= URL_BASE ?>/assets/imagens/esquilo-pfp.jpg');">
                <?php $equipado = null; foreach ($inventario as $registro) { if ($registro->isEmUso() && $registro->getItem()->getTipo() === 'VISUAL_ESQUILOSO') { $equipado = $registro->getItem(); break; } } ?>
                <?php if ($equipado): ?><img src="<?= URL_BASE ?>/<?= htmlspecialchars(ltrim($equipado->getImagem(), '/')) ?>" alt="Item equipado"><?php endif; ?>
            </div>
            <h1>Olá, <?= htmlspecialchars($funcionario->getNomeCompleto()) ?>!</h1>
            <?php if ($mensagem): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
            <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
            <h2 class="mensagem">Continue trilhando seu caminho para a qualificação</h2>
            
            <section class="infobase">
                <div class="card">
                    <h3><i class="bi bi-piggy-bank-fill"></i>Saldo de bolotas:</h3>
                    <p>B$<?= $funcionario->getBolotasTotais() ?></p>
                </div>

                <div class="card">
                    <h3>Desempenho atual:</h3>
                    <p><i class="bi bi-star-fill brilhinho"></i><?= number_format((float) $resumo['desempenho'], 1, ',', '.') ?> / 3</p>
                </div>
                
                <div id="loja" class="card verde">
                    <h3><i class="bi bi-cart-fill"></i> Loja</h3>
                    <a class="brilhinho" href="<?= URL_BASE ?>/funcionario/loja">Acessar loja <i class="bi bi-caret-right-fill"></i></a>
                </div>
            </section>
            
            <section class="progresso">
                <div id="fase" class="card">
                    <h2>Continue de onde parou!</h2>
                    
                    <div class="card-secundario">
                        <?php if ($proximaAtividade): ?>
                            <h3>Atividade <?= $proximaAtividade->getOrdem() ?></h3>
                            <p><?= htmlspecialchars($proximaAtividade->getNome()) ?></p>
                            <a href="<?= URL_BASE ?>/funcionario/atividades/exercicios?id=<?= $proximaAtividade->getId() ?>" class="botao brilho">Iniciar</a>
                        <?php else: ?><p>Você concluiu todas as atividades liberadas.</p><?php endif; ?>
                    </div>
                </div>

                <div id="progresso" class="card">
                    <h2>Seu progresso</h2>
                    <p><?= (int) $resumo['concluidas'] ?> de <?= (int) $resumo['total'] ?> atividades concluídas</p>
                </div>
            </section>

            <section class="inventario">
                <div class="card">
                    <h2><i class="bi bi-backpack4-fill"></i> Inventário</h2>
                    <p>Nossa, olha o que você já conquistou!</p>

                    <ul id="inventario">
                        <?php foreach($inventario as $registro): ?>
                        <li>
                            <?php $item = $registro->getItem(); ?>
                            <div class="card-secundario">
                                <img src="<?= URL_BASE ?>/<?= htmlspecialchars(ltrim($item->getImagem(), '/')) ?>" alt="imagem do item" width='200px' height='200px'>
                                <h4><?= htmlspecialchars($item->getNome()) ?></h4>
                                <p><?= $registro->isEmUso() ? 'Equipado' : 'Livre' ?></p>
                                <form action="<?= URL_BASE ?>/funcionario/equipar" method="post"><input type="hidden" name="id_item" value="<?= $item->getId() ?>"><button class="botao brilho" type="submit">Equipar</button></form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (empty($inventario)): ?><p>Seu inventário ainda está vazio.</p><?php endif; ?>

                </div>
            </section>

        </div>
    </main>

</body>
</html>
