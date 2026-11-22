<?php
// O segredo fica fora da pasta pública e não entra no Git.
function configurarGoogle(): array
{
    static $config;
    if ($config !== null) return $config;
    $arquivo = dirname(__DIR__, 2) . '/config/google.local.php';
    $local = is_file($arquivo) ? require $arquivo : [];
    $config = array_merge(['client_id' => '', 'client_secret' => '', 'redirect_uri' => 'http://127.0.0.1:3000/google-retorno.php', 'admin_emails' => []], is_array($local) ? $local : []);
    return $config;
}

function googleConfigurado(): bool
{
    $config = configurarGoogle();
    $url = parse_url($config['redirect_uri']);
    $local = in_array($url['host'] ?? '', ['localhost', '127.0.0.1'], true);
    return $config['client_id'] !== '' && $config['client_secret'] !== ''
        && (($url['scheme'] ?? '') === 'https' || ($local && ($url['scheme'] ?? '') === 'http'))
        && ($url['path'] ?? '') === '/google-retorno.php' && !isset($url['user']) && !isset($url['pass'])
        && !isset($url['query']) && !isset($url['fragment']);
}

function iniciarSessaoLogin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('emilyn_sessao');
        $https = parse_url(configurarGoogle()['redirect_uri'], PHP_URL_SCHEME) === 'https';
        session_start(['cookie_httponly' => true, 'cookie_secure' => $https, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true, 'use_only_cookies' => true, 'cookie_path' => '/']);
    }
    header('Cache-Control: no-store, private');
    header('Referrer-Policy: no-referrer');
    if (empty($_SESSION['loginToken'])) $_SESSION['loginToken'] = bin2hex(random_bytes(32));
}

function usuarioLogado(): ?array
{
    $usuario = $_SESSION['usuarioGoogle'] ?? null;
    if (!is_array($usuario) || !isset($usuario['sub'], $usuario['email'], $usuario['nome'], $usuario['entrada'])) return null;
    if ((int) $usuario['entrada'] < time() - 8 * 3600) {
        unset($_SESSION['usuarioGoogle'], $_SESSION['agendaDados']);
        return null;
    }
    return $usuario;
}

function administradorAgenda(): bool
{
    $usuario = usuarioLogado();
    if (!$usuario) return false;
    $emails = array_map('strtolower', configurarGoogle()['admin_emails']);
    return in_array(strtolower($usuario['email']), $emails, true);
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

function iniciarLoginGoogle(): never
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); header('Allow: POST'); exit('Use o botão de entrada.');
    }
    $token = $_POST['loginToken'] ?? null;
    if (!is_string($token) || !hash_equals($_SESSION['loginToken'], $token)) {
        http_response_code(403); exit('Formulário expirado. Reabra a página.');
    }
    if (!googleConfigurado()) {
        $_SESSION['loginErro'] = 'O login com Google ainda não foi configurado pelo responsável pelo site.';
        header('Location: /login.php', true, 303); exit;
    }
    $verificador = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    $_SESSION['googlePedido'] = ['state' => bin2hex(random_bytes(32)), 'nonce' => bin2hex(random_bytes(32)), 'verificador' => $verificador, 'criado' => time()];
    $config = configurarGoogle();
    $params = ['client_id' => $config['client_id'], 'redirect_uri' => $config['redirect_uri'], 'response_type' => 'code', 'scope' => 'openid email profile', 'state' => $_SESSION['googlePedido']['state'], 'nonce' => $_SESSION['googlePedido']['nonce'], 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verificador, true)), '+/', '-_'), '='), 'code_challenge_method' => 'S256', 'prompt' => 'select_account'];
    header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    exit;
}

