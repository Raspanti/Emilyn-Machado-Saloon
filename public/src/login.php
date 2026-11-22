<?php
function iniciarSessaoLogin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('emilyn_sessao');
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_start(['cookie_httponly' => true, 'cookie_secure' => $https, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true, 'use_only_cookies' => true, 'cookie_path' => '/']);
    }
    header('Cache-Control: no-store, private');
    header('Referrer-Policy: no-referrer');
    if (empty($_SESSION['loginToken'])) $_SESSION['loginToken'] = bin2hex(random_bytes(32));
}

function usuarioLogado(): ?array
{
    $usuario = $_SESSION['usuario'] ?? null;
    if (!is_array($usuario) || !isset($usuario['id'], $usuario['email'], $usuario['nome'], $usuario['perfil'], $usuario['entrada'])) return null;
    if ((int) $usuario['entrada'] < time() - 8 * 3600) {
        unset($_SESSION['usuario'], $_SESSION['agendaDados']);
        return null;
    }
    return $usuario;
}

function administradorAgenda(): bool
{
    return (usuarioLogado()['perfil'] ?? null) === 'equipe';
}

function exigirLogin(): void
{
    if (isset($_GET['data'], $_GET['hora'], $_GET['horaFim'])) {
        $selecao = [];
        foreach (['servico', 'data', 'hora', 'horaFim', 'tipo'] as $campo) {
            $selecao[$campo] = isset($_GET[$campo]) && is_string($_GET[$campo]) ? substr($_GET[$campo], 0, 100) : '';
        }
        $_SESSION['agendaSelecao'] = $selecao;
    }
    if (usuarioLogado()) return;
    $servico = isset($_GET['servico']) && is_string($_GET['servico']) ? $_GET['servico'] : '';
    global $services;
    $destino = '/login.php' . (isset($services[$servico]) ? '?servico=' . rawurlencode($servico) : '');
    header('Location: ' . $destino, true, 303);
    exit;
}

function campoConta(string $nome): string
{
    return is_string($_POST[$nome] ?? null) ? $_POST[$nome] : '';
}

function emailContaValido(string $email): bool
{
    return strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function senhaContaValida(string $senha): bool
{
    return strlen($senha) <= 72 && !str_contains($senha, "\0") && preg_match('/\A.{8,}\z/us', $senha) === 1;
}

function verificarTokenLogin(): void
{
    if (!hash_equals($_SESSION['loginToken'], campoConta('loginToken'))) {
        http_response_code(403);
        exit('Formulário expirado. Reabra a página.');
    }
}

function irParaAgenda(): never
{
    global $services;
    $servico = $_SESSION['loginServico'] ?? '';
    unset($_SESSION['loginServico']);
    $destino = '/agendamento.php' . (isset($services[$servico]) ? '?servico=' . rawurlencode($servico) : '');
    header('Location: ' . $destino, true, 303);
    exit;
}
