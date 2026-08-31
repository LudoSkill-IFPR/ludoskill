document.addEventListener('DOMContentLoaded', () => {
    const lista = document.getElementById('lista-questoes');
    const modelo = document.getElementById('modelo-questao');
    const botaoAdicionar = document.getElementById('adicionar-questao');
    if (!lista || !modelo || !botaoAdicionar) return;

    const reindexar = () => {
        const questoes = [...lista.querySelectorAll('.bloco-questao')];
        questoes.forEach((bloco, indiceQuestao) => {
            bloco.querySelector('.numero-questao').textContent = indiceQuestao + 1;
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
        lista.querySelectorAll('.remover-questao').forEach((botao) => {
            botao.disabled = questoes.length === 1;
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
        const bloco = modelo.content.firstElementChild.cloneNode(true);
        bloco.querySelector('.campo-enunciado').value = dados.enunciado ?? '';
        bloco.querySelector('.campo-justificativa').value = dados.justificativa ?? '';
        bloco.querySelector('.adicionar-alternativa').addEventListener('click', () => adicionarAlternativa(bloco));
        bloco.querySelector('.remover-questao').addEventListener('click', () => {
            bloco.remove();
            reindexar();
        });
        lista.appendChild(bloco);
        const alternativas = Array.isArray(dados.alternativas) && dados.alternativas.length >= 2 ? dados.alternativas : ['', ''];
        const correta = Number.isInteger(dados.alternativa_correta) ? dados.alternativa_correta : Number.parseInt(dados.alternativa_correta, 10);
        alternativas.forEach((alternativa, indice) => adicionarAlternativa(bloco, alternativa, indice === correta));
        reindexar();
    };

    botaoAdicionar.addEventListener('click', () => adicionarQuestao());
    const iniciais = Array.isArray(window.questoesIniciais) && window.questoesIniciais.length ? window.questoesIniciais : [{}];
    iniciais.forEach(adicionarQuestao);
});
