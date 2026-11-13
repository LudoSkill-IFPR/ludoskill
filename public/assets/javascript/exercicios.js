document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-exercicio');
    const lista = document.getElementById('lista-itens');
    const tipo = document.getElementById('tipo');
    const modeloQuestao = document.getElementById('modelo-exercicio');
    const modeloSimulacao = document.getElementById('modelo-simulacao');
    const botaoAdicionar = document.getElementById('adicionar-exercicio');
    const configuracoesSimulacao = [...document.querySelectorAll('.configuracao-simulacao')];
    if (!formulario || !lista || !tipo || !modeloQuestao || !modeloSimulacao || !botaoAdicionar) return;

    const reindexar = () => {
        const questoes = [...lista.querySelectorAll('.bloco-exercicio')];
        questoes.forEach((bloco, indiceQuestao) => {
            bloco.querySelector('.numero-exercicio').textContent = indiceQuestao + 1;
            bloco.querySelector('.campo-enunciado').name = `questoes[${indiceQuestao}][enunciado]`;
            bloco.querySelector('.campo-justificativa').name = `questoes[${indiceQuestao}][justificativa]`;
            bloco.querySelectorAll('.linha-alternativa').forEach((linha, indiceAlternativa) => {
                const radio = linha.querySelector('.alternativa-correta');
                const input = linha.querySelector('.campo-alternativa');
                const idRadio = `correta-${indiceQuestao}-${indiceAlternativa}`;
                radio.id = idRadio;
                radio.name = `questoes[${indiceQuestao}][alternativa_correta]`;
                radio.value = indiceAlternativa;
                input.name = `questoes[${indiceQuestao}][alternativas][${indiceAlternativa}]`;
                linha.querySelector('label').htmlFor = idRadio;
            });
        });

        const simulacoes = [...lista.querySelectorAll('.bloco-simulacao')];
        simulacoes.forEach((bloco, indiceSimulacao) => {
            bloco.querySelector('.numero-simulacao').textContent = indiceSimulacao + 1;
            bloco.querySelector('.campo-descricao-simulacao').name = `etapas[${indiceSimulacao}][descricao]`;
        });

        lista.querySelectorAll('.remover-exercicio, .remover-simulacao').forEach((botao) => {
            botao.disabled = lista.children.length === 1;
        });
    };

    const adicionarAlternativa = (bloco, texto = '', correta = false) => {
        const alternativas = bloco.querySelector('.alternativas');
        const linha = document.createElement('div');
        linha.className = 'linha-alternativa';

        const radio = document.createElement('input');
        radio.className = 'alternativa-correta';
        radio.type = 'radio';
        radio.required = true;
        radio.checked = correta;

        const label = document.createElement('label');
        label.textContent = 'Correta';

        const input = document.createElement('input');
        input.className = 'card-secundario campo-alternativa';
        input.type = 'text';
        input.placeholder = 'Texto da alternativa';
        input.required = true;
        input.value = texto;

        const remover = document.createElement('button');
        remover.className = 'botao remover-alternativa';
        remover.type = 'button';
        remover.textContent = 'Remover';
        remover.addEventListener('click', () => {
            if (alternativas.children.length <= 2) {
                window.alert('Cada questão deve possuir pelo menos duas alternativas.');
                return;
            }
            linha.remove();
            reindexar();
        });

        linha.append(radio, label, input, remover);
        alternativas.appendChild(linha);
        reindexar();
    };

    const adicionarQuestao = (dados = {}) => {
        const bloco = modeloQuestao.content.firstElementChild.cloneNode(true);
        bloco.querySelector('.campo-enunciado').value = dados.enunciado ?? '';
        bloco.querySelector('.campo-justificativa').value = dados.justificativa ?? '';
        bloco.querySelector('.adicionar-alternativa').addEventListener('click', () => adicionarAlternativa(bloco));
        bloco.querySelector('.remover-exercicio').addEventListener('click', () => {
            bloco.remove();
            reindexar();
        });
        lista.appendChild(bloco);

        const alternativas = Array.isArray(dados.alternativas) && dados.alternativas.length >= 2
            ? dados.alternativas
            : ['', ''];
        const correta = Number.isInteger(dados.alternativa_correta)
            ? dados.alternativa_correta
            : Number.parseInt(dados.alternativa_correta, 10);
        alternativas.forEach((alternativa, indice) => adicionarAlternativa(bloco, alternativa, indice === correta));
        reindexar();
    };

    const adicionarSimulacao = (dados = {}) => {
        const bloco = modeloSimulacao.content.firstElementChild.cloneNode(true);
        bloco.querySelector('.campo-descricao-simulacao').value = dados.descricao ?? '';
        bloco.querySelector('.remover-simulacao').addEventListener('click', () => {
            bloco.remove();
            reindexar();
        });
        lista.appendChild(bloco);
        reindexar();
    };

    const atualizarBotao = () => {
        const simulacao = tipo.value === 'simulacao';
        botaoAdicionar.textContent = simulacao ? 'Adicionar etapa' : 'Adicionar questão';
        configuracoesSimulacao.forEach((configuracao) => {
            configuracao.hidden = !simulacao;
            configuracao.querySelectorAll('input').forEach((input) => {
                input.disabled = !simulacao;
            });
        });
    };

    const adicionarItem = (dados = {}) => {
        if (tipo.value === 'simulacao') adicionarSimulacao(dados);
        else adicionarQuestao(dados);
    };

    let tipoAtual = ['s', 'simulacao'].includes(window.tipoExercicioInicial) ? 'simulacao' : 'exercicio';
    tipo.value = tipoAtual;
    atualizarBotao();

    const iniciais = Array.isArray(window.itensExercicioIniciais) && window.itensExercicioIniciais.length
        ? window.itensExercicioIniciais
        : [{}];
    iniciais.forEach(adicionarItem);

    botaoAdicionar.addEventListener('click', () => adicionarItem());
    tipo.addEventListener('change', () => {
        if (!window.confirm('Alterar o tipo removerá os itens preenchidos. Deseja continuar?')) {
            tipo.value = tipoAtual;
            return;
        }
        tipoAtual = tipo.value;
        lista.replaceChildren();
        atualizarBotao();
        adicionarItem();
    });

    formulario.addEventListener('submit', (evento) => {
        if (tipo.value !== 'simulacao') return;
        const pecaQuebrada = formulario.querySelector('input[name="peca_quebrada"]:checked');
        if (!pecaQuebrada) {
            evento.preventDefault();
            window.alert('Selecione a peça com defeito na simulação.');
        }
    });
});
