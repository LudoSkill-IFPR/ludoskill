<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Listar Funcionários</title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuGestor.php"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="<?= URL_BASE ?>/gestor/inicial" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a href="<?= URL_BASE ?>/gestor/funcionarios/cadastrar" class="botao brilho"><i class="bi bi-plus"></i> Novo Funcionário</a>

                <div>
                    <h1>Listagem de Funcionários</h1>
                    <p class="mensagem">Visualize os aprendizes cadastrados.</p>
                </div>
            </div>
    
            <section class="card">
                <h2>Lista de Funcionários</h2>
                <table id="funcionario">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data de Nascimento</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Numero de Telefone</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (($lista ?? []) as $funcionario): ?>
                        <tr>
                            <td data-label="Nome"><?= htmlspecialchars($funcionario->getNomeCompleto()) ?></td>
                            <td data-label="Data de nascimento"><?= htmlspecialchars($funcionario->getDataNascimento()?->format('d/m/Y') ?? '') ?></td>
                            <td data-label="CPF"><?= htmlspecialchars($funcionario->getCpf()) ?></td>
                            <td data-label="E-mail"><?= htmlspecialchars($funcionario->getEmail()) ?></td>
                            <td data-label="Número de telefone"><?= htmlspecialchars($funcionario->getNumeroTelefone() ?? '') ?></td>
                            <td data-label="Status">
                                <?php if ($funcionario->isAtivo()): ?>
                                    <span class="badge badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                    
                            <td id="acao" data-label="Ações">
                                <button
                                    type="button"
                                    class="botao brilho"
                                    data-abrir-modal-usuario="modal-funcionario-<?= $funcionario->getIdFuncionario() ?>"
                                >
                                    <i class="bi bi-eye"></i> Visualizar
                                </button>
                                <?php if ($funcionario->isAtivo()): ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/desativar" method="post" onsubmit="return confirm('Deseja desativar este funcionário?')">
                                        <input type="hidden" name="id" value="<?= $funcionario->getIdFuncionario() ?>">
                                        <button type="submit" class="botao brilhinho botao-desativar"><i class="bi bi-person-x"></i> Desativar</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= URL_BASE ?>/gestor/funcionarios/ativar" method="post" onsubmit="return confirm('Deseja reativar este funcionário?')">
                                        <input type="hidden" name="id" value="<?= $funcionario->getIdFuncionario() ?>">
                                        <button type="submit" class="botao brilho botao-ativar"><i class="bi bi-person-check"></i> Ativar</button>
                                    </form>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </section>

            <?php foreach (($lista ?? []) as $funcionario): ?>
                <dialog class="modal-usuario" id="modal-funcionario-<?= $funcionario->getIdFuncionario() ?>" data-modal-usuario>
                    <div class="modal-usuario-cabecalho">
                        <div>
                            <span class="modal-usuario-tipo">Funcionário</span>
                            <h2><?= htmlspecialchars($funcionario->getNomeCompleto()) ?></h2>
                        </div>
                        <button type="button" class="fechar-modal-usuario" data-fechar-modal-usuario aria-label="Fechar informações do funcionário">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <dl class="dados-modal-usuario">
                        <div><dt>Nome completo</dt><dd><?= htmlspecialchars($funcionario->getNomeCompleto()) ?></dd></div>
                        <div><dt>Data de nascimento</dt><dd><?= htmlspecialchars($funcionario->getDataNascimento()?->format('d/m/Y') ?? 'Não informada') ?></dd></div>
                        <div><dt>CPF</dt><dd><?= htmlspecialchars($funcionario->getCpf()) ?></dd></div>
                        <div><dt>E-mail</dt><dd><?= htmlspecialchars($funcionario->getEmail()) ?></dd></div>
                        <div><dt>Telefone</dt><dd><?= htmlspecialchars($funcionario->getNumeroTelefone() ?? 'Não informado') ?></dd></div>
                        <div><dt>Nível</dt><dd><?= $funcionario->getNivel() ?></dd></div>
                        <div><dt>Pontuação total</dt><dd><?= $funcionario->getPontuacaoTotal() ?></dd></div>
                        <div><dt>Saldo de bolotas</dt><dd>B$ <?= $funcionario->getBolotasTotais() ?></dd></div>
                        <div><dt>Status</dt><dd><span class="badge <?= $funcionario->isAtivo() ? 'badge-ativo' : 'badge-inativo' ?>"><?= $funcionario->isAtivo() ? 'Ativo' : 'Inativo' ?></span></dd></div>
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
