<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/login.php';
iniciarSessaoLogin();
$servico = is_string($_GET['servico'] ?? null) ? $_GET['servico'] : '';
if (isset($services[$servico])) $_SESSION['loginServico'] = $servico;
if (usuarioLogado()) irParaAgenda();

$email = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarTokenLogin();
    $email = trim(campoConta('email'));
    $senha = campoConta('senha');
    $erro = 'E-mail ou senha inválidos.';
    if (emailContaValido($email) && senhaContaValida($senha)) {
        try {
            require __DIR__ . '/src/bdconnecta.php';
            $stmt = $conn->prepare('SELECT id, nome, email, senha_hash, perfil FROM usuarios WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($id, $nome, $emailConta, $hash, $perfil);
            if ($stmt->fetch() && password_verify($senha, $hash)) {
                session_regenerate_id(true);
                $_SESSION['usuario'] = ['id' => (int) $id, 'nome' => $nome, 'email' => $emailConta,
                    'perfil' => $perfil, 'entrada' => time()];
                $_SESSION['loginToken'] = bin2hex(random_bytes(32));
                unset($_SESSION['agendaToken'], $_SESSION['agendaDados'], $_SESSION['agendaErro']);
                irParaAgenda();
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Login: MySQL ' . $exception->getCode());
            http_response_code(503);
            $erro = 'O login está temporariamente indisponível.';
        }
    }
}
$pageTitle = 'Entrar';
$agendaPage = true;
$cadastroConcluido = $_SESSION['cadastroConcluido'] ?? false;
unset($_SESSION['cadastroConcluido']);
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page agenda-page">
    <header class="page-heading"><h1>Entrar<span class="title-dot">.</span></h1></header>
    <section class="agenda-panel login-panel" aria-label="Login">
        <?php if ($cadastroConcluido): ?><p class="agenda-notice" role="status">Conta criada. Entre com seu e-mail e senha.</p><?php endif; ?>
        <?php if ($erro): ?><p class="agenda-error agenda-notice" role="alert"><?= escapeHtml($erro) ?></p><?php endif; ?>
        <form class="agenda-form" method="post" action="/login.php">
            <input type="hidden" name="loginToken" value="<?= escapeHtml($_SESSION['loginToken']) ?>">
            <div class="agenda-field agenda-wide"><label for="email">E-mail</label>
                <input id="email" name="email" type="email" maxlength="254" autocomplete="username" value="<?= escapeHtml($email) ?>" required></div>
            <div class="agenda-field agenda-wide"><label for="senha">Senha</label>
                <input id="senha" name="senha" type="password" autocomplete="current-password" required></div>
            <div class="agenda-wide"><button class="agenda-small-button" type="submit">Entrar</button></div>
        </form>
        <p class="login-help"><a class="text-link" href="/cadastro.php">Criar conta de cliente</a></p>
    </section>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
