<?php $pageTitle = 'Sobre nós'; require __DIR__ . '/src/includes/header.php'; ?>
<main id="conteudo" class="page-main container inner-page">
    <a class="back-link" href="/index.php"><?= renderIcon('back') ?> Início</a>
    <section class="about-layout"><div class="about-art" aria-hidden="true"><span class="about-monogram">em</span><span>Beleza com cuidado</span></div><div class="page-heading"><span class="eyebrow">Emilyn Machado · Salão de beleza</span><h1>Um espaço para<br><em>cuidar de você.</em></h1><p>A beleza também está em reservar um tempo na rotina. Aqui, cada cuidado começa com uma conversa sobre o que você procura.</p><p>Explore os serviços de cabelo, unhas e outros cuidados, e fale com o salão para conhecer as opções de atendimento.</p><a class="button" href="/servicos.php">Conheça os cuidados <?= renderIcon('right') ?></a></div></section>
    <section class="about-contact"><span class="eyebrow">Vamos conversar?</span><h2>Seu próximo momento<br>começa aqui.</h2><a class="text-link" href="/contato.php">Entre em contato <?= renderIcon('right') ?></a></section>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