function validarIdentidadeGoogle(array $dados, string $clientId, string $nonce): array
{
    if (!in_array($dados['iss'] ?? '', ['https://accounts.google.com', 'accounts.google.com'], true)
        || ($dados['aud'] ?? null) !== $clientId
        || (isset($dados['azp']) && $dados['azp'] !== $clientId)
        || !is_int($dados['exp'] ?? null) || $dados['exp'] <= time()
        || !is_int($dados['iat'] ?? null) || $dados['iat'] > time() + 60
        || !is_string($dados['nonce'] ?? null) || !hash_equals($nonce, $dados['nonce'])
        || ($dados['email_verified'] ?? false) !== true
        || !is_string($dados['email'] ?? null) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)
        || !is_string($dados['sub'] ?? null) || !preg_match('/^[\x21-\x7e]{1,255}$/D', $dados['sub'])) {
        throw new DomainException('A resposta do Google não pôde ser validada. Tente entrar novamente.');
    }
    $nome = is_string($dados['name'] ?? null) ? trim($dados['name']) : '';
    if ($nome === '' || !mb_check_encoding($nome, 'UTF-8')) $nome = $dados['email'];
    return ['sub' => $dados['sub'], 'email' => $dados['email'], 'nome' => mb_substr($nome, 0, 50), 'entrada' => time()];
}

function receberLoginGoogle(): never
{
    $pedido = $_SESSION['googlePedido'] ?? [];
    unset($_SESSION['googlePedido']); // Estado e código só podem ser usados uma vez.
    try {
        $state = $_GET['state'] ?? null;
        if (!googleConfigurado() || !is_string($state) || empty($pedido['state'])
            || !hash_equals($pedido['state'], $state) || $pedido['criado'] < time() - 600) {
            throw new DomainException('A tentativa de entrada expirou. Use o botão para tentar novamente.');
        }
        if (isset($_GET['error'])) throw new DomainException('Entrada cancelada. Nenhum agendamento foi criado.');
        $codigo = $_GET['code'] ?? null;
        if (!is_string($codigo) || $codigo === '' || strlen($codigo) > 4096) throw new DomainException('Resposta de entrada inválida.');
        $config = configurarGoogle();
        $curl = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_FOLLOWLOCATION => false, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'], CURLOPT_POSTFIELDS => http_build_query(['code' => $codigo, 'client_id' => $config['client_id'], 'client_secret' => $config['client_secret'], 'redirect_uri' => $config['redirect_uri'], 'grant_type' => 'authorization_code', 'code_verifier' => $pedido['verificador']])]);
        $resposta = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($resposta === false || $status !== 200) throw new DomainException('Não foi possível concluir a entrada com Google. Tente novamente.');
        $tokens = json_decode($resposta, true, 32, JSON_THROW_ON_ERROR);
        $idToken = $tokens['id_token'] ?? null;
        if (!is_string($idToken) || count($partes = explode('.', $idToken)) !== 3) throw new DomainException('Resposta do Google inválida.');
        // Aceita ID token SOMENTE desta troca direta, autenticada por segredo e HTTPS.
        // Nunca recebe tokens do navegador. Google documenta essa garantia no server flow.
        $payload = base64_decode(strtr($partes[1], '-_', '+/'), true);
        if ($payload === false) throw new DomainException('Resposta do Google inválida.');
        $dados = json_decode($payload, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($dados)) throw new DomainException('Resposta do Google inválida.');
        $usuario = validarIdentidadeGoogle($dados, $config['client_id'], $pedido['nonce']);
        session_regenerate_id(true);
        $_SESSION['usuarioGoogle'] = $usuario;
        $_SESSION['agendaToken'] = bin2hex(random_bytes(32));
        $_SESSION['loginToken'] = bin2hex(random_bytes(32));
        unset($_SESSION['agendaDados'], $_SESSION['agendaErro'], $_SESSION['loginErro']);
        $servico = $_SESSION['loginServico'] ?? '';
        unset($_SESSION['loginServico']);
        global $services;
        $destino = '/agendamento.php' . (isset($services[$servico]) ? '?servico=' . rawurlencode($servico) : '');
        header('Location: ' . $destino, true, 303); exit;
    } catch (DomainException $exception) {
        $_SESSION['loginErro'] = $exception->getMessage();
    } catch (Throwable $exception) {
        // Não registra respostas, tokens, códigos ou segredos.
        error_log('Login Google: falha ao processar a resposta.');
        $_SESSION['loginErro'] = 'Não foi possível entrar agora. Tente novamente.';
    }
    header('Location: /login.php', true, 303); exit;
}
