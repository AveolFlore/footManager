CREATE TABLE sanction (
    id INT AUTO_INCREMENT PRIMARY KEY,

    joueur_id INT NOT NULL,

    reglement_id INT NOT NULL,

    presence_id INT NULL,

    applique_par INT,

    montant INT,

    motif TEXT,

    statut ENUM(
        'en_attente',
        'payee'
    ) DEFAULT 'en_attente',

    date_sanction DATE,

    CONSTRAINT fk_sanction_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sanction_reglement
        FOREIGN KEY (reglement_id)
        REFERENCES reglement(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sanction_presence
        FOREIGN KEY (presence_id)
        REFERENCES presence(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_sanction_applique
        FOREIGN KEY (applique_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);