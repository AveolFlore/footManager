INSERT INTO reglement (
    titre,
    description,
    montant_amende,
    type_infraction,
    statut,
    propose_par,
    date_creation
)
VALUES
(
    'Retard aux entraînements',
    'Tout joueur en retard paie une amende.',
    200,
    'retard',
    'actif',
    2,
    CURDATE()
),
(
    'Absence injustifiée',
    'Toute absence sans justification sera sanctionnée.',
    500,
    'absence',
    'actif',
    2,
    CURDATE()
);
