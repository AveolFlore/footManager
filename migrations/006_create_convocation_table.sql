CREATE TABLE convocation (
    id INT AUTO_INCREMENT PRIMARY KEY,

    match_id INT NOT NULL,
    joueur_id INT NOT NULL,

    equipe_match ENUM('A', 'B') NOT NULL,

    est_capitaine BOOLEAN DEFAULT FALSE,

    numero_maillot INT NULL,

    UNIQUE(match_id, joueur_id),

    CONSTRAINT fk_convocation_match
        FOREIGN KEY (match_id)
        REFERENCES match_seance(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_convocation_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);