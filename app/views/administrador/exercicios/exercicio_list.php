<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Exercícios</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao-voltar botao brilho" href="<?= URL_BASE ?>/administrador/inicial"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/exercicios/cadastrar">Novo exercício</a>
            </div>
            <h1>Exercícios</h1>
            <?php if (empty($lista)): ?>
                <p>Nenhum exercício encontrado.</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>ID</th><th>Tipo</th><th>Descrição</th><th>Atividade</th><th>Itens</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($lista as $exercicio): ?>
                            <?php
                            $conteudo = json_decode($exercicio->getConteudo(), true);
                            $conteudo = is_array($conteudo) ? $conteudo : [];
                            $tipoJson = array_is_list($conteudo)
                                ? ((($conteudo[0]['tipo'] ?? 'q') === 's') ? 'simulacao' : 'exercicio')
                                : ($conteudo['tipo'] ?? 'exercicio');
                            $tipo = in_array($tipoJson, ['s', 'simulacao'], true) ? 'Simulação' : 'Exercício';
                            $quantidadeItens = $tipo === 'Simulação'
                                ? 1
                                : count($conteudo['questoes'] ?? (array_is_list($conteudo) ? $conteudo : []));
                            ?>
                            <tr>
                                <td><?= $exercicio->getId() ?></td>
                                <td><?= $tipo ?></td>
                                <td><?= htmlspecialchars($exercicio->getDescricao()) ?></td>
                                <td><?= htmlspecialchars($exercicio->getAtividade()->getNome()) ?></td>
                                <td><?= $quantidadeItens ?></td>
                                <td><div id="acao">
                                    <a class="botao brilho" href="<?= URL_BASE ?>/administrador/exercicios/editar?id=<?= $exercicio->getId() ?>">Editar</a>
                                    <form method="post" action="<?= URL_BASE ?>/administrador/exercicios/excluir" onsubmit="return confirm('Deseja excluir este exercício?')">
                                        <input type="hidden" name="id" value="<?= $exercicio->getId() ?>">
                                        <button class="botao brilhinho" type="submit">Excluir</button>
                                    </form>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
