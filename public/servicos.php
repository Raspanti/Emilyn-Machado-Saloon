<?php
require __DIR__ . '/src/bootstrap.php';
$slug = isset($_GET['categoria']) && is_string($_GET['categoria']) ? $_GET['categoria'] : '';
$category = $categories[$slug] ?? null;
if ($slug !== '' && !$category) { http_response_code(404); }
if ($category) {
    $idUnico = servicoUnicoDaCategoria($services, $slug);
    if ($idUnico !== null) {
        header('Location: /servico.php?id=' . rawurlencode($idUnico));
        exit;
    }
}
$pageTitle = $category ? $category['name'] : 'Nossos cuidados';
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page">
    <a class="back-link" href="/index.php"><?= renderIcon('back') ?> Todos os cuidados</a>
    <?php if ($slug !== '' && !$category): ?>
        <header class="page-heading"><span class="eyebrow">Página não encontrada</span><h1>Este cuidado não está no catálogo.</h1><a class="button" href="/servicos.php">Ver serviços <?= renderIcon('right') ?></a></header>
    <?php elseif (!$category): ?>
        <header class="page-heading"><span class="eyebrow">Um tempo para você</span><h1>Nossos <em>cuidados.</em></h1><p>Escolha uma categoria para conhecer os serviços.</p></header>
        <?php require __DIR__ . '/src/includes/category-grid.php'; ?>
    <?php else: ?>
        <header class="page-heading"><span class="eyebrow">Nossos cuidados</span><h1><?= escapeHtml($category['name']) ?><span class="title-dot">.</span></h1><p><?= escapeHtml($category['intro']) ?></p></header>
        <div class="category-grid service-grid">
            <?php foreach ($services as $id => $service): if ($service['category'] !== $slug) continue; ?>
                <a class="category-card" href="/servico.php?id=<?= escapeHtml($id) ?>"><?php $foto = $fotosServicos[$id] ?? null; ?>
                    <span class="category-art art-<?= escapeHtml($slug) ?><?= $foto ? ' has-photo' : '' ?><?= $foto && $foto['tipo'] === 'Antes e depois' ? ' photo-comparison' : '' ?>">
                        <?php if ($foto): ?>
                            <img src="<?= escapeHtml($foto['arquivo']) ?>" alt="<?= escapeHtml($foto['alt']) ?>" width="<?= $foto['largura'] ?>" height="<?= $foto['altura'] ?>" loading="lazy" decoding="async">
                        <?php else: ?>
                            <?= renderIcon($category['icon']) ?><span class="art-orbit" aria-hidden="true"></span>
                        <?php endif; ?>
                    </span><span class="category-caption"><?= escapeHtml($service['name']) ?><?= renderIcon('right') ?></span></a>
            <?php endforeach; ?>
            <?php if ($slug === 'cabelo'): $fotoProduto = $fotosCategorias['produtos'] ?? null; ?>
                <a class="category-card" href="/servicos.php?categoria=produtos"><span class="category-art art-produtos<?= $fotoProduto ? ' has-photo' : '' ?>">
                    <?php if ($fotoProduto): ?><img src="<?= escapeHtml($fotoProduto['arquivo']) ?>" alt="<?= escapeHtml($fotoProduto['alt']) ?>" width="<?= $fotoProduto['largura'] ?>" height="<?= $fotoProduto['altura'] ?>" loading="lazy" decoding="async"><?php else: ?><?= renderIcon('bottle') ?><?php endif; ?>
                </span><span class="category-caption">Produtos <?= renderIcon('right') ?></span></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
