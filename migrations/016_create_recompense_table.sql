CREATE TABLE recompense (
    id INT AUTO_INCREMENT PRIMARY KEY,

    joueur_id INT NOT NULL,

    type ENUM('mois', 'annee'),

    periode VARCHAR(10),

    total_points INT,

    date_attribution DATE,

    CONSTRAINT fk_recompense_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);