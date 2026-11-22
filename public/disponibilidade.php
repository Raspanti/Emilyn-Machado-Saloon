<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/login.php';
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Allow: GET'); http_response_code(405);
        echo json_encode(['erro' => 'Use GET para consultar a disponibilidade.']); exit;
    }
    $data = isset($_GET['data']) && is_string($_GET['data']) ? $_GET['data'] : '';
    $tipo = isset($_GET['tipo']) && is_string($_GET['tipo']) ? $_GET['tipo'] : '';
    $dia = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    if (!$dia || $dia->format('Y-m-d') !== $data || !in_array($tipo, ['Manicure', 'Cabeleireira'], true)) {
        throw new DomainException('Informe uma data e um tipo de atendimento válidos.');
    }
    $ignorar = 0;
    if (!empty($_GET['editar'])) {
        iniciarSessaoLogin();
        if (!usuarioLogado()) { http_response_code(401); echo json_encode(['erro' => 'Entre novamente para editar.']); exit; }
        require_once __DIR__ . '/src/agenda.php';
        $ignorar = idAgenda($_GET, 'editar');
    }
    require __DIR__ . '/src/bdconnecta.php';
    if ($ignorar) buscarAgenda($conn, $ignorar);
    $stmt = mysqli_prepare($conn, "SELECT hora, horaFim FROM agenda WHERE data=? AND tipo=? AND situacao='Aberto' AND id != ? ORDER BY hora");
    mysqli_stmt_bind_param($stmt, 'ssi', $data, $tipo, $ignorar);
    mysqli_stmt_execute($stmt);
    $intervalos = [];
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC) as $item) {
        $intervalos[] = ['inicio' => substr($item['hora'], 0, 5), 'fim' => substr($item['horaFim'], 0, 5)];
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    echo json_encode(['data' => $data, 'tipo' => $tipo, 'ocupados' => $intervalos]);
} catch (DomainException $exception) {
    http_response_code(400); echo json_encode(['erro' => $exception->getMessage()]);
} catch (Throwable $exception) {
    http_response_code(503); echo json_encode(['erro' => 'Não foi possível consultar os horários. Tente novamente.']);
}
