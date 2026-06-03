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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6">
            <!-- Retour -->
            <a href="/page-matchdetail?id=<?= $id ?>"
                class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
                ← Retour au match
            </a>

            <!-- Message flash -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-4 px-4 py-3 rounded-lg <?= str_contains($_GET['msg'], 'Erreur') || str_contains($_GET['msg'], 'ne peut pas') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <!-- Infos match -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <h1 class="text-lg font-semibold text-gray-800">
                    Équipe A vs Équipe B
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    <?= date('d/m/Y H:i', strtotime($match['date'])) ?>
                    — <?= htmlspecialchars($match['lieu']) ?>
                </p>
            </div>

            <!-- Suggestion auto -->
            <?php if (!empty($suggestion)): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <p class="text-sm font-medium text-blue-700 mb-3">
                        Suggestion automatique — top 8 joueurs ce mois
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <?php foreach ($suggestion as $s): ?>
                            <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 text-sm border border-blue-100">
                                <span class="font-medium text-gray-700">
                                    <?= htmlspecialchars($s['nom']) ?> <?= htmlspecialchars($s['prenom']) ?>
                                </span>
                                <span class="text-blue-600 font-medium ml-2">
                                    <?= $s['score'] ?> pts
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulaire convocations -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    Sélectionner les joueurs
                </h2>

                <!-- Compteurs -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-lg border border-blue-200 bg-blue-50">
                        <p class="text-sm text-blue-700 font-semibold mb-1">Équipe A</p>
                        <p class="text-2xl font-bold text-blue-600" id="countA">0 / 8 joueurs</p>
                    </div>
                    <div class="p-4 rounded-lg border border-red-200 bg-red-50">
                        <p class="text-sm text-red-700 font-semibold mb-1">Équipe B</p>
                        <p class="text-2xl font-bold text-red-600" id="countB">0 / 8 joueurs</p>
                    </div>
                </div>

                <form action="/convocation-convocsave" method="POST" id="convocationForm">
                    <input type="hidden" name="match_id" value="<?= $id ?>">

                    <?php if (empty($joueurs)): ?>
                        <p class="text-sm text-gray-400">Aucun joueur disponible.</p>

                    <?php else: ?>
                        <div class="space-y-2 mb-6" id="playersList">
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
                                <div class="flex items-center gap-4 p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                    <!-- Checkbox sélection -->
                                    <input type="checkbox"
                                        name="joueurs[<?= $joueur['id'] ?>][selectionne]"
                                        value="1"
                                        class="w-4 h-4 accent-green-600 player-checkbox"
                                        data-player-id="<?= $joueur['id'] ?>"
                                        <?= in_array($joueur['id'], $ids_convoques) ? 'checked' : '' ?>>

                                    <!-- Nom + poste -->
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">
                                            <?= htmlspecialchars($joueur['nom']) ?>
                                            <?= htmlspecialchars($joueur['prenom']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400"><?= $joueur['poste'] ?></p>
                                    </div>

                                    <!-- Équipe A ou B -->
                                    <select name="joueurs[<?= $joueur['id'] ?>][equipe]"
                                        class="text-sm border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-500 team-select"
                                        data-player-id="<?= $joueur['id'] ?>">
                                        <option value="A" <?= $convoqueData && $convoqueData['equipe_match'] === 'A' ? 'selected' : '' ?>>Équipe A</option>
                                        <option value="B" <?= $convoqueData && $convoqueData['equipe_match'] === 'B' ? 'selected' : '' ?>>Équipe B</option>
                                    </select>

                                    <!-- Capitaine -->
                                    <label class="flex items-center gap-1 text-xs text-gray-500">
                                        <input type="checkbox"
                                            name="joueurs[<?= $joueur['id'] ?>][capitaine]"
                                            value="1"
                                            class="w-3 h-3 accent-green-600 captain-checkbox"
                                            data-player-id="<?= $joueur['id'] ?>"
                                            data-team="<?= $convoqueData ? $convoqueData['equipe_match'] : 'A' ?>"
                                            <?= $convoqueData && $convoqueData['est_capitaine'] ? 'checked' : '' ?>>
                                        Cap.
                                    </label>

                                    <!-- Numéro maillot -->
                                    <input type="number"
                                        name="joueurs[<?= $joueur['id'] ?>][maillot]"
                                        value="<?= $convoqueData ? $convoqueData['numero_maillot'] : ($joueur['numero_maillot'] ?? '') ?>"
                                        placeholder="N°"
                                        min="1" max="99"
                                        class="w-16 text-sm border border-gray-300 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Boutons -->
                        <div class="flex gap-3">
                            <button type="submit" name="convocsave" value="Enregistrer"
                                class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                Enregistrer et publier
                            </button>
                            <a href="/page-matchdetail?id=<?= $id ?>"
                                class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                Annuler
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Fonction pour mettre à jour les compteurs
        function updateCounts() {
            let countA = 0;
            let countB = 0;

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

            document.getElementById('countA').textContent = `${countA} / 8 joueurs`;
            document.getElementById('countB').textContent = `${countB} / 8 joueurs`;
            document.getElementById('countA').className = countA > 8 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-blue-600';
            document.getElementById('countB').className = countB > 8 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-red-600';
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
