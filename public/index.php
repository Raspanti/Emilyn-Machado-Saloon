<?php $pageTitle = 'Seu momento de cuidado'; require __DIR__ . '/src/includes/header.php'; ?>
<main id="conteudo" class="page-main">
    <section class="home-intro container" aria-labelledby="home-title">
        <div class="intro-copy"><span class="eyebrow">Beleza que acolhe</span><h1 id="home-title">Um cuidado. <br>Um respiro.<br><em>Seu momento.</em></h1><p>Descubra os nossos cuidados e encontre o que combina com você.</p><a class="text-link intro-link" href="/sobre.php">Conheça o salão <?= renderIcon('right') ?></a></div>
        <div class="home-catalog"><div class="section-heading"><h2>Como quer se cuidar?</h2><span>Explore os serviços</span></div><?php require __DIR__ . '/src/includes/category-grid.php'; ?></div>
    </section>
    <section class="container gallery-invite"><div><span class="eyebrow">Resultados reais</span><h2>Conheça nossos trabalhos.</h2><p>Veja cortes, luzes, coloração e outros cuidados na galeria.</p></div><a class="button" href="/galeria.php">Ver galeria</a></section>
    <section class="care-note container"><span class="note-star" aria-hidden="true">✦</span><div><span class="eyebrow">No seu ritmo</span><h2>O cuidado começa<br>com uma boa conversa.</h2><p>Tem uma ideia de visual ou está em dúvida sobre um serviço? Fale com o salão antes de agendar.</p><a class="text-link" href="/contato.php">Entre em contato <?= renderIcon('right') ?></a></div></section>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
