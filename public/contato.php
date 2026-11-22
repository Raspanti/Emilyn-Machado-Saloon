<?php
require __DIR__ . '/src/bootstrap.php';
$id = isset($_GET['servico']) && is_string($_GET['servico']) ? $_GET['servico'] : '';
$selected = $services[$id] ?? null;
$pageTitle = 'Contato e agendamento';
require __DIR__ . '/src/includes/header.php';
$whatsapp = preg_replace('/\D/', '', $site['whatsapp']);
$instagram = filter_var($site['instagram'], FILTER_VALIDATE_URL) && str_starts_with($site['instagram'], 'https://') ? $site['instagram'] : '';
?>
<main id="conteudo" class="page-main container inner-page contact-page">
    <a class="back-link" href="/index.php"><?= renderIcon('back') ?> Início</a>
    <header class="page-heading"><span class="eyebrow">Contato & agendamento</span><h1>Vamos reservar<br><em>um tempo seu?</em></h1><p>Converse com o salão sobre o serviço, os valores e os horários disponíveis.</p></header>
    <?php if ($selected): ?><div class="selected-service"><?= renderIcon('calendar') ?><div><span>Você escolheu</span><strong><?= escapeHtml($selected['name']) ?></strong></div><a href="/servicos.php">Trocar</a></div><?php endif; ?>
    <div class="contact-grid">
        <section class="contact-card"><span class="contact-number">01</span><h2>Fale com o salão</h2><p>Uma conversa para tirar dúvidas e combinar seu atendimento.</p><?php if ($whatsapp): ?><a class="button" href="https://wa.me/<?= escapeHtml($whatsapp) ?>?text=<?= rawurlencode('Olá! Gostaria de saber sobre ' . ($selected['name'] ?? 'os serviços do salão') . '.') ?>" target="_blank" rel="noopener noreferrer">Abrir WhatsApp <?= renderIcon('right') ?></a><?php else: ?><span class="coming-soon">Contato em breve</span><p class="setup-note">Os canais oficiais ainda serão adicionados. Nenhum agendamento é realizado por esta página.</p><?php endif; ?></section>
        <section class="contact-card"><span class="contact-number">02</span><h2>Acompanhe os cuidados</h2><p>Inspirações e novidades para o seu próximo visual.</p><?php if ($instagram): ?><a class="text-link" href="<?= escapeHtml($instagram) ?>" target="_blank" rel="noopener noreferrer">Visitar Instagram <?= renderIcon('right') ?></a><?php else: ?><span class="coming-soon">Instagram em breve</span><?php endif; ?><?php if ($site['address']): ?><p class="contact-address"><?= renderIcon('pin') ?> <?= escapeHtml($site['address']) ?></p><?php endif; ?></section>
    </div>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
