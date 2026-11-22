<?php
require __DIR__ . '/src/bootstrap.php';
$id = isset($_GET['id']) && is_string($_GET['id']) ? $_GET['id'] : '';
$service = $services[$id] ?? null;
if (!$service) http_response_code(404);
$bookingPage = (bool) $service;
$agendamentoConfirmado = false;
if ($service && isset($_COOKIE['emilyn_sessao'])) {
    require_once __DIR__ . '/src/login.php';
    iniciarSessaoLogin();
    $agendamentoConfirmado = ($_SESSION['agendaServicoConfirmado'] ?? null) === $id;
    if ($agendamentoConfirmado) unset($_SESSION['agendaServicoConfirmado']);
    session_write_close();
}
$pageTitle = $service ? $service['name'] : 'Serviço não encontrado';
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page">
    <?php if (!$service): ?>
        <header class="page-heading"><span class="eyebrow">Página não encontrada</span><h1>Vamos encontrar outro cuidado?</h1><a class="button" href="/servicos.php">Ver serviços <?= renderIcon('right') ?></a></header>
    <?php else: $category = $categories[$service['category']]; $foto = $fotosServicos[$id] ?? null; ?>
        <?php
        $categoriaUnica = servicoUnicoDaCategoria($services, $service['category']) !== null;
        $voltarPara = $categoriaUnica ? '/servicos.php' : '/servicos.php?categoria=' . $service['category'];
        $rotuloVoltar = $categoriaUnica ? 'Todos os cuidados' : $category['name'];
        ?>
        <a class="back-link" href="<?= escapeHtml($voltarPara) ?>"><?= renderIcon('back') ?> <?= escapeHtml($rotuloVoltar) ?></a>
        <?php if ($agendamentoConfirmado): ?>
            <p role="status">Agendamento confirmado. <a class="text-link" href="/agendamento.php">Ver minha agenda</a></p>
        <?php endif; ?>
        <article class="service-detail"><div class="detail-art art-<?= escapeHtml($service['category']) ?><?= $foto ? ' has-photo' : '' ?><?= $foto && $foto['tipo'] === 'Antes e depois' ? ' photo-comparison' : '' ?>">
            <?php if ($foto): ?>
                <img src="<?= escapeHtml($foto['arquivo']) ?>" alt="<?= escapeHtml($foto['alt']) ?>" width="<?= $foto['largura'] ?>" height="<?= $foto['altura'] ?>" decoding="async">
            <?php else: ?>
                <?= renderIcon($category['icon']) ?><span class="art-orbit" aria-hidden="true"></span><span class="art-label">Seu momento de cuidado</span>
            <?php endif; ?>
        </div><div class="detail-copy"><span class="eyebrow"><?= escapeHtml($category['name']) ?></span><h1><?= escapeHtml($service['name']) ?><span class="title-dot">.</span></h1><p><?= escapeHtml($service['description']) ?></p><?php if ($foto && $foto['tipo'] === 'Antes e depois'): ?><p class="service-photo-note">Imagem de um atendimento combinado: corte, luzes e hidratação.</p><?php elseif ($foto && $foto['tipo'] === 'Ilustrativa'): ?><p class="service-photo-note">Imagem ilustrativa.</p><?php endif; ?><a class="button" href="#escolher-horario">Escolher data e horário <?= renderIcon('calendar') ?></a><small class="booking-note">Consulte valores e disponibilidade com o salão.</small></div></article>
    <section id="escolher-horario" aria-label="Agendar este serviço">
        <form class="booking-service" action="/agendamento.php" method="get">
            <input type="hidden" name="servico" value="<?= escapeHtml($id) ?>">
            <input type="hidden" name="tipo" value="<?= escapeHtml(tipoAgendaServico($service)) ?>">
            <?php require __DIR__ . '/src/includes/calendario.php'; ?>
            <button type="submit" class="button">Continuar com este horário <?= renderIcon('right') ?></button>
            <small class="booking-note">Você entrará na sua conta para confirmar. A seleção ainda não reserva o horário.</small>
        </form>
    </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
