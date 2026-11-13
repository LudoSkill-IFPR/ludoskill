document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-simulacao');
    if (!form) return;

    const valorTimer = document.getElementById('valor-timer');
    const cronometro = document.getElementById('cronometro-atividade');
    if (valorTimer && cronometro) {
        const rotulo = document.getElementById('rotulo-timer');
        const mensagem = document.getElementById('mensagem-timer');
        const final = Date.now() + Math.max(0, Number.parseInt(valorTimer.dataset.segundos || '0', 10)) * 1000;
        let intervalo;
        const atualizar = () => {
            const segundos = Math.max(0, Math.ceil((final - Date.now()) / 1000));
            valorTimer.textContent = `${String(Math.floor(segundos / 60)).padStart(2, '0')}:${String(segundos % 60).padStart(2, '0')}`;
            cronometro.classList.toggle('timer-alerta', segundos > 0 && segundos <= 60);
            cronometro.classList.toggle('timer-esgotado', segundos === 0);
            if (segundos === 0) {
                rotulo.textContent = 'Tempo esgotado';
                mensagem.textContent = 'Você ainda pode concluir, com desconto na recompensa.';
                if (intervalo) window.clearInterval(intervalo);
            }
        };
        atualizar();
        intervalo = window.setInterval(atualizar, 1000);
    }

    const finalizada = form.dataset.finalizada === '1';
    const chave = form.dataset.storageKey;
    const botaoFinalizar = document.getElementById('finalizar-simulacao');
    const tabuleiros = [...form.querySelectorAll('.simulacao-interativa')];
    let armazenadas = {};
    try { armazenadas = JSON.parse(localStorage.getItem(chave) || '{}'); } catch (_) { armazenadas = {}; }

    const aplicarEfeito = (tabuleiro, acao) => {
        if (acao === 'remover:tampa') tabuleiro.classList.add('tampa-removida');
        if (acao === 'instalar:tampa') tabuleiro.classList.remove('tampa-removida');
        if (acao === 'remover:protecao') tabuleiro.classList.add('protecao-removida');
        if (acao === 'instalar:protecao') tabuleiro.classList.remove('protecao-removida');
        if (/^remover:(bateria|camera|chip)$/.test(acao)) tabuleiro.classList.add('alvo-removido');
        if (/^instalar:(bateria|camera|chip)$/.test(acao)) tabuleiro.classList.remove('alvo-removido');
    };

    const prepararTabuleiro = (tabuleiro) => {
        let sequencia = [];
        let acoesServidor = [];
        try { sequencia = JSON.parse(tabuleiro.dataset.sequencia || '[]'); } catch (_) { sequencia = []; }
        try { acoesServidor = JSON.parse(tabuleiro.dataset.acoes || '[]'); } catch (_) { acoesServidor = []; }
        const id = tabuleiro.dataset.id;
        const acoes = Array.isArray(armazenadas[id]) && !acoesServidor.length ? armazenadas[id] : acoesServidor;
        const input = tabuleiro.querySelector('.registro-acoes');
        const progresso = tabuleiro.querySelector('.progresso-simulacao');
        const mensagem = tabuleiro.querySelector('.mensagem-acao');
        let indice = 0;
        let erros = 0;

        acoes.forEach((acao) => {
            if (sequencia[indice] === acao) {
                aplicarEfeito(tabuleiro, acao);
                indice++;
            } else {
                erros++;
            }
        });

        const atualizar = () => {
            input.value = JSON.stringify(acoes);
            armazenadas[id] = acoes;
            if (!finalizada) localStorage.setItem(chave, JSON.stringify(armazenadas));
            const concluida = indice === sequencia.length && sequencia.length > 0;
            progresso.textContent = concluida ? 'Manutenção concluída' : `Ação ${indice + 1} de ${sequencia.length}`;
            if (concluida) {
                mensagem.textContent = erros ? `Sequência concluída com ${erros} tentativa(s) incorreta(s).` : 'Sequência concluída sem erros.';
                tabuleiro.classList.add('simulacao-concluida');
                tabuleiro.querySelectorAll('.acao-simulacao').forEach((botao) => { botao.disabled = true; });
            }
            tabuleiro.dataset.concluida = concluida ? '1' : '0';
            if (botaoFinalizar) botaoFinalizar.disabled = tabuleiros.some((item) => item.dataset.concluida !== '1');
        };

        tabuleiro.querySelectorAll('.acao-simulacao').forEach((botao) => {
            botao.addEventListener('click', () => {
                const acao = botao.dataset.acao;
                acoes.push(acao);
                if (sequencia[indice] === acao) {
                    aplicarEfeito(tabuleiro, acao);
                    indice++;
                    mensagem.textContent = 'Ação correta. Continue a manutenção.';
                    mensagem.className = 'mensagem-acao acao-correta';
                } else {
                    erros++;
                    mensagem.textContent = 'Essa não é a próxima ação. Consulte as etapas e tente novamente.';
                    mensagem.className = 'mensagem-acao acao-incorreta';
                    tabuleiro.classList.remove('erro-acao');
                    window.requestAnimationFrame(() => tabuleiro.classList.add('erro-acao'));
                }
                atualizar();
            });
        });
        atualizar();
    };

    tabuleiros.forEach(prepararTabuleiro);
    if (finalizada) localStorage.removeItem(chave);

    form.addEventListener('submit', (event) => {
        if (tabuleiros.some((tabuleiro) => tabuleiro.dataset.concluida !== '1')) {
            event.preventDefault();
            window.alert('Conclua todas as etapas práticas antes de finalizar.');
            return;
        }
        form.dataset.enviando = '1';
    });
    document.getElementById('voltar-conteudo')?.addEventListener('click', () => { form.dataset.enviando = '1'; });
    window.addEventListener('beforeunload', (event) => {
        if (!finalizada && form.dataset.enviando !== '1') {
            event.preventDefault();
            event.returnValue = '';
        }
    });
});
