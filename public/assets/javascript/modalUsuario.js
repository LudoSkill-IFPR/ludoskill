(() => {
    const fecharModal = (modal) => {
        if (modal instanceof HTMLDialogElement && modal.open) modal.close();
    };

    document.querySelectorAll('[data-abrir-modal-usuario]').forEach((botao) => {
        botao.addEventListener('click', () => {
            const idModal = botao.dataset.abrirModalUsuario;
            const modal = idModal ? document.getElementById(idModal) : null;
            if (modal instanceof HTMLDialogElement) modal.showModal();
        });
    });

    document.querySelectorAll('[data-modal-usuario]').forEach((modal) => {
        modal.querySelectorAll('[data-fechar-modal-usuario]').forEach((botao) => {
            botao.addEventListener('click', () => fecharModal(modal));
        });

        modal.addEventListener('click', (evento) => {
            if (evento.target === modal) fecharModal(modal);
        });
    });
})();
