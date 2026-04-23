INSERT INTO sanction (
    joueur_id,
    reglement_id,
    presence_id,
    applique_par,
    montant,
    motif,
    statut,
    date_sanction
)
VALUES
(
    2,
    1,
    2,
    2,
    2000,
    'Retard constaté lors de la séance',
    'en_attente',
    CURDATE()
);