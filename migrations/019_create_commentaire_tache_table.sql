CREATE TABLE commentaire_tache (
    id INT AUTO_INCREMENT PRIMARY KEY,

    tache_id INT NOT NULL,

    auteur_id INT NOT NULL,

    message TEXT NOT NULL,

    date_message DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_commentaire_tache
        FOREIGN KEY (tache_id)
        REFERENCES tache(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_commentaire_auteur
        FOREIGN KEY (auteur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);