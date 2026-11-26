document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-simulacao');

    if (!form) {
        console.error('Formulário da simulação não encontrado.');
        return;
    }

    const botoesFinalizar = document.getElementById('finalizar-simulacao');
    const tabuleiros = Array.from(
        form.querySelectorAll('.simulacao-interativa')
    );

    const finalizada = form.dataset.finalizada === '1';
    const storageKey = form.dataset.storageKey;

    const ferramentasPermitidas = [
        'pinca',
        'aquecedor',
        'solda',
        'palheta',
        'alcool'
    ];

    const componentesPermitidos = [
        'bateria',
        'camera',
        'chip'
    ];

    let dadosSalvos = {};

    try {
        dadosSalvos = JSON.parse(
            localStorage.getItem(storageKey) || '{}'
        );
    } catch (erro) {
        dadosSalvos = {};
    }

    // --------------------------------------------------
    // CRIAÇÃO DO ESTADO INICIAL
    // --------------------------------------------------

    function criarEstado(defeito) {
        const componentes = {
            bateria: true,
            camera: true,
            chip: true
        };

        if (componentesPermitidos.includes(defeito)) {
            componentes[defeito] = false;
        }

        return {
            componentes,
            removidos: {
                bateria: false,
                camera: false,
                chip: false
            },
            ferramenta: null,
            tampaRemovida: false,
            tampaAquecida: false,
            colaBateriaSolta: false,
            soldaChipSolta: false,
            concluida: false,
            erros: 0
        };
    }

    // --------------------------------------------------
    // REGISTRO DE ERROS
    // --------------------------------------------------

    function registrarErro(estado, mensagem) {
        estado.erros++;

        return {
            mensagem,
            erro: true
        };
    }

    // --------------------------------------------------
    // EXECUÇÃO DAS AÇÕES
    // --------------------------------------------------

    function executarAcao(estado, acao) {
        const partes = String(acao).split(':');

        const tipo = partes[0];
        const alvo = partes[1];

        // Selecionar ferramenta
        if (
            tipo === 'ferramenta' &&
            ferramentasPermitidas.includes(alvo)
        ) {
            estado.ferramenta = alvo;

            return {
                mensagem: 'Ferramenta selecionada.',
                erro: false
            };
        }

        // Interação com a tampa
        if (tipo === 'interagir' && alvo === 'tampa') {

            if (
                estado.ferramenta === 'aquecedor' &&
                !estado.tampaRemovida
            ) {
                estado.tampaAquecida = true;

                return {
                    mensagem: 'Tampa aquecida. Utilize a palheta para removê-la.',
                    erro: false
                };
            }

            if (
                estado.ferramenta === 'palheta' &&
                estado.tampaAquecida &&
                !estado.tampaRemovida
            ) {
                estado.tampaRemovida = true;

                return {
                    mensagem: 'Tampa removida com sucesso.',
                    erro: false
                };
            }

            return registrarErro(
                estado,
                'Aqueça a tampa e depois utilize a palheta.'
            );
        }

        // Interação com componentes
        if (
            tipo === 'interagir' &&
            componentesPermitidos.includes(alvo)
        ) {
            if (!estado.tampaRemovida) {
                return registrarErro(
                    estado,
                    'Primeiro remova a tampa traseira.'
                );
            }

            if (estado.removidos[alvo]) {
                return registrarErro(
                    estado,
                    'Esse componente já foi removido.'
                );
            }

            // BATERIA
            if (alvo === 'bateria') {

                if (estado.ferramenta === 'alcool') {
                    estado.colaBateriaSolta = true;

                    return {
                        mensagem: 'Cola da bateria dissolvida. Utilize a palheta.',
                        erro: false
                    };
                }

                if (
                    estado.ferramenta === 'palheta' &&
                    estado.colaBateriaSolta &&
                    !estado.componentes.bateria
                ) {
                    estado.removidos.bateria = true;

                    return {
                        mensagem: 'Bateria removida.',
                        erro: false
                    };
                }

                if (estado.ferramenta === 'aquecedor') {
                    estado.componentes.bateria = false;

                    return registrarErro(
                        estado,
                        'Você danificou a bateria com o calor!'
                    );
                }

                return registrarErro(
                    estado,
                    'Utilize álcool e palheta para remover a bateria.'
                );
            }

            // CÂMERA
            if (alvo === 'camera') {

                if (
                    estado.ferramenta === 'pinca' &&
                    !estado.componentes.camera
                ) {
                    estado.removidos.camera = true;

                    return {
                        mensagem: 'Câmera removida.',
                        erro: false
                    };
                }

                if (estado.ferramenta === 'aquecedor') {
                    estado.componentes.camera = false;

                    return registrarErro(
                        estado,
                        'A câmera foi danificada pelo calor!'
                    );
                }

                return registrarErro(
                    estado,
                    'Utilize a pinça para remover a câmera defeituosa.'
                );
            }

            // CHIP
            if (alvo === 'chip') {

                if (estado.ferramenta === 'solda') {
                    estado.componentes.chip = false;
                    estado.soldaChipSolta = true;

                    return {
                        mensagem: 'Solda do chip removida. Utilize a pinça.',
                        erro: false
                    };
                }

                if (
                    estado.ferramenta === 'pinca' &&
                    estado.soldaChipSolta &&
                    !estado.componentes.chip
                ) {
                    estado.removidos.chip = true;

                    return {
                        mensagem: 'Chip removido.',
                        erro: false
                    };
                }

                return registrarErro(
                    estado,
                    'Utilize o ferro de solda e depois a pinça.'
                );
            }
        }

        // Instalação de componentes novos
        if (
            tipo === 'instalar' &&
            componentesPermitidos.includes(alvo)
        ) {
            if (
                estado.tampaRemovida &&
                estado.removidos[alvo]
            ) {
                estado.removidos[alvo] = false;
                estado.componentes[alvo] = true;

                return {
                    mensagem: 'Novo componente instalado.',
                    erro: false
                };
            }

            return registrarErro(
                estado,
                'Remova o componente antigo antes de instalar o novo.'
            );
        }

        // Recolocar tampa
        if (tipo === 'instalar' && alvo === 'tampa') {

            const componentesRemovidos = Object.values(
                estado.removidos
            ).some(valor => valor);

            const componentesDanificados = Object.values(
                estado.componentes
            ).some(valor => !valor);

            if (
                estado.tampaRemovida &&
                !componentesRemovidos &&
                !componentesDanificados
            ) {
                estado.tampaRemovida = false;
                estado.concluida = true;

                return {
                    mensagem: 'Manutenção concluída! Aparelho montado corretamente.',
                    erro: false
                };
            }

            return registrarErro(
                estado,
                'Ainda existem componentes que precisam de manutenção.'
            );
        }

        return registrarErro(
            estado,
            'Ação inválida.'
        );
    }

    // --------------------------------------------------
    // ATUALIZAÇÃO DA INTERFACE
    // --------------------------------------------------

    function atualizarInterface(tabuleiro, estado, acoes) {

        const input = tabuleiro.querySelector('.registro-acoes');

        if (input) {
            input.value = JSON.stringify(acoes);
        }

        // Estado da tampa
        tabuleiro.classList.toggle(
            'tampa-removida',
            estado.tampaRemovida
        );

        tabuleiro.classList.toggle(
            'simulacao-concluida',
            estado.concluida
        );

        // Troca da imagem do verso conforme componentes removidos
        // (portado do tela.js antigo: pelado.png -> celular-sem-X.png)
        const estrutura = tabuleiro.querySelector(
            '.face.verso .estrutura-celular'
        );

        if (estrutura) {
            let urlImagem = estrutura.dataset.imgDefault || '';

            if (estado.removidos.bateria) {
                urlImagem = estrutura.dataset.imgSemBateria || urlImagem;
            } else if (estado.removidos.camera) {
                urlImagem = estrutura.dataset.imgSemCamera || urlImagem;
            } else if (estado.removidos.chip) {
                urlImagem = estrutura.dataset.imgSemChip || urlImagem;
            }

            if (urlImagem) {
                estrutura.style.backgroundImage = `url("${urlImagem}")`;
            }
        }

        // Componentes
        componentesPermitidos.forEach(componente => {

            const elemento = tabuleiro.querySelector(
                '.componente-' + componente
            );

            if (elemento) {
                elemento.classList.toggle(
                    'componente-removido',
                    estado.removidos[componente]
                );

                elemento.classList.toggle(
                    'componente-defeituoso',
                    !estado.componentes[componente] &&
                    !estado.removidos[componente]
                );
            }

            const linha = tabuleiro.querySelector(
                '[data-estado-componente="' + componente + '"]'
            );

            if (linha) {
                let texto = 'Funcionando';

                if (estado.removidos[componente]) {
                    texto = 'Removido';
                } else if (!estado.componentes[componente]) {
                    texto = 'Com defeito';
                }

                const strong = linha.querySelector('strong');

                if (strong) {
                    strong.textContent = texto;
                }

                linha.classList.toggle(
                    'estado-ok',
                    estado.componentes[componente] &&
                    !estado.removidos[componente]
                );

                linha.classList.toggle(
                    'estado-problema',
                    !estado.componentes[componente] ||
                    estado.removidos[componente]
                );
            }
        });

        // Ferramenta selecionada
        tabuleiro.querySelectorAll(
            '.ferramenta-simulacao'
        ).forEach(botao => {

            botao.classList.toggle(
                'ferramenta-selecionada',
                botao.dataset.acao ===
                'ferramenta:' + estado.ferramenta
            );
        });

        // Erros
        const contador = tabuleiro.querySelector(
            '.contador-erros strong'
        );

        if (contador) {
            contador.textContent = estado.erros;
        }

        // Progresso
        const progresso = tabuleiro.querySelector(
            '.progresso-simulacao'
        );

        if (progresso) {
            progresso.textContent = estado.concluida
                ? 'Manutenção concluída'
                : 'Manutenção em andamento';
        }

        tabuleiro.dataset.concluida =
            estado.concluida ? '1' : '0';

        // Mensagem
        if (estado.concluida) {
            const mensagem = tabuleiro.querySelector(
                '.mensagem-acao'
            );

            if (mensagem) {
                mensagem.textContent =
                    'Aparelho consertado corretamente!';
            }
        }

        if (estado.concluida || finalizada) {
            tabuleiro.querySelectorAll('[data-acao]').forEach(botao => {
                botao.disabled = true;
            });
        }

        // Persistência local
        dadosSalvos[tabuleiro.dataset.id] = acoes;

        if (!finalizada && storageKey) {
            try {
                localStorage.setItem(
                    storageKey,
                    JSON.stringify(dadosSalvos)
                );
            } catch (erro) {
                console.warn('Não foi possível salvar o progresso local.', erro);
            }
        }

        atualizarBotaoFinalizar();
    }

    // --------------------------------------------------
    // BOTÃO DE CONCLUSÃO
    // --------------------------------------------------

    function atualizarBotaoFinalizar() {

        if (!botoesFinalizar) {
            return;
        }

        const todosConcluidos = tabuleiros.every(
            tabuleiro => tabuleiro.dataset.concluida === '1'
        );

        botoesFinalizar.disabled =
            !todosConcluidos || finalizada;
    }

    // --------------------------------------------------
    // ROTAÇÃO 3D DO CELULAR
    // --------------------------------------------------

    function configurarRotacao(tabuleiro) {

    const palco = tabuleiro.querySelector('.palco-tela');
    const tela = tabuleiro.querySelector('.tela3d');

    if (!palco || !tela) {
        return;
    }

    let arrastando = false;
    let rotX = 18;
    let rotY = 0;
    let startX = 0;
    let startY = 0;

    function atualizar() {
        tela.style.transform =
            `rotateX(${rotX}deg) rotateY(${rotY}deg)`;
    }

    atualizar();

    palco.addEventListener('pointerdown', evento => {

        if (evento.target.closest('button')) {
            return;
        }

        arrastando = true;
        startX = evento.clientX;
        startY = evento.clientY;

        if (palco.setPointerCapture) {
            palco.setPointerCapture(evento.pointerId);
        }
    });

    palco.addEventListener('pointermove', evento => {

        if (!arrastando) {
            return;
        }

        const deltaX = evento.clientX - startX;
        const deltaY = evento.clientY - startY;

        rotY += deltaX * 0.5;
        rotX -= deltaY * 0.5;

        tela.style.transform =
            `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

        startX = evento.clientX;
        startY = evento.clientY;
    });

    function pararArraste() {
        arrastando = false;
    }

    palco.addEventListener('pointerup', pararArraste);
    palco.addEventListener('pointercancel', pararArraste);
    palco.addEventListener('lostpointercapture', pararArraste);
}

    // --------------------------------------------------
    // INICIALIZAÇÃO DE CADA EXERCÍCIO
    // --------------------------------------------------

    function inicializarTabuleiro(tabuleiro) {

        const id = tabuleiro.dataset.id;
        const defeito = tabuleiro.dataset.defeito;

        const input = tabuleiro.querySelector('.registro-acoes');
        const mensagem = tabuleiro.querySelector('.mensagem-acao');

        let acoesServidor = [];

        try {
            acoesServidor = JSON.parse(
                tabuleiro.dataset.acoes || '[]'
            );
        } catch (erro) {
            console.error('Erro ao ler ações anteriores:', erro);
        }

        let acoes = acoesServidor;

        if (
            Array.isArray(dadosSalvos[id]) &&
            acoesServidor.length === 0
        ) {
            acoes = dadosSalvos[id];
        }

        if (!Array.isArray(acoes)) {
            acoes = [];
        }

        const estado = criarEstado(defeito);

        // Reconstruir o estado usando as ações anteriores
        acoes.forEach(acao => {
            executarAcao(estado, acao);
        });

        if (input) {
            input.value = JSON.stringify(acoes);
        }

        if (!finalizada) {

            tabuleiro.querySelectorAll('[data-acao]').forEach(botao => {

                botao.addEventListener('click', () => {

                    if (estado.concluida) {
                        return;
                    }

                    const acao = botao.dataset.acao;

                    const resultado = executarAcao(
                        estado,
                        acao
                    );

                    acoes.push(acao);

                    if (mensagem) {
                        mensagem.textContent = resultado.mensagem;

                        mensagem.className = resultado.erro
                            ? 'mensagem-acao acao-incorreta'
                            : 'mensagem-acao acao-correta';
                    }

                    atualizarInterface(
                        tabuleiro,
                        estado,
                        acoes
                    );
                });
            });
        }

        configurarRotacao(tabuleiro);

        atualizarInterface(
            tabuleiro,
            estado,
            acoes
        );
    }

    // --------------------------------------------------
    // CRONÔMETRO
    // --------------------------------------------------

    function inicializarCronometro() {

        const cronometro = document.getElementById('cronometro-atividade');

        if (!cronometro) {
            return;
        }

        const valorEl = document.getElementById('valor-timer');
        const rotuloEl = document.getElementById('rotulo-timer');
        const mensagemEl = document.getElementById('mensagem-timer');

        if (!valorEl) {
            return;
        }

        let segundos = parseInt(valorEl.dataset.segundos, 10);

        if (Number.isNaN(segundos) || segundos < 0) {
            segundos = 0;
        }

        function formatar(valor) {
            const minutos = Math.floor(valor / 60);
            const resto = valor % 60;

            return String(minutos).padStart(2, '0') + ':' +
                   String(resto).padStart(2, '0');
        }

        function atualizar() {
            valorEl.textContent = formatar(segundos);

            cronometro.classList.toggle(
                'timer-alerta',
                segundos > 0 && segundos <= 60
            );

            cronometro.classList.toggle(
                'timer-esgotado',
                segundos <= 0
            );

            if (segundos <= 0) {
                if (rotuloEl) rotuloEl.textContent = 'Tempo esgotado';
                if (mensagemEl) {
                    mensagemEl.textContent =
                        'Tempo esgotado. A entrega não receberá estrelas.';
                }
            } else if (segundos <= 60) {
                if (rotuloEl) rotuloEl.textContent = 'Falta pouco tempo';
                if (mensagemEl) {
                    mensagemEl.textContent =
                        'Conclua a manutenção rapidamente!';
                }
            } else {
                if (rotuloEl) rotuloEl.textContent = 'Tempo restante';
                if (mensagemEl) {
                    mensagemEl.textContent =
                        'Conclua no tempo para receber todas as estrelas.';
                }
            }
        }

        atualizar();

        const intervalo = setInterval(() => {
            if (segundos > 0) {
                segundos--;
                atualizar();
            }

            if (segundos <= 0) {
                clearInterval(intervalo);
            }
        }, 1000);
    }

    // --------------------------------------------------
    // INICIALIZAÇÃO GERAL
    // --------------------------------------------------

    inicializarCronometro();

    tabuleiros.forEach(inicializarTabuleiro);

    if (finalizada && storageKey) {
        try {
            localStorage.removeItem(storageKey);
        } catch (erro) {
            console.warn(erro);
        }
    }

    // --------------------------------------------------
    // ENVIO DO FORMULÁRIO
    // --------------------------------------------------

    form.addEventListener('submit', evento => {

        const todosConcluidos = tabuleiros.every(
            tabuleiro => tabuleiro.dataset.concluida === '1'
        );

        if (!todosConcluidos) {
            evento.preventDefault();

            alert(
                'Conserte todos os componentes e recoloque a tampa antes de concluir.'
            );

            return;
        }

        form.dataset.enviando = '1';
    });

    const voltar = document.getElementById('voltar-conteudo');

    if (voltar) {
        voltar.addEventListener('click', () => {
            form.dataset.enviando = '1';
        });
    }

    window.addEventListener('beforeunload', evento => {

        if (
            !finalizada &&
            form.dataset.enviando !== '1'
        ) {
            evento.preventDefault();
            evento.returnValue = '';
        }
    });

});
