<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/login.php';
iniciarSessaoLogin();
if (usuarioLogado()) irParaAgenda();

$nome = $email = $erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarTokenLogin();
    $nome = trim(campoConta('nome'));
    $email = trim(campoConta('email'));
    $senha = campoConta('senha');
    if (preg_match('/\A.{1,100}\z/u', $nome) !== 1) {
        $erro = 'Informe um nome de até 100 caracteres.';
    } elseif (!emailContaValido($email)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (!senhaContaValida($senha)) {
        $erro = 'A senha deve ter pelo menos 8 caracteres e até 72 bytes.';
    } elseif ($senha !== campoConta('confirmacao')) {
        $erro = 'As senhas precisam ser iguais.';
    } else {
        try {
            require __DIR__ . '/src/bdconnecta.php';
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, 'cliente')");
            $stmt->bind_param('sss', $nome, $email, $hash);
            $stmt->execute();
            $_SESSION['cadastroConcluido'] = true;
            header('Location: /login.php', true, 303);
            exit;
        } catch (mysqli_sql_exception $exception) {
            if ((int) $exception->getCode() === 1062) {
                $erro = 'Já existe uma conta com esse e-mail.';
            } else {
                error_log('Cadastro: MySQL ' . $exception->getCode());
                http_response_code(503);
                $erro = 'O cadastro está temporariamente indisponível.';
            }
        }
    }
}
$pageTitle = 'Criar conta';
$agendaPage = true;
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page agenda-page">
    <header class="page-heading"><h1>Criar conta<span class="title-dot">.</span></h1></header>
    <section class="agenda-panel login-panel" aria-label="Cadastro de cliente">
        <?php if ($erro): ?><p class="agenda-error agenda-notice" role="alert"><?= escapeHtml($erro) ?></p><?php endif; ?>
        <form class="agenda-form" method="post" action="/cadastro.php">
            <input type="hidden" name="loginToken" value="<?= escapeHtml($_SESSION['loginToken']) ?>">
            <div class="agenda-field agenda-wide"><label for="nome">Nome</label>
                <input id="nome" name="nome" maxlength="100" autocomplete="name" value="<?= escapeHtml($nome) ?>" required></div>
            <div class="agenda-field agenda-wide"><label for="email">E-mail</label>
                <input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= escapeHtml($email) ?>" required></div>
            <div class="agenda-field agenda-wide"><label for="senha">Senha (mínimo de 8 caracteres)</label>
                <input id="senha" name="senha" type="password" minlength="8" autocomplete="new-password" required></div>
            <div class="agenda-field agenda-wide"><label for="confirmacao">Confirmar senha</label>
                <input id="confirmacao" name="confirmacao" type="password" minlength="8" autocomplete="new-password" required></div>
            <div class="agenda-wide"><button class="agenda-small-button" type="submit">Cadastrar</button></div>
        </form>
        <p class="login-help"><a class="text-link" href="/login.php">Voltar para o login</a></p>
    </section>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
