INSERT INTO activite_log (
    auteur_id,
    type_action,
    description,
    lien,
    date_action
)
VALUES
(
    2,
    'match_cree',
    'Paul Martin a créé un nouveau match.',
    '/matchs/1',
    NOW()
),
(
    2,
    'sanction_appliquee',
    'Une sanction pour retard a été appliquée.',
    '/sanctions/1',
    NOW()
);