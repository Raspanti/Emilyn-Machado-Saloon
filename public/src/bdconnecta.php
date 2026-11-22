<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexaoHospedagem = dirname(__DIR__) . '/bdconnecta.php';

if (!getenv('DB_HOST') && is_file($conexaoHospedagem)) {
    require $conexaoHospedagem;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
} else {
    $conn = mysqli_connect(
        getenv('DB_HOST') ?: 'db',
        getenv('DB_USER') ?: '',
        getenv('DB_PASSWORD') ?: '',
        getenv('DB_NAME') ?: ''
    );
}

mysqli_set_charset($conn, 'utf8mb4');
