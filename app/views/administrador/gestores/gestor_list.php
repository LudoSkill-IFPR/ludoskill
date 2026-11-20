<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <title>Gestores</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo"><a class="botao-voltar botao brilho" href="<?= URL_BASE ?>/administrador/inicial"><i class="bi bi-arrow-left"></i> Voltar</a><a class="botao brilho" href="<?= URL_BASE ?>/administrador/gestores/cadastrar">Novo gestor</a></div>
            <h1>Gestores</h1>
            <?php if (empty($lista)): ?><p>Nenhum gestor cadastrado.</p><?php else: ?><table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Empresa</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($lista as $gestor): ?><tr>
                                <td data-label="Nome"><?= htmlspecialchars($gestor->getNomeCompleto()) ?></td>
                                <td data-label="E-mail"><?= htmlspecialchars($gestor->getEmail()) ?></td>
                                <td data-label="Empresa"><?= htmlspecialchars($gestor->getEmpresa()->getNome()) ?></td>
                                <td data-label="Status">
                                    <?php if ($gestor->getEstado() === 'ATIVO'): ?>
                                        <span class="badge badge-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Ações">
                                    <div id="acao">
                                        <button
                                            class="botao brilho"
                                            type="button"
                                            data-abrir-modal-usuario="modal-gestor-<?= $gestor->getIdGestor() ?>"
                                        >
                                            <i class="bi bi-eye"></i> Visualizar
                                        </button>
                                        <?php if ($gestor->isAtivo()): ?>
                                            <form method="post" action="<?= URL_BASE ?>/administrador/gestores/desativar" onsubmit="return confirm('Deseja desativar este gestor?')">
                                                <input type="hidden" name="id" value="<?= $gestor->getIdGestor() ?>">
                                                <button class="botao brilhinho botao-desativar" type="submit">Desativar</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="<?= URL_BASE ?>/administrador/gestores/ativar" onsubmit="return confirm('Deseja reativar este gestor?')">
                                                <input type="hidden" name="id" value="<?= $gestor->getIdGestor() ?>">
                                                <button class="botao brilho botao-ativar" type="submit">Ativar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr><?php endforeach; ?></tbody>
                </table><?php endif; ?>

            <?php foreach (($lista ?? []) as $gestor): ?>
                <dialog class="modal-usuario" id="modal-gestor-<?= $gestor->getIdGestor() ?>" data-modal-usuario>
                    <div class="modal-usuario-cabecalho">
                        <div>
                            <span class="modal-usuario-tipo">Gestor</span>
                            <h2><?= htmlspecialchars($gestor->getNomeCompleto()) ?></h2>
                        </div>
                        <button type="button" class="fechar-modal-usuario" data-fechar-modal-usuario aria-label="Fechar informações do gestor">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <dl class="dados-modal-usuario">
                        <div><dt>Nome completo</dt><dd><?= htmlspecialchars($gestor->getNomeCompleto()) ?></dd></div>
                        <div><dt>Data de nascimento</dt><dd><?= htmlspecialchars($gestor->getDataNascimento()?->format('d/m/Y') ?? 'Não informada') ?></dd></div>
                        <div><dt>CPF</dt><dd><?= htmlspecialchars($gestor->getCpf()) ?></dd></div>
                        <div><dt>E-mail</dt><dd><?= htmlspecialchars($gestor->getEmail()) ?></dd></div>
                        <div><dt>Telefone</dt><dd><?= htmlspecialchars($gestor->getNumeroTelefone() ?? 'Não informado') ?></dd></div>
                        <div><dt>Empresa</dt><dd><?= htmlspecialchars($gestor->getEmpresa()->getNome()) ?></dd></div>
                        <div><dt>Status</dt><dd><span class="badge <?= $gestor->isAtivo() ? 'badge-ativo' : 'badge-inativo' ?>"><?= $gestor->isAtivo() ? 'Ativo' : 'Inativo' ?></span></dd></div>
                    </dl>

                    <!-- <div class="modal-usuario-rodape">
                        <button type="button" class="botao brilho" data-fechar-modal-usuario>Fechar</button>
                    </div> -->
                </dialog>
            <?php endforeach; ?>
        </div>
    </main>
    <script src="<?= URL_BASE ?>/assets/javascript/modalUsuario.js"></script>
</body>

</html>
