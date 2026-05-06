-- Nettoyage de la table
DELETE FROM match_seance;

INSERT INTO match_seance (
    type,
    date,
    lieu,
    description,
    statut,
    createur_id
)
VALUES
-- 1. Match terminé
(
    'match',
    '2024-10-15',
    'Stade Municipal',
    'Victoire 2-0 contre l''équipe locale',
    'termine',
    1
),
-- 2. Match terminé
(
    'match',
    '2024-11-05',
    'City Stade',
    'Match amical de reprise',
    'termine',
    1
),
-- 3. Match planifié (remplace "en cours")
(
    'match',
    CURDATE(),
    'Stade Municipal',
    'Match de championnat aujourd''hui',
    'planifie',
    1
),
-- 4. Match publié
(
    'match',
    '2026-05-01',
    'Stade Principal',
    'Match amical international',
    'publie',
    1
),
-- 5. Match publié
(
    'match',
    '2026-06-12',
    'Complexe Sportif',
    'Dernier match de la saison',
    'publie',
    1
);
