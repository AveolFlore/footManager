<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

if (!in_array($_SESSION['user']['role'], ['president', 'organisateur'])) {
    header('Location: /page-match');
    exit;
}

use Controllers\Convocation\ConvocationController;
use Controllers\MatchSeanceController;

$matchController      = new MatchSeanceController();
$convocationController = new ConvocationController();

$id        = $_GET['id'] ?? null;
$match     = $matchController->read_one((int) $id);
$suggestion = $convocationController->get_suggestion();
$deja_convoques = $convocationController->index((int) $id);

// ids des joueurs déjà convoqués
$ids_convoques = array_column($deja_convoques, 'joueur_id');

// tous les joueurs validés
use Config\Database;

$db      = new Database();
$pdo     = $db->connect();
$joueurs = $pdo->query(
    "SELECT id, nom, prenom, poste, numero_maillot
     FROM users
     WHERE statut = 'valide' AND role = 'joueur'
     ORDER BY nom"
)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Gérer les convocations";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">

                <!-- Retour -->
                <a href="/page-matchdetail?id=<?= $id ?>"
                    class="flex items-center gap-2 text-slate-500 hover:text-slate-700 mb-8 font-semibold">
                    <i class="fas fa-arrow-left"></i>
                    Retour au match
                </a>

                <!-- Message flash -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl <?= str_contains($_GET['msg'], 'Erreur') || str_contains($_GET['msg'], 'ne peut pas') ? 'bg-gradient-to-r from-red-50 to-pink-50 text-red-700 border border-red-200' : 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200' ?> font-semibold shadow-sm">
                        <i class="fas fa-info-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <!-- Infos match -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 mb-8 shadow-xl">
                    <h1 class="text-3xl font-extrabold text-slate-800 mb-3 flex items-center gap-3">
                        <i class="fas fa-users text-green-600"></i>
                        Équipe A vs Équipe B
                    </h1>
                    <p class="text-lg text-slate-500">
                        <i class="fas fa-calendar mr-2"></i>
                        <?= date('d/m/Y H:i', strtotime($match['date'])) ?>
                        <span class="mx-3 text-slate-300">•</span>
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?= htmlspecialchars($match['lieu']) ?>
                    </p>
                </div>

                <!-- Suggestion auto -->
                <?php if (!empty($suggestion)): ?>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-3xl p-8 mb-8 shadow-lg shadow-blue-100">
                        <p class="text-lg font-extrabold text-blue-700 mb-6 flex items-center gap-3">
                            <i class="fas fa-star text-yellow-500 text-2xl"></i>
                            Suggestion automatique — top <?= count($suggestion) ?> joueurs ce mois
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <?php foreach ($suggestion as $s): ?>
                                <div class="flex items-center justify-between bg-white rounded-2xl px-5 py-4 border border-blue-100 shadow-sm">
                                    <span class="font-bold text-slate-800 text-lg">
                                        <?= htmlspecialchars($s['nom']) ?> <?= htmlspecialchars($s['prenom']) ?>
                                    </span>
                                    <span class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 px-4 py-2 rounded-full font-bold">
                                        <i class="fas fa-trophy"></i>
                                        <?= $s['score'] ?> pts
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulaire convocations -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-xl">
                    <h2 class="text-2xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-edit text-green-600"></i>
                        Sélectionner les joueurs
                    </h2>

                    <!-- Compteurs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-6 rounded-3xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 shadow-sm">
                            <p class="text-sm font-bold text-blue-700 mb-2 uppercase tracking-wide">Équipe A</p>
                            <p class="text-4xl font-extrabold" id="countA">0 / 10 joueurs</p>
                        </div>
                        <div class="p-6 rounded-3xl border-2 border-red-200 bg-gradient-to-br from-red-50 to-pink-50 shadow-sm">
                            <p class="text-sm font-bold text-red-700 mb-2 uppercase tracking-wide">Équipe B</p>
                            <p class="text-4xl font-extrabold" id="countB">0 / 10 joueurs</p>
                        </div>
                    </div>

                    <form action="/convocation-convocsave" method="POST" id="convocationForm">
                        <input type="hidden" name="match_id" value="<?= $id ?>">

                        <?php if (empty($joueurs)): ?>
                            <div class="text-center py-16 text-slate-400">
                                <i class="fas fa-user-slash text-7xl mb-6"></i>
                                <p class="text-xl font-semibold">Aucun joueur disponible.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4 mb-8" id="playersList">
                                <?php foreach ($joueurs as $joueur): ?>
                                    <?php
                                    // Récupérer les données du joueur déjà convoqué si présent
                                    $convoqueData = null;
                                    foreach ($deja_convoques as $dc) {
                                        if ($dc['joueur_id'] == $joueur['id']) {
                                            $convoqueData = $dc;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 p-6 border-2 border-slate-100 rounded-3xl hover:bg-slate-50 transition-all shadow-sm" id="player-row-<?= $joueur['id'] ?>">
                                        <!-- Checkbox sélection -->
                                        <input type="checkbox"
                                            name="joueurs[<?= $joueur['id'] ?>][selectionne]"
                                            value="1"
                                            class="w-6 h-6 accent-green-600 player-checkbox cursor-pointer"
                                            data-player-id="<?= $joueur['id'] ?>"
                                            <?= in_array($joueur['id'], $ids_convoques) ? 'checked' : '' ?>>

                                        <!-- Nom + poste -->
                                        <div class="flex-1">
                                            <p class="text-lg font-bold text-slate-800">
                                                <?= htmlspecialchars($joueur['nom']) ?>
                                                <?= htmlspecialchars($joueur['prenom']) ?>
                                            </p>
                                            <p class="text-sm text-slate-500 font-semibold"><?= $joueur['poste'] ?></p>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4">
                                            <!-- Équipe A ou B -->
                                            <select name="joueurs[<?= $joueur['id'] ?>][equipe]"
                                                class="text-base border-2 border-slate-200 rounded-2xl px-5 py-3 focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white font-semibold team-select"
                                                data-player-id="<?= $joueur['id'] ?>">
                                                <option value="A" <?= $convoqueData && $convoqueData['equipe_match'] === 'A' ? 'selected' : '' ?>>Équipe A</option>
                                                <option value="B" <?= $convoqueData && $convoqueData['equipe_match'] === 'B' ? 'selected' : '' ?>>Équipe B</option>
                                            </select>

                                            <!-- Capitaine -->
                                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-600 cursor-pointer">
                                                <input type="checkbox"
                                                    name="joueurs[<?= $joueur['id'] ?>][capitaine]"
                                                    value="1"
                                                    class="w-5 h-5 accent-green-600 captain-checkbox cursor-pointer"
                                                    data-player-id="<?= $joueur['id'] ?>"
                                                    data-team="<?= $convoqueData ? $convoqueData['equipe_match'] : 'A' ?>"
                                                    <?= $convoqueData && $convoqueData['est_capitaine'] ? 'checked' : '' ?>>
                                                <i class="fas fa-crown text-yellow-600 mr-1"></i>
                                                Capitaine
                                            </label>

                                            <!-- Numéro maillot -->
                                            <input type="number"
                                                name="joueurs[<?= $joueur['id'] ?>][maillot]"
                                                value="<?= $convoqueData ? $convoqueData['numero_maillot'] : ($joueur['numero_maillot'] ?? '') ?>"
                                                placeholder="N°"
                                                min="1" max="99"
                                                class="w-20 text-base border-2 border-slate-200 rounded-2xl px-5 py-3 text-center font-extrabold focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Boutons -->
                            <div class="flex flex-col md:flex-row gap-4">
                                <button type="submit" name="convocsave" value="Enregistrer"
                                    class="flex-1 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-lg font-extrabold rounded-2xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Enregistrer et publier
                                </button>
                                <a href="/page-matchdetail?id=<?= $id ?>"
                                    class="flex-1 px-8 py-4 border-2 border-slate-200 text-slate-700 text-lg font-bold rounded-2xl hover:bg-slate-50 transition-all bg-white shadow-sm">
                                    Annuler
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
        const MAX_PLAYERS = 10;

        // Fonction pour mettre à jour les compteurs et désactiver les checkboxes
        function updateCounts() {
            let countA = 0;
            let countB = 0;

            // D'abord compter les joueurs sélectionnés
            document.querySelectorAll('.player-checkbox').forEach(checkbox => {
                if (checkbox.checked) {
                    const playerId = checkbox.dataset.playerId;
                    const teamSelect = document.querySelector(`.team-select[data-player-id="${playerId}"]`);
                    if (teamSelect.value === 'A') {
                        countA++;
                    } else {
                        countB++;
                    }
                }
            });

            // Mettre à jour les textes et couleurs des compteurs
            const countAElement = document.getElementById('countA');
            const countBElement = document.getElementById('countB');

            countAElement.textContent = `${countA} / ${MAX_PLAYERS} joueurs`;
            countBElement.textContent = `${countB} / ${MAX_PLAYERS} joueurs`;

            countAElement.className = countA >= MAX_PLAYERS ? 'text-4xl font-extrabold text-red-600' : 'text-4xl font-extrabold text-blue-700';
            countBElement.className = countB >= MAX_PLAYERS ? 'text-4xl font-extrabold text-red-600' : 'text-4xl font-extrabold text-red-700';

            // Désactiver les checkboxes pour les équipes qui ont atteint la limite
            document.querySelectorAll('.player-checkbox').forEach(checkbox => {
                const playerId = checkbox.dataset.playerId;
                const teamSelect = document.querySelector(`.team-select[data-player-id="${playerId}"]`);
                const row = document.getElementById(`player-row-${playerId}`);

                // Si la checkbox n'est pas cochée, vérifier si l'équipe sélectionnée a atteint la limite
                if (!checkbox.checked) {
                    if (teamSelect.value === 'A' && countA >= MAX_PLAYERS) {
                        checkbox.disabled = true;
                        row.classList.add('opacity-50', 'cursor-not-allowed');
                    } else if (teamSelect.value === 'B' && countB >= MAX_PLAYERS) {
                        checkbox.disabled = true;
                        row.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        checkbox.disabled = false;
                        row.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    // Toujours activer les checkboxes cochées
                    checkbox.disabled = false;
                    row.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        // Mettre à jour la team du capitaine quand la team change
        document.querySelectorAll('.team-select').forEach(select => {
            select.addEventListener('change', function() {
                const playerId = this.dataset.playerId;
                const captainCheckbox = document.querySelector(`.captain-checkbox[data-player-id="${playerId}"]`);
                if (captainCheckbox) {
                    captainCheckbox.dataset.team = this.value;
                }
                updateCounts();
            });
        });

        // Gérer les clicks sur les checkboxes de capitaine
        document.querySelectorAll('.captain-checkbox').forEach(captainCheckbox => {
            captainCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const team = this.dataset.team;
                    // Désélectionner les autres capitaines de la même équipe
                    document.querySelectorAll(`.captain-checkbox[data-team="${team}"]`).forEach(otherCheckbox => {
                        if (otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });

        // Ajouter écouteurs sur les checkboxes de joueurs
        document.querySelectorAll('.player-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateCounts);
        });

        // Appeler updateCounts au chargement initial
        updateCounts();
    </script>
</body>

</html>
