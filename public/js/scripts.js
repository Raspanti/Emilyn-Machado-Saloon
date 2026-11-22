// VOLTAR AO TOPO
// No celular, a navegação é feita pela barra inferior, sem menu expansível.
const btnTop = document.querySelector('[data-back-top]');

function backToTop(event) {
    event.preventDefault();
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.getElementById('top').scrollIntoView({
        behavior: reducedMotion ? 'instant' : 'smooth'
    });
    document.querySelector('.skip-link').focus({ preventScroll: true });
}

if (btnTop) {
    btnTop.addEventListener('click', backToTop);
}


// BARRA INFERIOR FIXA NA ÁREA VISÍVEL
// O teclado pode reduzir visualViewport sem reduzir a altura do layout.
const bottomNav = document.querySelector('.bottom-nav');
const mobileScreen = window.matchMedia('(max-width: 899px)');
let bottomNavFrame = 0;

function calcularRecuoBarra(alturaLayout, alturaVisivel, deslocamentoTopo) {
    return Math.max(0, Math.round(alturaLayout - alturaVisivel - deslocamentoTopo));
}

function atualizarBarraInferior() {
    if (!bottomNav) return;
    const viewport = window.visualViewport;
    let recuo = 0;

    // Não confundir o zoom por pinça com o teclado virtual.
    if (mobileScreen.matches && viewport && Math.abs(viewport.scale - 1) < 0.01) {
        recuo = calcularRecuoBarra(window.innerHeight, viewport.height, viewport.offsetTop);
    }

    document.documentElement.style.setProperty('--bottom-nav-offset', recuo + 'px');
    if (mobileScreen.matches) {
        const espaco = Math.ceil(bottomNav.getBoundingClientRect().height) + 16;
        document.documentElement.style.setProperty('--bottom-nav-space', espaco + 'px');
    }
}

function agendarAjusteBarra() {
    if (bottomNavFrame) return;
    bottomNavFrame = window.requestAnimationFrame(() => {
        bottomNavFrame = 0;
        atualizarBarraInferior();
    });
}

if (bottomNav) {
    window.addEventListener('resize', agendarAjusteBarra, { passive: true });
    window.addEventListener('pageshow', agendarAjusteBarra);
    document.addEventListener('focusin', agendarAjusteBarra);
    document.addEventListener('focusout', agendarAjusteBarra);
    mobileScreen.addEventListener('change', agendarAjusteBarra);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', agendarAjusteBarra, { passive: true });
        window.visualViewport.addEventListener('scroll', agendarAjusteBarra, { passive: true });
    }
    if ('ResizeObserver' in window) {
        const bottomNavObserver = new ResizeObserver(agendarAjusteBarra);
        bottomNavObserver.observe(bottomNav);
    }
    atualizarBarraInferior();
}
