<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/exercicios.css">
    <title>LudoSkill - Editar Exercícios</title>
</head>
<body>
    <header><?php include __DIR__ . '/../../includes/menuAdministrador.html'; ?></header>
    <main>
        <div class="container">
            <div id="topo">
                <a class="botao-voltar botao brilho" href="<?= URL_BASE ?>/administrador/exercicios"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Edição de Exercícios</h1>
                    <p class="mensagem">Edite as questões ou escolha a peça defeituosa da simulação.</p>
                </div>
            </div>
            <?php
            $exercicio = $exercicio ?? [];
            $exercicioId = $exercicio instanceof \app\models\Exercicio ? $exercicio->getId() : (int) ($exercicio['id_exercicio'] ?? $exercicio['id'] ?? 0);
            $descricaoExercicio = $exercicio instanceof \app\models\Exercicio ? $exercicio->getDescricao() : ($exercicio['descricao'] ?? '');
            $atividadeSelecionada = $exercicio instanceof \app\models\Exercicio
                ? $exercicio->getAtividade()->getId()
                : (int) ($exercicio['atividade_id'] ?? $exercicio['id_atividade'] ?? 0);
            $tipoSelecionado = $tipo ?? ($exercicio instanceof \app\models\Exercicio ? 'exercicio' : ($exercicio['tipo'] ?? 'exercicio'));
            $tipoSelecionado = in_array($tipoSelecionado, ['s', 'simulacao'], true) ? 'simulacao' : 'exercicio';
            $conteudoInicial = $conteudoExercicio ?? [];
            $itensIniciais = $tipoSelecionado === 'simulacao'
                ? []
                : ($conteudoInicial['questoes'] ?? [[
                'enunciado' => '',
                'alternativas' => ['', ''],
                'alternativa_correta' => null,
                'justificativa' => '',
            ]]);
            $pecaQuebradaInicial = $conteudoInicial['peca_quebrada'] ?? 'bateria';
            ?>

            <form class="card formulario-exercicio" action="<?= URL_BASE ?>/administrador/exercicios/atualizar" method="post" id="form-exercicio">
                <input type="hidden" name="id" value="<?= $exercicioId ?>">

                <div class="formgroup">
                    <label for="tipo">Tipo de exercício:</label>
                    <select class="card-secundario" id="tipo" name="tipo" required>
                        <option value="exercicio" <?= $tipoSelecionado === 'exercicio' ? 'selected' : '' ?>>Exercício de questões</option>
                        <option value="simulacao" <?= $tipoSelecionado === 'simulacao' ? 'selected' : '' ?>>Simulação</option>
                    </select>
                    <?php if (isset($erros['tipo'])): ?><div class="erro"><?= htmlspecialchars($erros['tipo']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="descricao">Descrição do conjunto:</label>
                    <input class="card-secundario" type="text" id="descricao" name="descricao" maxlength="255" required value="<?= htmlspecialchars($descricaoExercicio) ?>">
                    <?php if (isset($erros['descricao'])): ?><div class="erro"><?= htmlspecialchars($erros['descricao']) ?></div><?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="atividade_id">Atividade:</label>
                    <select class="card-secundario" id="atividade_id" name="atividade_id" required>
                        <option value="">Selecione uma atividade</option>
                        <?php foreach (($atividades ?? []) as $atividade): ?>
                            <option value="<?= $atividade->getId() ?>" <?= $atividadeSelecionada === $atividade->getId() ? 'selected' : '' ?>><?= htmlspecialchars($atividade->getNome()) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['atividade_id'])): ?><div class="erro"><?= htmlspecialchars($erros['atividade_id']) ?></div><?php endif; ?>
                </div>

                <?php if (isset($erros['conteudo'])): ?><div class="erro erro-conteudo"><?= htmlspecialchars($erros['conteudo']) ?></div><?php endif; ?>

                <fieldset class="grupo-pecas configuracao-simulacao" id="configuracao-peca">
                    <legend>Peça com defeito:</legend>
                    <?php foreach (['bateria' => 'Bateria', 'camera' => 'Câmera', 'chip' => 'Chip'] as $peca => $rotulo): ?>
                        <label><input type="radio" name="peca_quebrada" value="<?= $peca ?>" <?= $pecaQuebradaInicial === $peca ? 'checked' : '' ?>> <?= $rotulo ?></label>
                    <?php endforeach; ?>
                </fieldset>

                <div class="card-secundario configuracao-simulacao instrucao-simulacao">
                    <strong>Etapas definidas pelo sistema</strong>
                    <p>O administrador escolhe a peça defeituosa. O passo a passo, as ferramentas e as regras de conserto são adicionados automaticamente ao abrir a simulação.</p>
                </div>

                <div id="lista-itens" aria-live="polite"></div>

                <div class="acoes-formulario">
                    <button type="button" class="botao brilho" id="adicionar-exercicio">Adicionar questão</button>
                    <button type="submit" class="botao brilho">Salvar</button>
                </div>
            </form>

            <template id="modelo-exercicio">
                <fieldset class="card-secundario bloco-exercicio">
                    <div class="cabecalho-exercicio">
                        <legend>Questão <span class="numero-exercicio"></span></legend>
                        <button type="button" class="botao remover-exercicio">Remover questão</button>
                    </div>
                    <div class="formgroup">
                        <label>Enunciado:</label>
                        <textarea class="card-secundario campo-enunciado" required></textarea>
                    </div>
                    <div class="alternativas"></div>
                    <button type="button" class="botao brilho adicionar-alternativa">Adicionar alternativa</button>
                    <div class="formgroup">
                        <label>Justificativa da alternativa correta:</label>
                        <textarea class="card-secundario campo-justificativa" required></textarea>
                    </div>
                </fieldset>
            </template>

            <script>
                window.tipoExercicioInicial = <?= json_encode($tipoSelecionado, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
                window.itensExercicioIniciais = <?= json_encode(array_values($itensIniciais), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            </script>
            <script src="<?= URL_BASE ?>/assets/javascript/exercicios.js"></script>
        </div>
    </main>
</body>
</html>
