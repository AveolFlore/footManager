INSERT INTO presence (
    seance_id,
    joueur_id,
    type_presence,
    marque_par,
    date_marquage,
    note
)
VALUES
(
    1,
    1,
    'present',
    2,
    CURDATE(),
    NULL
),
(
    1,
    2,
    'retard',
    2,
    CURDATE(),
    'Arrivé 15 minutes en retard'
);