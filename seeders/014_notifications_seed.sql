INSERT INTO notifications (
    destinataire_id,
    type,
    message,
    lien,
    lu,
    date_creation
)
VALUES
(
    1,
    'match_publie',
    'Un nouveau match a été publié.',
    '/matchs/1',
    FALSE,
    NOW()
),
(
    1,
    'sanction',
    'Vous avez reçu une sanction pour retard.',
    '/sanctions/1',
    FALSE,
    NOW()
);
