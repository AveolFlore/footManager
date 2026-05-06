CREATE TABLE galerie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seance_id INT NULL,
    titre VARCHAR(100),
    image_url VARCHAR(255) NOT NULL,
    description TEXT,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_galerie_seance
        FOREIGN KEY (seance_id)
        REFERENCES match_seance(id)
        ON DELETE SET NULL
);
