<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/login.php';
iniciarSessaoLogin();
$servico = isset($_GET['servico']) && is_string($_GET['servico']) ? $_GET['servico'] : '';
if (isset($services[$servico])) $_SESSION['loginServico'] = $servico;
if ($_SERVER['REQUEST_METHOD'] === 'POST') iniciarLoginGoogle();
if (usuarioLogado()) {
    header('Location: /agendamento.php' . (isset($services[$servico]) ? '?servico=' . rawurlencode($servico) : ''), true, 303); exit;
}
$pageTitle = 'Entrar';
$agendaPage = true;
$loginErro = $_SESSION['loginErro'] ?? '';
unset($_SESSION['loginErro']);
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page agenda-page">
    <header class="page-heading"><span class="eyebrow">Seu momento de cuidado</span><h1>Entre para agendar<span class="title-dot">.</span></h1><p>Use sua conta Google para marcar um horário e acompanhar seus agendamentos.</p></header>
    <section class="agenda-panel login-panel" aria-labelledby="entrar-titulo">
        <h2 id="entrar-titulo">Bem-vinda ao salão</h2>
        <?php if ($loginErro): ?><p class="agenda-error agenda-notice" role="alert"><?= escapeHtml($loginErro) ?></p><?php endif; ?>
        <?php if (!googleConfigurado()): ?><p class="agenda-notice" role="status">O login com Google está sendo configurado. Os agendamentos pelo site ficarão disponíveis em breve.</p><?php endif; ?>
        <form method="post" action="/login.php">
            <input type="hidden" name="loginToken" value="<?= escapeHtml($_SESSION['loginToken']) ?>">
            <button class="google-login" type="submit"<?= !googleConfigurado() ? ' disabled' : '' ?>>Entrar com Google</button>
        </form>
        <p class="login-help">Usamos seu nome e e-mail para identificar sua conta. Sua senha é informada somente ao Google.</p>
        <a class="text-link" href="/servicos.php">Continuar vendo os serviços</a>
    </section>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
