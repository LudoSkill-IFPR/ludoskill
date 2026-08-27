<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Itens</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="/administrador/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="/administrador/itens/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Novo Item</a>

                <div>
                    <h1>Listagem de Itens</h1>
                    <p class="mensagem">Visualize as recompensas.</p>
                </div>
            </div>
    
            <section class="card">
                <h2>Lista de Itens</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Imagem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $item): ?>
                            <?php
                                $imagem = $item['imagem'] ?? '';
                                $imagemUrl = !empty($imagem)
                                    ? URL_BASE . '/' . ltrim($imagem, '/')
                                    : '';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id_item'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['nome'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['preco'] ?? '') ?></td>
                            <td class="imagem">
                                 <?php if (!empty($imagemUrl)): ?>
                                    <img src="<?= htmlspecialchars($imagemUrl) ?>" alt="<?= htmlspecialchars($item['nome'] ?? '') ?>" style="max-width: 100px; max-height: 100px;">
                                <?php endif; ?>
                            </td>

                            <td id="acao">
                                <a href="<?= URL_BASE ?>/administrador/itens/editar?id=<?= $item['id_item'] ?>" class="botao brilho"><i class="bi bi-pencil"></i> Editar</a>
                                
                                <form action="<?= URL_BASE ?>/administrador/itens/excluir" method="post" onsubmit="return confirm('Deseja excluir este item?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id_item'] ?? '') ?>">
                                    <button type="submit" class="botao brilhinho"><i class="bi bi-trash"></i> Excluir</button>
                                </form>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </section>

        </div>
    </main>
</body>
</html>
