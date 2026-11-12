<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralEntrada.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/solicitacaoAcesso.css">
    <title>LudoSkill - Solicitar acesso</title>
</head>
<body>
    <header><?php include __DIR__ . '/../includes/menuEntrada.php'; ?></header>
    <main class="pagina-solicitacao">
        <section class="card formulario-solicitacao">
            <div class="cabecalho-formulario">
                <h1>Solicite acesso para sua empresa</h1>
                <p>Envie os dados do futuro gestor e da empresa. A equipe administradora analisará a solicitação.</p>
            </div>

            <?php if (!empty($sucesso)): ?>
                <div class="alerta sucesso" role="status"><?= htmlspecialchars($sucesso) ?></div>
            <?php else: ?>
                <form action="<?= URL_BASE ?>/solicitar-acesso/enviar" method="post">
                    <fieldset>
                        <legend>Dados do gestor</legend>
                        <div class="grade-formulario">
                            <?php foreach ([
                                'nome_completo' => ['Nome completo', 'text'],
                                'data_nascimento' => ['Data de nascimento', 'date'],
                                'cpf' => ['CPF', 'text'],
                                'email' => ['E-mail', 'email'],
                                'numero_telefone' => ['Telefone', 'tel'],
                            ] as $campo => [$rotulo, $tipo]): ?>
                                <div class="formgroup <?= $campo === 'nome_completo' ? 'campo-largo' : '' ?>">
                                    <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                                    <input type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($solicitacao[$campo] ?? '') ?>" required>
                                    <?php if (isset($erros[$campo])): ?><span class="erro"><?= htmlspecialchars($erros[$campo]) ?></span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Dados da empresa</legend>
                        <div class="grade-formulario">
                            <?php foreach ([
                                'empresa_nome' => ['Nome da empresa', 'text'],
                                'empresa_cnpj' => ['CNPJ', 'text'],
                                'empresa_email' => ['E-mail da empresa', 'email'],
                            ] as $campo => [$rotulo, $tipo]): ?>
                                <div class="formgroup <?= $campo === 'empresa_nome' ? 'campo-largo' : '' ?>">
                                    <label for="<?= $campo ?>"><?= $rotulo ?>:</label>
                                    <input type="<?= $tipo ?>" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($solicitacao[$campo] ?? '') ?>" required>
                                    <?php if (isset($erros[$campo])): ?><span class="erro"><?= htmlspecialchars($erros[$campo]) ?></span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="formgroup campo-largo">
                                <label for="mensagem">Mensagem adicional (opcional):</label>
                                <textarea id="mensagem" name="mensagem" maxlength="1000"><?= htmlspecialchars($solicitacao['mensagem'] ?? '') ?></textarea>
                                <?php if (isset($erros['mensagem'])): ?><span class="erro"><?= htmlspecialchars($erros['mensagem']) ?></span><?php endif; ?>
                            </div>
                        </div>
                    </fieldset>
                    <button class="botao brilho" type="submit">Enviar solicitação</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
