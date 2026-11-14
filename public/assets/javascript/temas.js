(() => {
    const CHAVE_TEMA = 'ludoskill:tema';
    const temasValidos = ['padrao', 'escuro', 'claro'];
    const scriptAtual = document.currentScript;
    const homeUrl = scriptAtual?.dataset.homeUrl || '/';

    const aplicarTema = (tema) => {
        const temaValido = temasValidos.includes(tema) ? tema : 'padrao';
        document.documentElement.dataset.theme = temaValido;
        document.documentElement.style.colorScheme = temaValido === 'claro' ? 'light' : 'dark';
        document.querySelectorAll('[data-seletor-tema]').forEach((seletor) => {
            seletor.value = temaValido;
        });
        window.dispatchEvent(new CustomEvent('ludoskill:tema-alterado', { detail: { tema: temaValido } }));
    };

    let temaSalvo = 'padrao';
    try { temaSalvo = localStorage.getItem(CHAVE_TEMA) || 'padrao'; } catch (_) { temaSalvo = 'padrao'; }
    aplicarTema(temaSalvo);

    document.addEventListener('DOMContentLoaded', () => {
        aplicarTema(temaSalvo);
        document.querySelectorAll('[data-seletor-tema]').forEach((seletor) => {
            seletor.addEventListener('change', () => {
                temaSalvo = seletor.value;
                try { localStorage.setItem(CHAVE_TEMA, temaSalvo); } catch (_) {}
                aplicarTema(temaSalvo);
            });
        });

        const somenteDigitos = (valor) => (valor || '').replace(/\D/g, '');
        const cpfValido = (valor) => {
            const cpf = somenteDigitos(valor);
            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
            for (let digito = 9; digito < 11; digito++) {
                let soma = 0;
                for (let indice = 0; indice < digito; indice++) soma += Number(cpf[indice]) * ((digito + 1) - indice);
                let verificador = (10 * soma) % 11;
                if (verificador === 10) verificador = 0;
                if (Number(cpf[digito]) !== verificador) return false;
            }
            return true;
        };
        const ddds = new Set(['11','12','13','14','15','16','17','18','19','21','22','24','27','28','31','32','33','34','35','37','38','41','42','43','44','45','46','47','48','49','51','53','54','55','61','62','63','64','65','66','67','68','69','71','73','74','75','77','79','81','82','83','84','85','86','87','88','89','91','92','93','94','95','96','97','98','99']);
        const telefoneValido = (valor) => {
            const numero = somenteDigitos(valor);
            return ddds.has(numero.slice(0, 2)) && (/^[1-9]{2}[2-5]\d{7}$/.test(numero) || /^[1-9]{2}9[6-9]\d{7}$/.test(numero));
        };

        document.querySelectorAll('input[name="cpf"], input[name="CPF"]').forEach((input) => {
            input.inputMode = 'numeric';
            input.maxLength = 14;
            const validar = () => input.setCustomValidity(input.value && !cpfValido(input.value) ? 'Informe um CPF válido.' : '');
            input.addEventListener('input', () => {
                const valor = somenteDigitos(input.value).slice(0, 11);
                input.value = valor.replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1-$2');
                validar();
            });
            validar();
        });

        document.querySelectorAll('input[name="numero_telefone"]').forEach((input) => {
            input.type = 'tel';
            input.inputMode = 'numeric';
            input.maxLength = 15;
            const validar = () => input.setCustomValidity(input.value && !telefoneValido(input.value) ? 'Informe um telefone brasileiro válido com DDD.' : '');
            input.addEventListener('input', () => {
                const numero = somenteDigitos(input.value).slice(0, 11);
                input.value = numero.length > 10
                    ? numero.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3')
                    : numero.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                validar();
            });
            validar();
        });

        const hoje = new Date();
        const dataMaxima = hoje.toISOString().slice(0, 10);
        const dataMinima = new Date(hoje.getFullYear() - 120, hoje.getMonth(), hoje.getDate()).toISOString().slice(0, 10);
        document.querySelectorAll('input[name="data_nascimento"]').forEach((input) => {
            input.max = dataMaxima;
            input.min = dataMinima;
        });

        const caminhoAtual = window.location.pathname.replace(/\/+$/, '') || '/';
        const caminhoInicial = new URL(homeUrl, window.location.origin).pathname.replace(/\/+$/, '') || '/';
        const ehRaiz = caminhoAtual === caminhoInicial || caminhoAtual === '' || caminhoAtual === '/' || caminhoAtual.endsWith('/homepage.php');

        // Verifica se a tela já possui um botão de voltar (evita duplicação)
        const botaoExistente = document.querySelector('.botao-voltar, .botao-voltar-global, #topo a[href*="inicial"], #topo a.botao, .barra-topo-voltar');

        if (!ehRaiz && !botaoExistente) {
            const nav = document.querySelector('nav');
            const voltar = document.createElement('a');
            voltar.className = 'botao-voltar botao-voltar-global';
            voltar.href = 'javascript:void(0);';
            voltar.setAttribute('aria-label', 'Voltar para a tela anterior');
            voltar.innerHTML = '<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar';
            voltar.addEventListener('click', (evento) => {
                evento.preventDefault();
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = homeUrl;
                }
            });

            if (nav) {
                // Posiciona no lado superior esquerdo do menu de navegação
                nav.insertBefore(voltar, nav.firstChild);
            } else {
                // Caso não haja nav, posiciona no topo superior esquerdo da página
                const container = document.querySelector('.container') || document.querySelector('main') || document.body;
                container.insertBefore(voltar, container.firstChild);
            }
        }
    });
})();
