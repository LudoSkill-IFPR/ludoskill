<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/simulacao.css">
    <title>LudoSkill - Simulação: <?= htmlspecialchars($atividade->getNome()) ?></title>
</head>
<body class="corpo-simulacao">
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>
    <main class="pagina-simulacao">
        <div class="topo-simulacao">
            <a class="botao brilho" id="voltar-conteudo" href="<?= URL_BASE ?>/funcionario/atividades/conteudo?id=<?= $atividade->getId() ?>">Voltar ao conteúdo</a>
            <div>
                <span class="rotulo-simulacao">Simulação prática</span>
                <h1><?= htmlspecialchars($atividade->getNome()) ?></h1>
            </div>
            <?php if (!isset($resultado)): ?>
                <?php $segundosRestantes = max(0, (int) $tempoRestante); ?>
                <div class="timer-simulacao" id="cronometro-atividade" role="timer" aria-live="polite">
                    <span id="rotulo-timer">Tempo restante</span>
                    <strong id="valor-timer" data-segundos="<?= $segundosRestantes ?>"><?= sprintf('%02d:%02d', intdiv($segundosRestantes, 60), $segundosRestantes % 60) ?></strong>
                    <small id="mensagem-timer">O tempo continua contando.</small>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($erro)): ?><div class="alerta-simulacao"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if (isset($resultado)): ?>
            <section class="resultado-simulacao-geral">
                <div><strong><?= (int) $resultado['pontuacao'] ?>%</strong><span>Pontuação</span></div>
                <div><strong><?= (int) $resultado['estrelas'] ?></strong><span>Estrelas</span></div>
                <div><strong><?= (int) $resultado['bolotas'] ?></strong><span>Bolotas</span></div>
                <div><strong><?= (int) $resultado['acertos'] ?>/<?= (int) $resultado['total'] ?></strong><span>Ações corretas</span></div>
            </section>
        <?php endif; ?>

        <form action="<?= URL_BASE ?>/funcionario/atividades/exercicios/responder" method="post" id="form-simulacao"
            data-storage-key="ludoskill:simulacao:<?= $_SESSION['usuario_logado']->getId() ?>:<?= $atividade->getId() ?>"
            data-finalizada="<?= isset($resultado) ? '1' : '0' ?>">
            <input type="hidden" name="id_atividade" value="<?= $atividade->getId() ?>">

            <?php foreach ($exercicios as $numero => $exercicio): ?>
                <?php
                $conteudo = $exercicio['conteudo'];
                $etapas = $conteudo['etapas'] ?? [];
                $ferramentas = $conteudo['ferramentas'] ?? [];
                $acoesEsperadas = array_column($conteudo['sequencia'] ?? [], 'acao');
                $idExercicio = (string) $exercicio['id_exercicio'];
                $respostaAtual = $respostasSimulacao[$idExercicio]['acoes'] ?? '[]';
                $acoesAtuais = json_decode((string) $respostaAtual, true);
                if (!is_array($acoesAtuais)) $acoesAtuais = [];
                $pecaQuebrada = (string) ($conteudo['peca_quebrada'] ?? 'bateria');
                $nomesComponentes = ['bateria' => 'Bateria', 'camera' => 'Câmera', 'chip' => 'Chip'];
                $dadosFerramentas = [
                    'luvas' => ['Luvas', '♧'], 'aquecedor' => ['Soprador térmico', '♨'],
                    'palheta' => ['Palheta plástica', '◒'], 'chave_fenda' => ['Chave de fenda', '🪛'],
                    'pinca' => ['Pinça', '⌁'], 'pino_ejetor' => ['Pino ejetor', '⊙'],
                    'adesivo' => ['Adesivo para bateria', '▰'],
                ];
                ?>
                <section class="tabuleiro-simulacao simulacao-interativa"
                    data-id="<?= (int) $idExercicio ?>"
                    data-sequencia="<?= htmlspecialchars(json_encode($acoesEsperadas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
                    data-acoes="<?= htmlspecialchars(json_encode($acoesAtuais, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
                    aria-labelledby="titulo-simulacao-<?= (int) $idExercicio ?>">
                    <aside class="painel-simulacao painel-etapas">
                        <h2>Etapas</h2>
                        <div class="lista-etapas">
                            <?php foreach ($etapas as $indice => $etapa): ?>
                                <article class="etapa-simulacao">
                                    <span><?= str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                    <div><h3>Etapa <?= $indice + 1 ?></h3><p><?= nl2br(htmlspecialchars($etapa['descricao'] ?? '')) ?></p></div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </aside>

                    <section class="centro-simulacao">
                        <div class="cabecalho-cenario">
                            <span>Simulação <?= $numero + 1 ?></span>
                            <h2 id="titulo-simulacao-<?= (int) $idExercicio ?>"><?= htmlspecialchars($exercicio['descricao']) ?></h2>
                            <p>Use as ferramentas e execute a manutenção na ordem correta.</p>
                        </div>
                        <div class="andamento-simulacao" aria-live="polite">
                            <strong class="progresso-simulacao">Ação 1 de <?= count($acoesEsperadas) ?></strong>
                            <span class="mensagem-acao">Escolha a primeira ação.</span>
                        </div>
                        <div class="aparelho-simulacao" aria-label="Representação interativa de um telefone celular">
                            <div class="camera camera-um"></div><div class="camera camera-dois"></div><div class="flash-aparelho">+</div>
                            <div class="tela-aparelho"><span>LudoSkill</span><strong>MANUTENÇÃO</strong></div>
                            <div class="placa-aparelho protecao-visual">PROTEÇÃO</div>
                            <div class="bateria-aparelho <?= $pecaQuebrada === 'bateria' ? 'alvo-visual peca-defeituosa' : '' ?>">BATERIA</div>
                            <div class="chip-aparelho <?= $pecaQuebrada === 'chip' ? 'alvo-visual peca-defeituosa' : '' ?>">CHIP</div>
                            <div class="camera-alvo <?= $pecaQuebrada === 'camera' ? 'alvo-visual peca-defeituosa' : '' ?>">CÂMERA</div>
                            <div class="tampa-aparelho">TAMPA TRASEIRA</div>
                        </div>
                        <fieldset class="acoes-pecas">
                            <legend>Peças do aparelho</legend>
                            <button type="button" class="acao-simulacao" data-acao="remover:tampa" <?= isset($resultado) ? 'disabled' : '' ?>>Remover tampa</button>
                            <button type="button" class="acao-simulacao" data-acao="remover:protecao" <?= isset($resultado) ? 'disabled' : '' ?>>Remover proteção</button>
                            <button type="button" class="acao-simulacao" data-acao="remover:<?= htmlspecialchars($pecaQuebrada) ?>" <?= isset($resultado) ? 'disabled' : '' ?>>Retirar <?= htmlspecialchars($nomesComponentes[$pecaQuebrada] ?? $pecaQuebrada) ?></button>
                            <button type="button" class="acao-simulacao" data-acao="instalar:<?= htmlspecialchars($pecaQuebrada) ?>" <?= isset($resultado) ? 'disabled' : '' ?>>Instalar <?= htmlspecialchars($nomesComponentes[$pecaQuebrada] ?? $pecaQuebrada) ?> nova</button>
                            <button type="button" class="acao-simulacao" data-acao="instalar:protecao" <?= isset($resultado) ? 'disabled' : '' ?>>Recolocar proteção</button>
                            <button type="button" class="acao-simulacao" data-acao="instalar:tampa" <?= isset($resultado) ? 'disabled' : '' ?>>Recolocar tampa</button>
                        </fieldset>
                        <input class="registro-acoes" type="hidden" name="respostas_simulacao[<?= (int) $idExercicio ?>][acoes]" value="<?= htmlspecialchars(json_encode($acoesAtuais), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($resultado)): ?>
                            <div class="feedback-simulacao feedback-correto"><strong>Manutenção concluída.</strong><p>A pontuação considera a ordem das ações e as tentativas incorretas.</p></div>
                        <?php endif; ?>
                    </section>

                    <aside class="painel-simulacao painel-ferramentas">
                        <h2>Ferramentas</h2>
                        <div class="lista-ferramentas">
                            <?php foreach ($ferramentas as $ferramenta): ?>
                                <?php if (!isset($dadosFerramentas[$ferramenta])) continue; ?>
                                <button type="button" class="acao-simulacao ferramenta-simulacao" data-acao="ferramenta:<?= htmlspecialchars($ferramenta) ?>" <?= isset($resultado) ? 'disabled' : '' ?>>
                                    <span><?= $dadosFerramentas[$ferramenta][1] ?></span><?= htmlspecialchars($dadosFerramentas[$ferramenta][0]) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                </section>
            <?php endforeach; ?>

            <div class="acoes-simulacao">
                <?php if (!isset($resultado)): ?>
                    <button class="botao brilho" id="finalizar-simulacao" type="submit" disabled>Concluir manutenção</button>
                <?php else: ?>
                    <a class="botao brilho" href="<?= URL_BASE ?>/funcionario/modulos">Concluir simulação</a>
                <?php endif; ?>
            </div>
        </form>
    </main>
    <script src="<?= URL_BASE ?>/assets/javascript/simulacao.js"></script>
</body>
</html>
