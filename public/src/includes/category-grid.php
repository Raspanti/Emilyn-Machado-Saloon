<div class="category-grid">
    <?php foreach ($categories as $slug => $category):
        $idUnico = servicoUnicoDaCategoria($services, $slug);
        $destino = $idUnico !== null ? '/servico.php?id=' . $idUnico : '/servicos.php?categoria=' . $slug;
    ?>
        <a class="category-card" href="<?= escapeHtml($destino) ?>">
            <?php $foto = $fotosCategorias[$slug] ?? null; ?>
            <span class="category-art art-<?= escapeHtml($slug) ?><?= $foto ? ' has-photo' : '' ?>">
                <?php if ($foto): ?>
                    <img src="<?= escapeHtml($foto['arquivo']) ?>" alt="<?= escapeHtml($foto['alt']) ?>" width="<?= $foto['largura'] ?>" height="<?= $foto['altura'] ?>" decoding="async">
                <?php else: ?>
                    <?= renderIcon($category['icon']) ?><span class="art-orbit" aria-hidden="true"></span>
                <?php endif; ?>
            </span>
            <span class="category-caption"><?= escapeHtml($category['name']) ?><?= renderIcon('right') ?></span>
        </a>
    <?php endforeach; ?>
</div>
