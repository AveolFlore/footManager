CREATE TABLE IF NOT EXISTS resultat_match (
    id INT AUTO_INCREMENT PRIMARY KEY,

    match_id INT UNIQUE NOT NULL,

    buts_equipe_a INT DEFAULT 0,
    buts_equipe_b INT DEFAULT 0,

    equipe_gagnante ENUM('A', 'B', 'nul')
    GENERATED ALWAYS AS (
        CASE
            WHEN buts_equipe_a > buts_equipe_b THEN 'A'
            WHEN buts_equipe_b > buts_equipe_a THEN 'B'
            ELSE 'nul'
        END
    ) STORED,

    saisie_par INT,

    CONSTRAINT fk_resultat_match
        FOREIGN KEY (match_id)
        REFERENCES match_seance(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_resultat_saisie
        FOREIGN KEY (saisie_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);