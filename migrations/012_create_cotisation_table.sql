CREATE TABLE cotisation (
    id INT AUTO_INCREMENT PRIMARY KEY,

    joueur_id INT NOT NULL,

    montant INT,

    mois VARCHAR(20),

    annee INT,

    statut ENUM(
        'paye',
        'non_paye'
    ) DEFAULT 'non_paye',

    date_paiement DATE NULL,

    CONSTRAINT fk_cotisation_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);