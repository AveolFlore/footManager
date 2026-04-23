CREATE TABLE caisse (
    id INT AUTO_INCREMENT PRIMARY KEY,

    type ENUM('entree', 'sortie') NOT NULL,

    libelle VARCHAR(200),

    montant INT,

    categorie ENUM(
        'cotisation',
        'don',
        'sanction',
        'depense',
        'autre'
    ),

    reference_id INT NULL,

    enregistre_par INT,

    date_transaction DATE,

    CONSTRAINT fk_caisse_user
        FOREIGN KEY (enregistre_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);