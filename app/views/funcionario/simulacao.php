<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/simulacao.css">
    <title>LudoSkill - Simulação: <?= htmlspecialchars($atividade->getNome()) ?></title>
    <link rel="icon" href="<?= URL_BASE ?>/favicon.ico">
</head>

<body class="corpo-simulacao">
    <header><?php include __DIR__ . '/../includes/menuFuncionario.php'; ?></header>

    <main class="pagina-simulacao">
        <div class="topo-simulacao">
            <a class="botao-voltar botao brilho" id="voltar-conteudo" href="<?= URL_BASE ?>/funcionario/atividades/conteudo?id=<?= $atividade->getId() ?>">
                <i class="bi bi-arrow-left"></i> Voltar ao conteúdo
            </a>
            <div>
                <span class="rotulo-simulacao">Simulação prática</span>
                <h1><?= htmlspecialchars($atividade->getNome()) ?></h1>
            </div>

            <?php if (!isset($resultado)): ?>
                <?php $segundosRestantes = max(0, (int) $tempoRestante); ?>
                <div class="timer-simulacao" id="cronometro-atividade" role="timer" aria-live="polite">
                    <span id="rotulo-timer">Tempo restante</span>
                    <strong id="valor-timer" data-segundos="<?= $segundosRestantes ?>"><?= sprintf('%02d:%02d', intdiv($segundosRestantes, 60), $segundosRestantes % 60) ?></strong>
                    <small id="mensagem-timer">Conclua no tempo para receber todas as estrelas.</small>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($erro)): ?><div class="alerta-simulacao"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <?php if (isset($resultado)): ?>
            <section class="resultado-simulacao-geral">
                <div><strong><?= number_format((float) $resultado['tempo'], 1, ',', '.') ?>s</strong><span>Tempo utilizado</span></div>
                <div><strong><?= (int) $resultado['estrelas'] ?></strong><span>Estrelas</span></div>
                <div><strong><?= (int) ($resultado['erros_simulacao'] ?? 0) ?></strong><span>Erros cometidos</span></div>
                <div><strong><?= (int) $resultado['bolotas'] ?></strong><span>Bolotas</span></div>
            </section>
            <div class="feedback-resultado <?= !empty($resultado['dentro_tempo']) ? 'resultado-no-tempo' : 'resultado-fora-tempo' ?>">
                <?php if (!empty($resultado['dentro_tempo'])): ?>
                    Você concluiu dentro do tempo e recebeu a quantidade máxima de estrelas da atividade.
                <?php else: ?>
                    O aparelho foi consertado, mas o tempo terminou antes da conclusão. Por isso, esta tentativa não recebeu estrelas.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form action="<?= URL_BASE ?>/funcionario/atividades/exercicios/responder" method="post" id="form-simulacao"
            data-storage-key="ludoskill:simulacao:v2:<?= $_SESSION['usuario_logado']->getId() ?>:<?= $atividade->getId() ?>"
            data-finalizada="<?= isset($resultado) ? '1' : '0' ?>">
            <input type="hidden" name="id_atividade" value="<?= $atividade->getId() ?>">

            <?php foreach ($exercicios as $numero => $exercicio): ?>
                <?php
                $conteudo = $exercicio['conteudo'];
                $etapas = $conteudo['etapas'] ?? [];
                $ferramentas = $conteudo['ferramentas'] ?? [];
                $idExercicio = (string) $exercicio['id_exercicio'];
                $respostaAtual = $respostasSimulacao[$idExercicio]['acoes'] ?? '[]';
                $acoesAtuais = json_decode((string) $respostaAtual, true);
                if (!is_array($acoesAtuais)) $acoesAtuais = [];
                $pecaQuebrada = (string) ($conteudo['peca_quebrada'] ?? 'bateria');
                $nomesComponentes = ['bateria' => 'Bateria', 'camera' => 'Câmera', 'chip' => 'Chip'];
                $dadosFerramentas = [
                    'pinca' => ['Pinça', 'pinca-de-manutencao.png'],
                    'aquecedor' => ['Soprador térmico', 'aquecedor.png'],
                    'solda' => ['Ferro de solda', 'solda.png'],
                    'palheta' => ['Palheta plástica', 'palheta.png'],
                    'alcool' => ['Álcool isopropílico', 'alcool.png'],
                ];
                ?>

                <section class="tabuleiro-simulacao simulacao-interativa"
                    data-id="<?= (int) $idExercicio ?>"
                    data-defeito="<?= htmlspecialchars($pecaQuebrada) ?>"
                    data-acoes="<?= htmlspecialchars(json_encode($acoesAtuais, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
                    aria-labelledby="titulo-simulacao-<?= (int) $idExercicio ?>">

                    <aside class="painel-simulacao painel-etapas">
                        <h2>Etapas</h2>
                        <div class="lista-etapas">
                            <?php foreach ($etapas as $indice => $etapa): ?>
                                <article class="etapa-simulacao">
                                    <span><?= str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                    <div>
                                        <h3>Etapa <?= $indice + 1 ?></h3>
                                        <p><?= htmlspecialchars($etapa['descricao'] ?? '') ?></p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </aside>

                    <section class="centro-simulacao">
                        <div class="cabecalho-cenario">
                            <span>Simulação <?= $numero + 1 ?></span>
                            <h2 id="titulo-simulacao-<?= (int) $idExercicio ?>"><?= htmlspecialchars($exercicio['descricao']) ?></h2>
                            <p>Defeito inicial: <strong><?= htmlspecialchars($nomesComponentes[$pecaQuebrada] ?? $pecaQuebrada) ?></strong></p>
                        </div>

                        <div class="andamento-simulacao" aria-live="polite">
                            <strong class="progresso-simulacao">Aparelho aguardando manutenção</strong>
                            <span class="mensagem-acao" aria-live="polite">Escolha uma ferramenta e interaja com o celular.</span>
                        </div>

                        <div class="bancada3d" aria-label="Celular 3D interativo. Arraste para girar o aparelho.">
                            <div class="palco-tela">
                                <div class="tela3d">
                                    <div class="face frente" aria-label="Parte frontal do celular">
                                        <div class="camera-frontal"></div>
                                        <span>Tela</span>
                                    </div>
                                    <div class="face verso" aria-label="Parte traseira do celular">
                                        <div class="estrutura-celular"
