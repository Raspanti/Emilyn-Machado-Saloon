const galleryDialog = document.getElementById('gallery-dialog');
const galleryImage = document.getElementById('gallery-dialog-image');
const galleryTitle = document.getElementById('gallery-dialog-title');
const galleryOriginal = document.getElementById('gallery-original');

function abrirFoto(event) {
    if (!galleryDialog || typeof galleryDialog.showModal !== 'function') return;
    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    const link = event.currentTarget;
    galleryImage.src = link.href;
    galleryImage.alt = link.querySelector('img').alt;
    galleryTitle.textContent = link.dataset.title;
    galleryOriginal.href = link.href;
    galleryDialog.showModal();
}

function fecharFoto() {
    galleryDialog.close();
}

if (galleryDialog) {
    document.querySelectorAll('[data-gallery-photo]').forEach((link) => {
        link.addEventListener('click', abrirFoto);
    });
    document.querySelector('[data-gallery-close]').addEventListener('click', fecharFoto);
    galleryDialog.addEventListener('click', (event) => {
        const area = galleryDialog.getBoundingClientRect();
        if (event.clientX < area.left || event.clientX > area.right || event.clientY < area.top || event.clientY > area.bottom) fecharFoto();
    });
    galleryDialog.addEventListener('close', () => galleryImage.removeAttribute('src'));
}
