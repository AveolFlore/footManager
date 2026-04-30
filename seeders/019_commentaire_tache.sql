-- Commentaires exemples sur les taches
-- Utilise uniquement auteur_id 1 et 2 (users existants)

INSERT INTO commentaire_tache (tache_id, auteur_id, message, date_message)
VALUES
(
    2,
    2,
    'J ai commence a installer les buts. Il manque un piquet pour le but cote nord, je signale au bureau.',
    DATE_SUB(NOW(), INTERVAL 30 MINUTE)
),
(
    2,
    1,
    'Merci pour l info. On va regler ca avant jeudi. Continue avec le reste.',
    DATE_SUB(NOW(), INTERVAL 15 MINUTE)
),
(
    5,
    1,
    'La tache est en retard. Pourquoi les ballons ne sont pas encore gonfles ?',
    DATE_SUB(NOW(), INTERVAL 1 HOUR)
),
(
    4,
    2,
    'Eau apportee : 12 bouteilles. J ai garde le recu pour le remboursement.',
    DATE_SUB(NOW(), INTERVAL 2 DAY)
);