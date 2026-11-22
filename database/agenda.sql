-- Apenas estrutura: nenhum cliente do dump enviado é importado.
-- Se a tabela já existir, o CREATE falha em vez de sobrescrever dados.
CREATE TABLE agenda (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    situacao VARCHAR(20) NOT NULL DEFAULT 'Aberto',
    cliente VARCHAR(50) NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    horaFim TIME NOT NULL,
    DTHR DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(15) NOT NULL,
    obs VARCHAR(500) NOT NULL DEFAULT '',
    INDEX agenda_horario (tipo, data, situacao, hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
