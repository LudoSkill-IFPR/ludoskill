<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>LudoSkill - Meu Perfil</title>
</head>
<body>
    <header>
        <?php
        $perfil = $usuario->getPerfil();
        if ($perfil === 'admin') {
            include_once __DIR__ . '/../includes/menuAdministrador.html';
        } elseif ($perfil === 'gestor') {
            include_once __DIR__ . '/../includes/menuGestor.php';
        } else {
            include_once __DIR__ . '/../includes/menuFuncionario.php';
        }
        ?>
    </header>

    <main>
        <div class="pagina-perfil">

            <?php if (!empty($mensagemSucesso)): ?>
                <div class="alerta-sucesso" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($mensagemSucesso) ?></span>
                </div>
            <?php endif; ?>

            <div class="cabecalho-perfil">
                <h1>Olá, <?= htmlspecialchars($usuario->getNomeCompleto()) ?>!</h1>
                <p class="subtitulo">Gerencie suas informações pessoais e visualize seu histórico na plataforma.</p>
            </div>

            <?php
            // Formatação do CPF
            $cpfLimpo = preg_replace('/\D/', '', $usuario->getCpf() ?? '');
            $cpfFormatado = strlen($cpfLimpo) === 11 
                ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfLimpo) 
                : htmlspecialchars($usuario->getCpf() ?? '');
            ?>

            <?php if ($perfil === 'funcionario'): ?>

                <div class="grid-perfil">
                    <!-- Card Esquerdo: Suas informações -->
                    <section class="card-perfil">
                        <div>
                            <h2><i class="bi bi-person-badge-fill"></i> Suas informações:</h2>

                            <div class="secao-avatar">
                                <div class="avatar-circulo">
                                    <img src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="Avatar do Funcionário">
                                </div>
                                <div class="avatar-info">
                                    <h3><?= htmlspecialchars($usuario->getNomeCompleto()) ?></h3>
                                    <span class="badge-cargo">Funcionário</span>
                                </div>
                            </div>

                            <div class="lista-info">
                                <div class="item-info">
                                    <span class="rotulo">Data de nascimento:</span>
                                    <span class="valor"><?= $usuario->getDataNascimento()?->format('d/m/Y') ?? 'Não informada' ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">CPF:</span>
                                    <span class="valor"><?= $cpfFormatado ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">E-mail:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getEmail()) ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Telefone:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getNumeroTelefone() ?? 'Não informado') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="acoes-perfil">
                            <a class="botao brilho" href="<?= URL_BASE ?>/perfil/editar">
                                <i class="bi bi-pencil-square"></i> Editar informações
                            </a>
                        </div>
                    </section>

                    <!-- Coluna Direita: Turma e Progresso -->
                    <div class="coluna-cards">
                        <!-- Card: Turma Cadastrada -->
                        <section class="card-perfil">
                            <h2><i class="bi bi-building"></i> Turma Cadastrada</h2>
                            <div class="lista-info">
                                <div class="item-info">
                                    <span class="rotulo">Empresa associada:</span>
                                    <span class="valor"><?= htmlspecialchars(method_exists($usuario, 'getEmpresa') && $usuario->getEmpresa() ? $usuario->getEmpresa()->getNome() : 'Não vinculada') ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Fase atual / Onde parou:</span>
                                    <span class="valor">
                                        <?php if (!empty($proximaAtividade)): ?>
                                            Módulo <?= $proximaAtividade->getModulo()?->getId() ?? 1 ?> · Atividade <?= $proximaAtividade->getOrdem() ?> (<?= htmlspecialchars($proximaAtividade->getNome()) ?>)
                                        <?php else: ?>
                                            Todas as atividades concluídas!
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </section>

                        <!-- Card: Progresso Geral -->
                        <?php
                        $totalAtividades = (int) ($resumo['total'] ?? 0);
                        $concluidas = (int) ($resumo['concluidas'] ?? 0);
                        $taxaProgresso = $totalAtividades > 0 ? round(($concluidas / $totalAtividades) * 100) : 0;
                        ?>
                        <section class="card-perfil">
                            <h2><i class="bi bi-graph-up-arrow"></i> Progresso geral</h2>
                            <div class="secao-progresso">
                                <div class="progresso-cabecalho">
                                    <span>Conclusão do curso</span>
                                    <span class="taxa"><?= $taxaProgresso ?>%</span>
                                </div>
                                <div class="barra-trilha">
                                    <div class="barra-trilha-preenchimento" style="width: <?= $taxaProgresso ?>%;"></div>
                                </div>
                            </div>

                            <div class="grid-metricas" style="margin-top: 20px;">
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-star-fill"></i></div>
                                    <div class="numero"><?= (int) ($resumo['estrelas'] ?? 0) ?></div>
                                    <div class="legenda">Estrelas conquistadas</div>
                                </div>
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-piggy-bank-fill"></i></div>
                                    <div class="numero">B$ <?= method_exists($usuario, 'getBolotasTotais') ? $usuario->getBolotasTotais() : 0 ?></div>
                                    <div class="legenda">Saldo de bolotas</div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

            <?php elseif ($perfil === 'gestor'): ?>

                <div class="grid-perfil">
                    <!-- Card Esquerdo: Suas informações -->
                    <section class="card-perfil">
                        <div>
                            <h2><i class="bi bi-person-badge-fill"></i> Suas informações:</h2>

                            <div class="lista-info">
                                <div class="item-info">
                                    <span class="rotulo">Nome completo:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getNomeCompleto()) ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Data de nascimento:</span>
                                    <span class="valor"><?= $usuario->getDataNascimento()?->format('d/m/Y') ?? 'Não informada' ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Cargo:</span>
                                    <span class="valor">Gestor Corporativo</span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">CPF:</span>
                                    <span class="valor"><?= $cpfFormatado ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">E-mail institucional:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getEmail()) ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Telefone de contato:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getNumeroTelefone() ?? 'Não informado') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="acoes-perfil">
                            <a class="botao brilho" href="<?= URL_BASE ?>/perfil/editar">
                                <i class="bi bi-pencil-square"></i> Editar informações
                            </a>
                        </div>
                    </section>

                    <!-- Card Direito: Informações da empresa -->
                    <section class="card-perfil">
                        <div>
                            <h2><i class="bi bi-building"></i> Informações da empresa</h2>

                            <div class="lista-info">
                                <div class="item-info">
                                    <span class="rotulo">Razão Social / Nome:</span>
                                    <span class="valor"><?= htmlspecialchars($empresa?->getNome() ?? 'Empresa não associada') ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">CNPJ:</span>
                                    <span class="valor"><?= htmlspecialchars($empresa?->getCnpj() ?? 'Não cadastrado') ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Plano Contratado:</span>
                                    <span class="valor"><?= htmlspecialchars($empresa?->getPlano() ?? 'BÁSICO') ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Total de funcionários na plataforma:</span>
                                    <span class="valor"><?= (int) ($qtdFuncionarios ?? 0) ?> colaboradores</span>
                                </div>
                            </div>
                        </div>

                        <div class="acoes-perfil">
                            <a class="botao" href="<?= URL_BASE ?>/gestor/funcionarios">
                                <i class="bi bi-people"></i> Gerenciar funcionários
                            </a>
                        </div>
                    </section>
                </div>

                <!-- Métricas do Gestor (Figura 14) -->
                <div class="grid-metricas">
                    <div class="card-metrica">
                        <div class="icone"><i class="bi bi-people-fill"></i></div>
                        <div class="numero"><?= (int) ($qtdFuncionarios ?? 0) ?></div>
                        <div class="legenda">Funcionários cadastrados</div>
                    </div>
                    <div class="card-metrica">
                        <div class="icone"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <div class="numero"><?= (int) ($qtdModulos ?? 0) ?></div>
                        <div class="legenda">Módulos de treinamento</div>
                    </div>
                    <div class="card-metrica">
                        <div class="icone"><i class="bi bi-award-fill"></i></div>
                        <div class="numero"><?= number_format((float) ($taxaConclusao ?? 0), 1, ',', '.') ?>%</div>
                        <div class="legenda">Taxa média de conclusão</div>
                    </div>
                </div>

            <?php else: ?>

                <div class="grid-perfil">
                    <section class="card-perfil">
                        <div>
                            <h2><i class="bi bi-shield-lock-fill"></i> Suas informações:</h2>

                            <div class="lista-info">
                                <div class="item-info">
                                    <span class="rotulo">Nome completo:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getNomeCompleto()) ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Cargo no sistema:</span>
                                    <span class="valor">Administrador Geral</span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Data de nascimento:</span>
                                    <span class="valor"><?= $usuario->getDataNascimento()?->format('d/m/Y') ?? 'Não informada' ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">CPF:</span>
                                    <span class="valor"><?= $cpfFormatado ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">E-mail:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getEmail()) ?></span>
                                </div>
                                <div class="item-info">
                                    <span class="rotulo">Telefone:</span>
                                    <span class="valor"><?= htmlspecialchars($usuario->getNumeroTelefone() ?? 'Não informado') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="acoes-perfil">
                            <a class="botao brilho" href="<?= URL_BASE ?>/perfil/editar">
                                <i class="bi bi-pencil-square"></i> Editar informações
                            </a>
                        </div>
                    </section>

                    <section class="card-perfil">
                        <div>
                            <h2><i class="bi bi-speedometer2"></i> Painel do Sistema</h2>
                            <p style="color: #b0b0b0; margin-bottom: 16px;">Visão geral dos recursos ativos no LudoSkill.</p>

                            <div class="grid-metricas">
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-building"></i></div>
                                    <div class="numero"><?= (int) ($qtdEmpresas ?? 0) ?></div>
                                    <div class="legenda">Empresas</div>
                                </div>
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-person-badge"></i></div>
                                    <div class="numero"><?= (int) ($qtdGestores ?? 0) ?></div>
                                    <div class="legenda">Gestores</div>
                                </div>
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-collection-play"></i></div>
                                    <div class="numero"><?= (int) ($qtdModulos ?? 0) ?></div>
                                    <div class="legenda">Módulos</div>
                                </div>
                                <div class="card-metrica">
                                    <div class="icone"><i class="bi bi-shop"></i></div>
                                    <div class="numero"><?= (int) ($qtdItens ?? 0) ?></div>
                                    <div class="legenda">Itens Loja</div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            <?php endif; ?>

            <div style="margin-top: 24px;">
                <?php
                $rotaVoltar = match ($perfil) {
                    'admin' => URL_BASE . '/administrador/inicial',
                    'gestor' => URL_BASE . '/gestor/inicial',
                    default => URL_BASE . '/funcionario/inicial'
                };
                ?>
                <a class="botao" href="<?= $rotaVoltar ?>"><i class="bi bi-arrow-left"></i> Voltar à tela inicial</a>
            </div>

        </div>
    </main>
</body>
</html>