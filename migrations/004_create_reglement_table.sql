CREATE TABLE reglement (
    id INT AUTO_INCREMENT PRIMARY KEY,

    titre VARCHAR(200),

    description TEXT,

    montant_amende INT,

    type_infraction ENUM(
        'retard',
        'absence',
        'comportement',
        'autre'
    ),

    statut ENUM(
        'reflexion',
        'actif',
        'rejete'
    ) DEFAULT 'reflexion',

    propose_par INT,

    date_creation DATE,

    date_debut_vote DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reglement_propose
        FOREIGN KEY (propose_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);