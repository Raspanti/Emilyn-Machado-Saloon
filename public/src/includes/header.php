<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $pageTitle ?? 'Seu momento de cuidado';
$navItems = ['index.php' => 'Início', 'servicos.php' => 'Serviços', 'galeria.php' => 'Galeria', 'sobre.php' => 'Sobre nós'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#a7ad96">
    <meta name="description" content="Conheça os serviços e encontre seu momento de cuidado no salão Emilyn Machado.">
    <title><?= escapeHtml($pageTitle) ?> · <?= escapeHtml($site['name']) ?></title>
    <link rel="stylesheet" href="/css/style.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../../css/style.css'), 0, 12) ?>">
    <?php if (!empty($agendaPage)): ?><link rel="stylesheet" href="/css/agenda.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../../css/agenda.css'), 0, 12) ?>"><?php endif; ?>
    <?php if (!empty($agendaPage) || !empty($bookingPage)): ?><link rel="stylesheet" href="/css/calendario.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../../css/calendario.css'), 0, 12) ?>"><?php endif; ?>
    <?php if (!empty($galleryPage)): ?><link rel="stylesheet" href="/css/galeria.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../../css/galeria.css'), 0, 12) ?>"><?php endif; ?>
</head>
<body id="top" class="app-layout">
<a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="/index.php" aria-label="Emilyn Machado — início">
            <span class="brand-monogram" aria-hidden="true">em<span>✦</span></span>
            <span class="brand-copy"><span class="brand-name"><?= escapeHtml($site['name']) ?></span><span class="brand-subtitle"><?= escapeHtml($site['subtitle']) ?></span></span>
        </a>
        <nav id="nav" class="main-nav" aria-label="Navegação principal">
            <?php foreach ($navItems as $href => $label): ?>
                <a href="/<?= escapeHtml($href) ?>"<?= $currentPage === $href ? ' aria-current="page"' : '' ?>><?= escapeHtml($label) ?></a>
            <?php endforeach; ?>
            <a href="/agendamento.php" class="header-booking">Agende seu momento <?= renderIcon('right') ?></a>
        </nav>
    </div>
</header>

<?php require __DIR__ . '/bottom-nav.php'; ?>
