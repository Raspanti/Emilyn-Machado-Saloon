<footer class="site-footer">
    <div class="container footer-inner">
        <div><span class="footer-name">Emilyn Machado</span><p>Beleza, cuidado e um tempo para você.</p></div>
        <a href="/galeria.php">Galeria de trabalhos</a>
        <a href="/contato.php">Vamos conversar <?= renderIcon('right') ?></a>
        <small>© <?= date('Y') ?> · Projeto acadêmico</small>
    </div>
</footer>
<script src="/js/scripts.js?v=<?= substr(hash_file('sha256', __DIR__ . '/../../js/scripts.js'), 0, 12) ?>" defer></script>
<?php if (!empty($agendaPage)): ?><script src="/js/agenda.js?v=<?= substr(hash_file('sha256', __DIR__ . '/../../js/agenda.js'), 0, 12) ?>" defer></script><?php endif; ?>
<?php if (!empty($agendaPage) || !empty($bookingPage)): ?><script src="/js/calendario.js?v=<?= substr(hash_file('sha256', __DIR__ . '/../../js/calendario.js'), 0, 12) ?>" defer></script><?php endif; ?>
<?php if (!empty($galleryPage)): ?><script src="/js/galeria.js?v=<?= substr(hash_file('sha256', __DIR__ . '/../../js/galeria.js'), 0, 12) ?>" defer></script><?php endif; ?>
</body>
</html>
