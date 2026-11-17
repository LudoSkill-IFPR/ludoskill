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

        const atualizarTimer = () => {
            const segundos = Math.max(0, Math.ceil((final - Date.now()) / 1000));
            valorTimer.textContent = `${String(Math.floor(segundos / 60)).padStart(2, '0')}:${String(segundos % 60).padStart(2, '0')}`;
            cronometro.classList.toggle('timer-alerta', segundos > 0 && segundos <= 60);
            cronometro.classList.toggle('timer-esgotado', segundos === 0);
            if (segundos === 0) {
                rotulo.textContent = 'Tempo esgotado';
                mensagem.textContent = 'Você ainda pode concluir, mas esta tentativa não receberá estrelas.';
                if (intervalo) window.clearInterval(intervalo);
            }
        };

        atualizarTimer();
        intervalo = window.setInterval(atualizarTimer, 1000);
    }

    const ferramentasPermitidas = ['pinca', 'aquecedor', 'solda', 'palheta', 'alcool'];

    const criarEstado = (pecaQuebrada) => {
        const componentes = { bateria: true, camera: true, chip: true };
        componentes[pecaQuebrada] = false;
        return {
            componentes,
            removidos: { bateria: false, camera: false, chip: false },
            ferramenta: '',
            tampaRemovida: false,
            tampaFoiRemovida: false,
            colaTampaAquecida: false,
            colaBateriaSolta: false,
            soldaChipSolta: false,
            concluida: false,
            erros: 0,
        };
    };

    const registrarErro = (estado, mensagem) => {
        estado.erros++;
        return { mensagem, erro: true };
    };

    const executarAcao = (estado, acao) => {
        const partes = String(acao).split(':');
        const tipo = partes[0] || '';
        const alvo = partes[1] || '';

        if (tipo === 'ferramenta' && ferramentasPermitidas.includes(alvo)) {
            estado.ferramenta = alvo;
            return { mensagem: 'Ferramenta selecionada. Agora interaja com a peça adequada.', erro: false };
        }

        if (tipo === 'interagir' && alvo === 'tampa') {
            if (!estado.tampaRemovida && estado.ferramenta === 'aquecedor') {
                estado.colaTampaAquecida = true;
                return { mensagem: 'A cola da tampa foi aquecida. Use a palheta para removê-la.', erro: false };
            }
            if (!estado.tampaRemovida && estado.ferramenta === 'palheta' && estado.colaTampaAquecida) {
                estado.tampaRemovida = true;
                estado.tampaFoiRemovida = true;
                return { mensagem: 'Tampa removida. Agora conserte o componente defeituoso.', erro: false };
            }
            if (estado.tampaRemovida && estado.ferramenta === 'aquecedor') {
                estado.componentes.bateria = false;
                return registrarErro(estado, 'O calor atingiu a bateria e a danificou. Você precisará substituí-la.');
            }
            return registrarErro(estado, 'A tampa precisa ser aquecida e depois removida com a palheta.');
        }

        if (tipo === 'interagir' && alvo in estado.componentes) {
            if (!estado.tampaRemovida) return registrarErro(estado, 'Remova a tampa antes de acessar os componentes.');
            if (estado.removidos[alvo]) return registrarErro(estado, 'Esse componente já foi retirado.');

            if (alvo === 'bateria') {
                if (estado.ferramenta === 'aquecedor') {
                    estado.componentes.bateria = false;
                    return registrarErro(estado, 'A bateria foi danificada pelo calor. Use álcool e palheta para retirá-la.');
                }
                if (estado.ferramenta === 'alcool') {
                    estado.colaBateriaSolta = true;
                    return { mensagem: 'A cola da bateria foi dissolvida. Agora use a palheta.', erro: false };
                }
                if (estado.ferramenta === 'palheta' && estado.colaBateriaSolta && !estado.componentes.bateria) {
                    estado.removidos.bateria = true;
                    return { mensagem: 'Bateria retirada. Insira uma bateria nova.', erro: false };
                }
                return registrarErro(estado, estado.componentes.bateria
                    ? 'A bateria está funcionando e não precisa ser retirada.'
                    : 'Dissolva a cola com álcool e retire a bateria usando a palheta.');
            }

            if (alvo === 'camera') {
                if (estado.ferramenta === 'aquecedor') {
                    estado.componentes.camera = false;
                    return registrarErro(estado, 'O calor danificou a câmera. Agora ela também precisa ser substituída.');
                }
                if (estado.ferramenta === 'pinca' && !estado.componentes.camera) {
                    estado.removidos.camera = true;
                    return { mensagem: 'Câmera retirada. Insira uma câmera nova.', erro: false };
                }
                return registrarErro(estado, estado.componentes.camera
                    ? 'A câmera está funcionando e não precisa ser retirada.'
                    : 'Use a pinça para retirar a câmera defeituosa.');
            }

            if (alvo === 'chip') {
                if (estado.ferramenta === 'solda') {
                    const estavaFuncionando = estado.componentes.chip;
                    estado.componentes.chip = false;
                    estado.soldaChipSolta = true;
                    if (estavaFuncionando) {
                        return registrarErro(estado, 'O chip estava funcionando e foi danificado pela solda. Agora substitua-o.');
                    }
                    return { mensagem: 'A solda do chip foi liberada. Agora use a pinça.', erro: false };
                }
                if (estado.ferramenta === 'pinca' && estado.soldaChipSolta && !estado.componentes.chip) {
                    estado.removidos.chip = true;
                    return { mensagem: 'Chip retirado. Insira um chip novo.', erro: false };
                }
                return registrarErro(estado, estado.componentes.chip
                    ? 'O chip está funcionando e não precisa ser retirado.'
                    : 'Solte a solda e depois use a pinça para retirar o chip.');
            }
        }

        if (tipo === 'instalar' && alvo in estado.componentes) {
            if (estado.tampaRemovida && estado.removidos[alvo]) {
                estado.removidos[alvo] = false;
                estado.componentes[alvo] = true;
                if (alvo === 'bateria') estado.colaBateriaSolta = false;
                if (alvo === 'chip') estado.soldaChipSolta = false;
                return { mensagem: 'Componente novo instalado corretamente.', erro: false };
            }
            return registrarErro(estado, 'Retire o componente danificado antes de inserir uma peça nova.');
        }

        if (tipo === 'instalar' && alvo === 'tampa') {
            const algumaRemovida = Object.values(estado.removidos).includes(true);
            const algumaQuebrada = Object.values(estado.componentes).includes(false);
            if (estado.tampaRemovida && !algumaRemovida && !algumaQuebrada) {
                estado.tampaRemovida = false;
                estado.concluida = true;
                return { mensagem: 'Aparelho consertado e fechado. Você pode concluir a simulação.', erro: false };
            }
            return registrarErro(estado, 'Ainda existe um componente quebrado ou fora do aparelho. Termine os reparos antes de fechar.');
        }

        return registrarErro(estado, 'Essa ação não pode ser realizada agora.');
    };

    const finalizada = form.dataset.finalizada === '1';
    const chave = form.dataset.storageKey;
    const botaoFinalizar = document.getElementById('finalizar-simulacao');
    const tabuleiros = [...form.querySelectorAll('.simulacao-interativa')];
    let armazenadas = {};
    try { armazenadas = JSON.parse(localStorage.getItem(chave) || '{}'); } catch (_) { armazenadas = {}; }

    const atualizarBotaoFinalizar = () => {
        if (botaoFinalizar) {
            botaoFinalizar.disabled = tabuleiros.some((tabuleiro) => tabuleiro.dataset.concluida !== '1');
        }
    };

    const prepararTabuleiro = (tabuleiro) => {
        const id = tabuleiro.dataset.id;
        const defeito = tabuleiro.dataset.defeito;
        let acoesServidor = [];
        try { acoesServidor = JSON.parse(tabuleiro.dataset.acoes || '[]'); } catch (_) { acoesServidor = []; }
        const acoes = Array.isArray(armazenadas[id]) && acoesServidor.length === 0 ? armazenadas[id] : acoesServidor;
        const estado = criarEstado(defeito);
        const input = tabuleiro.querySelector('.registro-acoes');
        const progresso = tabuleiro.querySelector('.progresso-simulacao');
        const mensagem = tabuleiro.querySelector('.mensagem-acao');

        acoes.forEach((acao) => executarAcao(estado, acao));

        const atualizarVisual = () => {
            input.value = JSON.stringify(acoes);
            armazenadas[id] = acoes;
            if (!finalizada) localStorage.setItem(chave, JSON.stringify(armazenadas));

            tabuleiro.classList.toggle('tampa-removida', estado.tampaRemovida);
            tabuleiro.classList.toggle('simulacao-concluida', estado.concluida);

            ['bateria', 'camera', 'chip'].forEach((componente) => {
                const botaoComponente = tabuleiro.querySelector(`.componente-${componente}`);
                botaoComponente.classList.toggle('componente-removido', estado.removidos[componente]);
                botaoComponente.classList.toggle('componente-defeituoso', !estado.componentes[componente] && !estado.removidos[componente]);

                const linhaEstado = tabuleiro.querySelector(`[data-estado-componente="${componente}"]`);
                const texto = estado.removidos[componente]
                    ? 'Fora do aparelho'
                    : (estado.componentes[componente] ? 'Funcionando' : 'Com defeito');
                linhaEstado.querySelector('strong').textContent = texto;
                linhaEstado.className = estado.componentes[componente] ? 'estado-ok' : 'estado-problema';
            });

            tabuleiro.querySelectorAll('.ferramenta-simulacao').forEach((botao) => {
                botao.classList.toggle('ferramenta-selecionada', botao.dataset.acao === `ferramenta:${estado.ferramenta}`);
            });

            tabuleiro.querySelector('.contador-erros strong').textContent = estado.erros;
            progresso.textContent = estado.concluida ? 'Manutenção concluída' : 'Manutenção em andamento';
            tabuleiro.dataset.concluida = estado.concluida ? '1' : '0';

            if (estado.concluida || finalizada) {
                tabuleiro.querySelectorAll('[data-acao]').forEach((botao) => { botao.disabled = true; });
            }
            atualizarBotaoFinalizar();
        };

        if (!finalizada) {
            tabuleiro.querySelectorAll('[data-acao]').forEach((botao) => {
                botao.addEventListener('click', () => {
                    const resultado = executarAcao(estado, botao.dataset.acao);
                    acoes.push(botao.dataset.acao);
                    mensagem.textContent = resultado.mensagem;
                    mensagem.className = resultado.erro ? 'mensagem-acao acao-incorreta' : 'mensagem-acao acao-correta';
                    if (resultado.erro) {
                        tabuleiro.classList.remove('erro-acao');
                        window.requestAnimationFrame(() => tabuleiro.classList.add('erro-acao'));
                    }
                    atualizarVisual();
                });
            });
        }

        atualizarVisual();
    };

    tabuleiros.forEach(prepararTabuleiro);
    if (finalizada) localStorage.removeItem(chave);

    form.addEventListener('submit', (evento) => {
        if (tabuleiros.some((tabuleiro) => tabuleiro.dataset.concluida !== '1')) {
            evento.preventDefault();
            window.alert('Conserte todos os componentes e recoloque a tampa antes de concluir.');
            return;
        }
        form.dataset.enviando = '1';
    });

    document.getElementById('voltar-conteudo')?.addEventListener('click', () => { form.dataset.enviando = '1'; });
    window.addEventListener('beforeunload', (evento) => {
        if (!finalizada && form.dataset.enviando !== '1') {
            evento.preventDefault();
            evento.returnValue = '';
        }
    });
});