data-img-default="<?= URL_BASE ?>/assets/imagens/simulacao/pelado.png"
data-img-sem-bateria="<?= URL_BASE ?>/assets/imagens/simulacao/celular-sem-bateria.png"
data-img-sem-camera="<?= URL_BASE ?>/assets/imagens/simulacao/celular-sem-camera.png"
data-img-sem-chip="<?= URL_BASE ?>/assets/imagens/simulacao/celular-sem-chip.png"></div>
                                        <button type="button" class="componente-celular componente-bateria" data-acao="interagir:bateria" aria-label="Interagir com a bateria">
                                            <img src="<?= URL_BASE ?>/assets/imagens/simulacao/bateria.png" alt="Bateria" draggable="false">
                                        </button>
                                        <button type="button" class="componente-celular componente-camera" data-acao="interagir:camera" aria-label="Interagir com a câmera">
                                            <img src="<?= URL_BASE ?>/assets/imagens/simulacao/camera.png" alt="Câmera" draggable="false">
                                        </button>
                                        <button type="button" class="componente-celular componente-chip" data-acao="interagir:chip" aria-label="Interagir com o chip">
                                            <img src="<?= URL_BASE ?>/assets/imagens/simulacao/chip.png" alt="Chip" draggable="false">
                                        </button>
                                        <button type="button" class="tampa-celular" data-acao="interagir:tampa" aria-label="Interagir com a tampa traseira">
                                            <img src="<?= URL_BASE ?>/assets/imagens/simulacao/verso-celular.png" alt="Tampa traseira" draggable="false">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="instrucao-giro">Clique e arraste para girar o celular</small>
                        </div>


                        <input class="registro-acoes" type="hidden" name="respostas_simulacao[<?= (int) $idExercicio ?>][acoes]" value="<?= htmlspecialchars(json_encode($acoesAtuais), ENT_QUOTES, 'UTF-8') ?>">
                    </section>

                    <aside class="painel-simulacao painel-ferramentas">
                        <h2>Ferramentas</h2>
                        <div class="lista-ferramentas">
                            <?php foreach ($ferramentas as $ferramenta): ?>
                                <?php if (!isset($dadosFerramentas[$ferramenta])) continue; ?>
                                <button type="button" class="acao-simulacao ferramenta-simulacao" data-acao="ferramenta:<?= htmlspecialchars($ferramenta) ?>" <?= isset($resultado) ? 'disabled' : '' ?>>
                                    <span><img src="<?= URL_BASE ?>/assets/imagens/simulacao/<?= htmlspecialchars($dadosFerramentas[$ferramenta][1]) ?>" alt=""></span>
                                    <?= htmlspecialchars($dadosFerramentas[$ferramenta][0]) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="componentes-novos">
                            <h2>Peças novas</h2>
                            <?php foreach ($nomesComponentes as $componente => $rotulo): ?>
                                <button type="button" class="acao-simulacao botao-instalar" data-acao="instalar:<?= $componente ?>" <?= isset($resultado) ? 'disabled' : '' ?>>
                                    <span>+</span> Inserir <?= htmlspecialchars($rotulo) ?>
                                </button>
                            <?php endforeach; ?>
                            <button type="button" class="acao-simulacao botao-fechar" data-acao="instalar:tampa" <?= isset($resultado) ? 'disabled' : '' ?>>
                                Recolocar tampa traseira
                            </button>
                        </div>

                        <div class="estado-componentes">
                            <h2>Estado das peças</h2>
                            <?php foreach ($nomesComponentes as $componente => $rotulo): ?>
                                <p data-estado-componente="<?= $componente ?>"><span></span><?= htmlspecialchars($rotulo) ?>: <strong>Aguardando</strong></p>
                            <?php endforeach; ?>
                            <p class="contador-erros">Erros cometidos: <strong>0</strong></p>
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
