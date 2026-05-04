CREATE TABLE IF NOT EXISTS performance (
    id INT AUTO_INCREMENT PRIMARY KEY,

    seance_id INT NOT NULL,
    joueur_id INT NOT NULL,

    buts INT DEFAULT 0,
    passes INT DEFAULT 0,
    equipe_type ENUM('A', 'B') NOT NULL,

    points_total INT
    GENERATED ALWAYS AS (
        (buts * 3) + (passes * 2)
    ) STORED,

    date_enregistrement DATE,

    CONSTRAINT fk_perf_seance
        FOREIGN KEY (seance_id)
        REFERENCES match_seance(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_perf_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);