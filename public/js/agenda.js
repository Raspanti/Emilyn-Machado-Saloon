document.querySelectorAll('[data-agenda-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Excluir este agendamento?')) event.preventDefault();
    });
});
