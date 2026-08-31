document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.questao-funcionario').forEach((questao) => {
        const botao = questao.querySelector('.verificar-resposta');
        botao.addEventListener('click', () => {
            const selecionada = questao.querySelector('input[type="radio"]:checked');
            if (!selecionada) {
                window.alert('Selecione uma alternativa antes de verificar.');
                return;
            }

            const acertou = Number(selecionada.value) === Number(questao.dataset.correta);
            const resultado = questao.querySelector('.resultado-questao');
            const status = questao.querySelector('.status-resposta');
            status.textContent = acertou ? 'Resposta correta!' : 'Resposta incorreta.';
            resultado.classList.toggle('resposta-correta', acertou);
            resultado.classList.toggle('resposta-incorreta', !acertou);
            resultado.hidden = false;
        });
    });
});
