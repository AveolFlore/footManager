CREATE TABLE vote (
    id INT AUTO_INCREMENT PRIMARY KEY,

    reglement_id INT NOT NULL,
    joueur_id INT NOT NULL,

    choix ENUM('oui', 'non') NOT NULL,

    date_vote DATE,

    UNIQUE(reglement_id, joueur_id),

    CONSTRAINT fk_vote_reglement
        FOREIGN KEY (reglement_id)
        REFERENCES reglement(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_vote_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);