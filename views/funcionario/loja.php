<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/lojaFuncionario.css">
    
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
                    <p><img style="width: 20px;" src="<?= URL_BASE ?>/assets/imagens/bolotona.png" alt="icone de bolotas">Saldo de bolotas:</p>
                    <p>B$ <?= $funcionario?->getBolotasTotais() ?? 0 ?></p>
                </div>
            </div>

            <div id="loja">
                <section class="card">
                    <h3><i class="bi bi-funnel-fill"></i> Filtros</h3>
                
                    <form id="filtros-loja">
                        <h4>Ordenar por:</h4>
                        <div class="card-secundario">
                            <div class="formgroup">
                                <input type="radio" id="ordem-menor-preco" name="ordenar" value="preco-asc"><label for="ordem-menor-preco">Menor preço</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" id="ordem-maior-preco" name="ordenar" value="preco-desc"><label for="ordem-maior-preco">Maior preço</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" id="ordem-az" name="ordenar" value="nome-asc"><label for="ordem-az">Ordem alfabética A-Z</label>
                            </div>
                            <div class="formgroup">
                                <input type="radio" id="ordem-za" name="ordenar" value="nome-desc"><label for="ordem-za">Ordem alfabética Z-A</label>
                            </div>
                        </div>

                        <h4>Filtrar por:</h4>

                        <div class="card-secundario">
                            <div class="formgroup">
                                <input type="checkbox" id="naoPossui" name="naoPossui"><label for="naoPossui">Não possui</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" id="possui" name="possui"><label for="possui">Possui</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" id="tema" name="tema"><label for="tema">Temas de interface</label>
                            </div>
                            <div class="formgroup">
                                <input type="checkbox" id="estilo" name="estilo"><label for="estilo">Estilo para o esquilo</label>
                            </div>
                        
                        </div>
                    </form>
                </section>

                <section id="itens">
                    <h3>Itens</h3>
                    <?php if ($mensagem): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
                    <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
                    <ul id="lista-itens">
                        <?php foreach (($lista ?? []) as $item): ?>
                            <?php $possui = false; foreach ($inventario as $registro) { if ($registro->getItem()->getId() === $item->getId()) { $possui = true; break; } } ?>
                            <li data-nome="<?= htmlspecialchars($item->getNome(), ENT_QUOTES, 'UTF-8') ?>" data-preco="<?= $item->getPreco() ?>" data-tipo="<?= htmlspecialchars($item->getTipo(), ENT_QUOTES, 'UTF-8') ?>" data-possui="<?= $possui ? '1' : '0' ?>">
                                <div class="card">
                                    <img src="<?= URL_BASE ?>/<?= htmlspecialchars(ltrim($item->getImagem(), '/')) ?>" alt="imagem do item" width="200px" height="200px">
                                    <h4><?= htmlspecialchars($item->getNome()) ?></h4>
                                    <p>B$<?= $item->getPreco() ?></p>
                                    <?php if ($possui): ?>
                                        <span class="botao">Já adquirido</span>
                                    <?php elseif ($item->getPreco() > ($funcionario?->getBolotasTotais() ?? 0)): ?>
                                        <span class="botao"><i class="bi bi-ban"></i> Bolotas insuficientes</span>
                                    <?php else: ?>
                                        <form action="<?= URL_BASE ?>/funcionario/comprar" method="post" onsubmit="return confirm('Deseja comprar este item?')">
                                            <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                            <button class="botao brilho" type="submit"><i class="bi bi-bag-plus-fill"></i> Comprar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p id="sem-itens" hidden>Nenhum item corresponde aos filtros selecionados.</p>
                </section>
                
            </div>

        </div>
    </main>
    <script>
    (() => {
        const filtros = document.getElementById('filtros-loja');
        const lista = document.getElementById('lista-itens');
        const vazio = document.getElementById('sem-itens');
        if (!filtros || !lista) return;

        const atualizar = () => {
            const itens = [...lista.querySelectorAll(':scope > li')];
            const possui = filtros.possui.checked;
            const naoPossui = filtros.naoPossui.checked;
            const tema = filtros.tema.checked;
            const estilo = filtros.estilo.checked;
            const ordem = filtros.querySelector('[name="ordenar"]:checked')?.value ?? '';

            itens.forEach(item => {
                const passaPosse = (!possui && !naoPossui)
                    || (possui && item.dataset.possui === '1')
                    || (naoPossui && item.dataset.possui === '0');
                const passaTipo = (!tema && !estilo)
                    || (tema && item.dataset.tipo === 'TEMA')
                    || (estilo && item.dataset.tipo === 'VISUAL_ESQUILOSO');
                item.hidden = !(passaPosse && passaTipo);
            });

            itens.sort((a, b) => {
                if (ordem === 'preco-asc') return Number(a.dataset.preco) - Number(b.dataset.preco);
                if (ordem === 'preco-desc') return Number(b.dataset.preco) - Number(a.dataset.preco);
                if (ordem === 'nome-asc') return a.dataset.nome.localeCompare(b.dataset.nome, 'pt-BR', { sensitivity: 'base' });
                if (ordem === 'nome-desc') return b.dataset.nome.localeCompare(a.dataset.nome, 'pt-BR', { sensitivity: 'base' });
                return 0;
            }).forEach(item => lista.appendChild(item));

            vazio.hidden = itens.some(item => !item.hidden);
        };

        filtros.addEventListener('change', atualizar);
        filtros.addEventListener('submit', event => event.preventDefault());
    })();
    </script>
</body>
</html>
