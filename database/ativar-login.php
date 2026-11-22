<?php
// Executar somente no terminal: docker compose exec -T app php database/ativar-login.php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../public/src/bdconnecta.php';
$existe = mysqli_query($conn, "SHOW TABLES LIKE 'agenda_contas'");
if (mysqli_num_rows($existe)) { echo "Vínculos da agenda já existem; nenhuma alteração.\n"; exit; }
$backup = __DIR__ . '/../.dev-backups/' . date('Ymd-His') . '-agenda-pre-login.sql';
$handle = fopen($backup, 'x');
if (!$handle) throw new RuntimeException('Não foi possível criar o backup.');
chmod($backup, 0600);
$estrutura = mysqli_fetch_row(mysqli_query($conn, 'SHOW CREATE TABLE agenda'));
fwrite($handle, "-- Backup da agenda antes de criar vínculos Google. Não importar sobre tabela existente.\n" . $estrutura[1] . ";\n");
$resultado = mysqli_query($conn, 'SELECT * FROM agenda');
while ($linha = mysqli_fetch_assoc($resultado)) {
    $colunas = implode(',', array_map(fn($nome) => '`' . $nome . '`', array_keys($linha)));
    $valores = implode(',', array_map(fn($valor) => $valor === null ? 'NULL' : "'" . mysqli_real_escape_string($conn, (string) $valor) . "'", $linha));
    fwrite($handle, "INSERT INTO agenda ($colunas) VALUES ($valores);\n");
}
fclose($handle);
mysqli_query($conn, file_get_contents(__DIR__ . '/002-agenda-contas.sql'));
echo "Backup local criado; tabela de vínculos Google criada. Agenda original preservada.\n";
