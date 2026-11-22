-- Não modifica agenda nem atribui registros antigos a clientes.
CREATE TABLE agenda_contas (
    agenda_id INT NOT NULL PRIMARY KEY,
    google_sub VARCHAR(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    INDEX agenda_contas_google (google_sub),
    CONSTRAINT agenda_contas_agenda FOREIGN KEY (agenda_id) REFERENCES agenda(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
