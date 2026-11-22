<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/galeria.php';

$servico = isset($_GET['servico']) && is_string($_GET['servico']) ? $_GET['servico'] : 'todos';
if (!isset($servicosGaleria[$servico])) {
    http_response_code(404);
    $servicoInvalido = true;
} else {
    $servicoInvalido = false;
}
$fotosFiltradas = [];
foreach ($fotosGaleria as $foto) {
    if ($servico === 'todos' || in_array($servico, $foto['servicos'], true)) $fotosFiltradas[] = $foto;
}
$pageTitle = 'Galeria';
$galleryPage = true;
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page gallery-page">
    <header class="page-heading"><span class="eyebrow">Nosso trabalho, de perto</span><h1>Galeria de <em>cuidados.</em></h1><p>Resultados e momentos de atendimento do salão, organizados por serviço.</p></header>

    <nav class="gallery-filters" aria-label="Filtrar fotos por serviço">
        <?php foreach ($servicosGaleria as $chave => $nome): ?>
            <?php $quantidade = 0; foreach ($fotosGaleria as $foto) { if ($chave === 'todos' || in_array($chave, $foto['servicos'], true)) $quantidade++; } ?>
            <a href="/galeria.php?servico=<?= escapeHtml($chave) ?>"<?= $servico === $chave ? ' aria-current="page"' : '' ?>><?= escapeHtml($nome) ?><span class="gallery-filter-count"><?= $quantidade ?></span></a>
        <?php endforeach; ?>
    </nav>

    <?php if ($servicoInvalido): ?>
        <p class="gallery-empty">Categoria não encontrada. Escolha um serviço acima para continuar.</p>
    <?php else: ?>
        <p class="gallery-count"><?= count($fotosFiltradas) ?> foto(s) · <?= escapeHtml($servicosGaleria[$servico]) ?></p>
        <div class="gallery-grid">
            <?php foreach ($fotosFiltradas as $indice => $foto): ?>
                <figure class="gallery-card">
                    <a class="gallery-photo" href="<?= escapeHtml($foto['arquivo']) ?>" target="_blank" rel="noopener" data-gallery-photo data-title="<?= escapeHtml($foto['titulo']) ?>" aria-label="Ampliar: <?= escapeHtml($foto['titulo']) ?>">
                        <img src="<?= escapeHtml($foto['arquivo']) ?>" alt="<?= escapeHtml($foto['alt']) ?>" width="<?= $foto['largura'] ?>" height="<?= $foto['altura'] ?>" loading="<?= $indice < 2 ? 'eager' : 'lazy' ?>" decoding="async">
                        <span class="gallery-enlarge">Ampliar foto</span>
                    </a>
                    <figcaption><span class="gallery-photo-type"><?= escapeHtml($foto['tipo']) ?></span><h2><?= escapeHtml($foto['titulo']) ?></h2><p><?= escapeHtml($foto['descricao']) ?></p><a class="gallery-source" href="<?= escapeHtml($foto['origem']) ?>" target="_blank" rel="noopener noreferrer">Publicação no Instagram</a></figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
        <p class="gallery-note">Uma foto pode representar mais de um serviço. Em “Todas”, cada imagem aparece uma única vez. As imagens marcadas como “Antes” mostram o ponto de partida do atendimento.</p>
    <?php endif; ?>
    <section class="gallery-contact"><h2>Encontrou uma inspiração?</h2><p>Converse com o salão sobre o resultado que você procura.</p><a class="button" href="/contato.php">Falar com o salão</a></section>
</main>

<dialog class="gallery-dialog" id="gallery-dialog" aria-labelledby="gallery-dialog-title">
    <div class="gallery-dialog-header"><h2 id="gallery-dialog-title">Foto ampliada</h2><button type="button" data-gallery-close aria-label="Fechar foto ampliada">Fechar</button></div>
    <img id="gallery-dialog-image" alt="">
    <a id="gallery-original" target="_blank" rel="noopener">Abrir imagem original</a>
</dialog>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
