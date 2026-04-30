-- Tâches exemples réalistes
-- Utilise uniquement les IDs users qui existent : 1 (Dupont/joueur) et 2 (Martin/entraineur)
-- assigne_par = 1, assigne_a = 1 ou 2 uniquement
-- seance_id = NULL partout (match_seance peut être vide)

INSERT INTO tache (
    titre,
    description,
    categorie_id,
    seance_id,
    assigne_a,
    assigne_par,
    priorite,
    statut,
    deadline,
    date_creation,
    recurrente,
    intervalle_jours
)
VALUES
(
    'Laver les maillots apres le match',
    'Laver tous les maillots et chasubles. Machine disponible au local.',
    1, NULL, 1, 1, 'haute', 'a_faire',
    DATE_ADD(NOW(), INTERVAL 1 DAY), NOW(), 1, 7
),
(
    'Preparer le terrain avant entrainement',
    'Placer les buts, les cones et les jalons. Arriver 45 min avant.',
    2, NULL, 2, 1, 'haute', 'en_cours',
    DATE_ADD(NOW(), INTERVAL 2 DAY), NOW(), 1, 3
),
(
    'Nettoyer le vestiaire apres seance',
    'Balai + serpillere. Les produits sont dans l armoire du couloir.',
    3, NULL, 1, 1, 'haute', 'a_faire',
    DATE_ADD(NOW(), INTERVAL 1 DAY), NOW(), 1, 3
),
(
    'Apporter l eau pour l entrainement',
    'Prevoir 10 bouteilles minimum. Budget dans la caisse du club.',
    4, NULL, 2, 1, 'haute', 'termine',
    DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 1, 3
),
(
    'Gonfler les ballons avant la seance',
    'Verifier la pression des 6 ballons. La pompe est dans le local.',
    1, NULL, 1, 1, 'haute', 'en_retard',
    DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY), 1, 3
),
(
    'Relancer les cotisations impayees',
    'Envoyer un message aux joueurs n ayant pas paye ce mois.',
    5, NULL, 2, 1, 'moyenne', 'a_faire',
    DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), 0, NULL
);