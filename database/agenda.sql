CREATE TABLE agenda (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    situacao VARCHAR(20) NOT NULL DEFAULT 'Aberto',
    cliente VARCHAR(100) NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    horaFim TIME NOT NULL,
    DTHR DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(15) NOT NULL,
    obs VARCHAR(500) NOT NULL DEFAULT '',
    usuario_id INT UNSIGNED NULL,
    INDEX idx_agenda_usuario_id (usuario_id),
    INDEX agenda_horario (tipo, data, situacao, hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
