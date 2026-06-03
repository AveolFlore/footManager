<?php
$pageTitle = "Marquer les présences";

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marquer les présences - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6">
            <a href="/presence" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium mb-6">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-clipboard-check mr-3 text-green-600"></i>
                Marquer les présences
            </h1>

            <div class="bg-white rounded-lg p-4 border border-gray-200 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    <?php
                        $dateObj = new DateTime($seance['date']);
                        $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        $mois_fr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                        $jour = $jours[$dateObj->format('w')];
                        $num_jour = $dateObj->format('d');
                        $mois = $mois_fr[(int)$dateObj->format('m')];
                        $annee = $dateObj->format('Y');
                        echo ucfirst($seance['type']) . ' - ' . "$jour $num_jour $mois $annee";
                    ?>
                </h2>
                <p class="text-gray-600">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <?= htmlspecialchars($seance['lieu']) ?>
                    <span class="mx-3 text-gray-400">•</span>
                    <i class="fas fa-clock mr-2"></i>
                    <?= date('H:i', strtotime($seance['date'])) ?>
                </p>
            </div>

            <form method="POST" action="/presence-marquer" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <input type="hidden" name="seance_id" value="<?= $seance['id'] ?>">

                <!-- Barre d'actions rapide -->
                <div class="mb-6 flex flex-wrap gap-3">
                    <button type="button" onclick="marquerTous('present')"
                        class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition font-medium">
                        <i class="fas fa-check mr-2"></i>Tous présents
                    </button>
                    <button type="button" onclick="marquerTous('absent')"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Tous absents
                    </button>
                </div>

                <!-- Liste des joueurs -->
                <div class="space-y-4 mb-8">
                    <?php foreach ($joueurs as $joueur): ?>
                        <?php
                        $presenceExistante = $presencesMap[$joueur['id']] ?? null;
                        $typeSelectionne = $presenceExistante ? $presenceExistante['type_presence'] : 'present';
                        $noteExistante = $presenceExistante ? $presenceExistante['note'] : '';
                        ?>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <!-- Infos joueur -->
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                    <?= $joueur['numero_maillot'] ?? '?' ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-lg">
                                        <?= htmlspecialchars($joueur['nom']) ?> <?= htmlspecialchars($joueur['prenom']) ?>
                                    </p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($joueur['poste'] ?? '-') ?></p>
                                </div>
                            </div>

                            <!-- Sélection type de présence -->
                            <div class="flex items-center gap-3 flex-wrap">
                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition 
                                    <?= $typeSelectionne === 'present' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' ?>">
                                    <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="present"
                                        class="presence-radio"
                                        <?= $typeSelectionne === 'present' ? 'checked' : '' ?>
                                        onchange="changerType(this, <?= $joueur['id'] ?>)">
                                    <span><i class="fas fa-check mr-1"></i>Présent</span>
                                </label>

                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition 
                                    <?= $typeSelectionne === 'retard' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' ?>">
                                    <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="retard"
                                        class="presence-radio"
                                        <?= $typeSelectionne === 'retard' ? 'checked' : '' ?>
                                        onchange="changerType(this, <?= $joueur['id'] ?>)">
                                    <span><i class="fas fa-clock mr-1"></i>Retard</span>
                                </label>

                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition 
                                    <?= $typeSelectionne === 'absent' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>">
                                    <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="absent"
                                        class="presence-radio"
                                        <?= $typeSelectionne === 'absent' ? 'checked' : '' ?>
                                        onchange="changerType(this, <?= $joueur['id'] ?>)">
                                    <span><i class="fas fa-times mr-1"></i>Absent</span>
                                </label>

                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition 
                                    <?= $typeSelectionne === 'excuse' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' ?>">
                                    <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="excuse"
                                        class="presence-radio"
                                        <?= $typeSelectionne === 'excuse' ? 'checked' : '' ?>
                                        onchange="changerType(this, <?= $joueur['id'] ?>)">
                                    <span><i class="fas fa-sticky-note mr-1"></i>Excusé</span>
                                </label>
                            </div>

                            <!-- Note -->
                            <div class="w-full md:w-64">
                                <input type="text"
                                    name="presences[<?= $joueur['id'] ?>][note]"
                                    value="<?= htmlspecialchars($noteExistante) ?>"
                                    placeholder="Note (optionnelle)"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Boutons de soumission -->
                <div class="flex flex-wrap gap-4 justify-end pt-6 border-t border-gray-200">
                    <a href="/presence"
                        class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les présences
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Fonction pour marquer tous les joueurs d'un type
        function marquerTous(type) {
            document.querySelectorAll(`input[value="${type}"]`).forEach(radio => {
                radio.checked = true;
                const joueurId = radio.name.match(/\[(\d+)\]/)[1];
                changerType(radio, joueurId);
            });
        }

        // Fonction pour changer le style lors de la sélection
        function changerType(radio, joueurId) {
            const container = radio.closest('.flex.flex-col');
            const labels = container.querySelectorAll('label');

            labels.forEach(label => {
                const input = label.querySelector('input');
                if (input.checked) {
                    // Style pour le label sélectionné
                    const type = input.value;
                    label.classList.remove('bg-green-100', 'text-green-700',
                        'bg-orange-100', 'text-orange-700',
                        'bg-red-100', 'text-red-700',
                        'bg-blue-100', 'text-blue-700',
                        'hover:bg-green-200', 'hover:bg-orange-200',
                        'hover:bg-red-200', 'hover:bg-blue-200');

                    if (type === 'present') {
                        label.classList.add('bg-green-600', 'text-white');
                    } else if (type === 'retard') {
                        label.classList.add('bg-orange-600', 'text-white');
                    } else if (type === 'absent') {
                        label.classList.add('bg-red-600', 'text-white');
                    } else if (type === 'excuse') {
                        label.classList.add('bg-blue-600', 'text-white');
                    }
                } else {
                    // Style pour les labels non sélectionnés
                    const type = input.value;
                    label.classList.remove('bg-green-600', 'bg-orange-600',
                        'bg-red-600', 'bg-blue-600', 'text-white');

                    if (type === 'present') {
                        label.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                    } else if (type === 'retard') {
                        label.classList.add('bg-orange-100', 'text-orange-700', 'hover:bg-orange-200');
                    } else if (type === 'absent') {
                        label.classList.add('bg-red-100', 'text-red-700', 'hover:bg-red-200');
                    } else if (type === 'excuse') {
                        label.classList.add('bg-blue-100', 'text-blue-700', 'hover:bg-blue-200');
                    }
                }
            });
        }

        // Initialiser les styles au chargement
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.presence-radio').forEach(radio => {
                if (radio.checked) {
                    const joueurId = radio.name.match(/\[(\d+)\]/)[1];
                    changerType(radio, joueurId);
                }
            });
        });
    </script>
</body>

</html>