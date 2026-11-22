ALTER TABLE agenda
    MODIFY COLUMN cliente VARCHAR(100) NOT NULL,
    ADD COLUMN usuario_id INT UNSIGNED NULL,
    ADD INDEX idx_agenda_usuario_id (usuario_id);
