CREATE TABLE tache (
    id INT AUTO_INCREMENT PRIMARY KEY,

    titre VARCHAR(200) NOT NULL,

    description TEXT,

    categorie_id INT NOT NULL,

    seance_id INT NULL,

    assigne_a INT NOT NULL,

    assigne_par INT NOT NULL,

    priorite ENUM(
        'haute',
        'moyenne',
        'faible'
    ) DEFAULT 'moyenne',

    statut ENUM(
        'a_faire',
        'en_cours',
        'termine',
        'en_retard'
    ) DEFAULT 'a_faire',

    deadline DATETIME NOT NULL,

    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,

    date_cloture DATETIME NULL,

    commentaire_cloture TEXT NULL,

    recurrente BOOLEAN DEFAULT FALSE,

    intervalle_jours INT NULL,

    CONSTRAINT fk_tache_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES categorie_tache(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_tache_seance
        FOREIGN KEY (seance_id)
        REFERENCES match_seance(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_tache_assigne_a
        FOREIGN KEY (assigne_a)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_tache_assigne_par
        FOREIGN KEY (assigne_par)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE INDEX idx_tache_assigne_a  ON tache(assigne_a);
CREATE INDEX idx_tache_statut     ON tache(statut);
CREATE INDEX idx_tache_deadline   ON tache(deadline);
CREATE INDEX idx_tache_seance     ON tache(seance_id);
CREATE INDEX idx_tache_categorie  ON tache(categorie_id);