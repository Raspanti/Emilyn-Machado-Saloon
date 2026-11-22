<?php
require __DIR__ . '/src/agenda.php';
$pageTitle = 'Editar agendamento';
$agendaErro = $_SESSION['agendaErro'] ?? '';
$agendaAnterior = $_SESSION['agendaDados'] ?? [];
unset($_SESSION['agendaErro'], $_SESSION['agendaDados'], $_SESSION['agendaMensagem']);
$agendaDados = null;
try {
    $id = idAgenda($_GET, 'id');
    require __DIR__ . '/src/bdconnecta.php';
    $agendaDados = buscarAgenda($conn, $id);
    if ($agendaAnterior) $agendaDados = array_merge($agendaDados, $agendaAnterior);
    mysqli_close($conn);
} catch (DomainException $exception) {
    http_response_code(404);
    $agendaErro = $exception->getMessage();
} catch (Throwable $exception) {
    http_response_code(503);
    error_log('Agenda: ' . $exception->getMessage());
    $agendaErro = 'Não foi possível abrir o agendamento.';
}
$editando = true;
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page agenda-page">
    <a class="back-link" href="/agendamento.php">Voltar para a agenda</a>
    <header class="page-heading"><span class="eyebrow">Painel local</span><h1>Editar atendimento</h1></header>
    <?php if ($agendaErro): ?><p class="agenda-error agenda-notice" role="alert"><?= escapeHtml($agendaErro) ?></p><?php endif; ?>
    <?php if ($agendaDados): ?><section class="agenda-panel"><?php require __DIR__ . '/src/includes/formAgenda.php'; ?></section><?php endif; ?>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
