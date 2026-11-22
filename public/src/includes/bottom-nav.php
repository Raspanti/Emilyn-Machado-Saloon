<nav class="bottom-nav" aria-label="Navegação rápida">
    <a href="/galeria.php"<?= $currentPage === 'galeria.php' ? ' aria-current="page"' : '' ?>><?= renderIcon('gallery') ?><span>Galeria</span></a>
    <a class="bottom-home" href="/index.php"<?= $currentPage === 'index.php' ? ' aria-current="page"' : '' ?>><span class="home-disc"><?= renderIcon('home') ?></span><span>Início</span></a>
    <a href="/agendamento.php"<?= $currentPage === 'agendamento.php' ? ' aria-current="page"' : '' ?>><?= renderIcon('calendar') ?><span>Agendar</span></a>
</nav>
