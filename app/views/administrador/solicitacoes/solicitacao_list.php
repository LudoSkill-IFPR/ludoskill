<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/solicitacaoAcesso.css">
    <title>LudoSkill - Solicitações de acesso</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/administrador/inicial">Voltar</a>
                <div>
                    <h1>Solicitações de acesso</h1>
                    <p class="mensagem">Analise os dados antes de criar o acesso do gestor.</p>
                </div>
            </div>

            <?php if (!empty($mensagem)): ?>
                <div class="aviso-administrador <?= $tipoMensagem === 'erro' ? 'aviso-erro' : 'aviso-sucesso' ?>" role="status"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if (empty($lista)): ?>
                <div class="card">Nenhuma solicitação recebida.</div>
            <?php else: ?>
                <div class="lista-solicitacoes">
                    <?php foreach ($lista as $item): ?>
                        <article class="card solicitacao-card">
                            <div class="solicitacao-cabecalho">
                                <div>
                                    <h2><?= htmlspecialchars($item->getNomeCompleto()) ?></h2>
                                    <p><?= htmlspecialchars($item->getEmpresaNome()) ?></p>
                                </div>
                                <span class="badge badge-<?= strtolower($item->getEstado()) ?>"><?= htmlspecialchars($item->getEstado()) ?></span>
                            </div>
                            <div class="dados-solicitacao">
                                <p><strong>E-mail:</strong> <?= htmlspecialchars($item->getEmail()) ?></p>
                                <p><strong>Telefone:</strong> <?= htmlspecialchars($item->getNumeroTelefone()) ?></p>
                                <p><strong>CPF:</strong> <?= htmlspecialchars($item->getCpf()) ?></p>
                                <p><strong>Nascimento:</strong> <?= $item->getDataNascimento()?->format('d/m/Y') ?></p>
                                <p><strong>CNPJ:</strong> <?= htmlspecialchars($item->getEmpresaCnpj()) ?></p>
                                <p><strong>E-mail da empresa:</strong> <?= htmlspecialchars($item->getEmpresaEmail()) ?></p>
                                <p><strong>Enviada em:</strong> <?= $item->getDataSolicitacao()?->format('d/m/Y H:i') ?></p>
                                <?php if ($item->getMensagem() !== ''): ?><p class="campo-total"><strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($item->getMensagem())) ?></p><?php endif; ?>
                            </div>

                            <?php if ($item->getEstado() === 'PENDENTE'): ?>
                                <div class="acoes-solicitacao">
                                    <form action="<?= URL_BASE ?>/administrador/solicitacoes-acesso/aprovar" method="post" class="form-aprovacao">
                                        <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                        <div>
                                            <label for="plano-<?= $item->getId() ?>">Plano:</label>
                                            <select id="plano-<?= $item->getId() ?>" name="plano">
                                                <option value="BASICO">Básico</option>
                                                <option value="INTERMEDIARIO">Intermediário</option>
                                                <option value="AVANCADO">Avançado</option>
                                                <option value="CORPORATIVO">Corporativo</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="senha-<?= $item->getId() ?>">Senha inicial:</label>
                                            <input id="senha-<?= $item->getId() ?>" name="senha" type="password" minlength="8" autocomplete="new-password" required>
                                        </div>
                                        <button class="botao brilho" type="submit">Aprovar e enviar</button>
                                    </form>
                                    <form action="<?= URL_BASE ?>/administrador/solicitacoes-acesso/recusar" method="post" onsubmit="return confirm('Deseja recusar esta solicitação?')">
                                        <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                        <button class="botao botao-desativar" type="submit">Recusar</button>
                                    </form>
                                </div>
                            <?php elseif ($item->getEstado() === 'APROVADA'): ?>
                                <p class="resultado-solicitacao">Acesso criado. E-mail <?= $item->isEmailEnviado() ? 'enviado' : 'não enviado' ?>.</p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
