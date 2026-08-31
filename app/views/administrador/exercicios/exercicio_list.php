<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>LudoSkill - Exercícios</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/inicial">Voltar</a>
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/exercicios/cadastrar">Novo exercício</a>
            </div>
            <h1>Exercícios</h1>
            <?php if (empty($lista)): ?>
                <p>Nenhum exercício encontrado.</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>ID</th><th>Descrição</th><th>Atividade</th><th>Questões</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($lista as $exercicio): ?>
                            <?php $conteudo = json_decode($exercicio['conteudo'], true); ?>
                            <tr>
                                <td><?= (int) $exercicio['id_exercicio'] ?></td>
                                <td><?= htmlspecialchars($exercicio['descricao']) ?></td>
                                <td><?= htmlspecialchars($exercicio['nome_atividade']) ?></td>
                                <td><?= is_array($conteudo) ? count($conteudo) : 0 ?></td>
                                <td><div id="acao">
                                    <a class="botao brilho" href="<?= URL_BASE ?>/administrador/exercicios/editar?id=<?= (int) $exercicio['id_exercicio'] ?>">Editar</a>
                                    <form method="post" action="<?= URL_BASE ?>/administrador/exercicios/excluir" onsubmit="return confirm('Deseja excluir este exercício?')">
                                        <input type="hidden" name="id" value="<?= (int) $exercicio['id_exercicio'] ?>">
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
