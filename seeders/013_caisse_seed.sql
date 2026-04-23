INSERT INTO caisse (
    type,
    libelle,
    montant,
    categorie,
    reference_id,
    enregistre_par,
    date_transaction
)
VALUES
(
    'entree',
    'Cotisation Avril - Jean Dupont',
    5000,
    'cotisation',
    1,
    2,
    CURDATE()
),
(
    'entree',
    'Amende retard',
    2000,
    'sanction',
    1,
    2,
    CURDATE()
),
(
    'sortie',
    'Achat de ballons',
    15000,
    'depense',
    NULL,
    2,
    CURDATE()
);