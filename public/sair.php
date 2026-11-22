<?php
require __DIR__ . '/src/login.php';
iniciarSessaoLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); exit('Use o botão Sair.');
}
$token = $_POST['loginToken'] ?? null;
if (!is_string($token) || !hash_equals($_SESSION['loginToken'], $token)) {
    http_response_code(403); exit('Formulário expirado. Reabra a agenda.');
}
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'], 'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax']);
session_destroy();
header('Location: /login.php', true, 303);
