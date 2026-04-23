CREATE TABLE historique_equipe (
    id INT AUTO_INCREMENT PRIMARY KEY,

    joueur_id INT NOT NULL,
    equipe_id INT NOT NULL,

    date_entree DATE NOT NULL,
    date_sortie DATE NULL,

    motif VARCHAR(200),

    modifie_par INT,

    CONSTRAINT fk_hist_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_hist_equipe
        FOREIGN KEY (equipe_id)
        REFERENCES equipe(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_hist_modifie_par
        FOREIGN KEY (modifie_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);
