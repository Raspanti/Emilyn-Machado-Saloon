<?php
// Usa o banco do Docker (db), nunca a hospedagem remota do arquivo enviado.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect(
    getenv('DB_HOST') ?: 'db',
    getenv('DB_USER') ?: '',
    getenv('DB_PASSWORD') ?: '',
    getenv('DB_NAME') ?: ''
);
mysqli_set_charset($conn, 'utf8mb4');
