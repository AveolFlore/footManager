<?php
$pageTitle = "Gestion des Présences";

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;

// Calculer les stats globales
$totalPresents = 0;
$totalAbsents = 0;
$totalRetards = 0;
$totalSeancesJoueurs = 0;

foreach ($joueursStats as $joueur) {
    $totalPresents += $joueur['nb_presents'];
    $totalAbsents += $joueur['nb_absents'];
    $totalRetards += $joueur['nb_retards'];
    $totalSeancesJoueurs += $joueur['total_seances'];
}

$tauxPresence = $totalSeancesJoueurs > 0 ? round(($totalPresents / $totalSeancesJoueurs) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Présences - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6 md:ml-0">
            <!-- Alertes -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'presences_enregistrees'): ?>
                    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        Présences enregistrées avec succès !
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Statistiques globales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Taux de présence</p>
                            <p class="text-3xl font-bold text-green-600"><?= $tauxPresence ?>%</p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Présents</p>
                            <p class="text-3xl font-bold text-blue-600"><?= $totalPresents ?></p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Absents</p>
                            <p class="text-3xl font-bold text-red-600"><?= $totalAbsents ?></p>
                        </div>
                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Retards</p>
                            <p class="text-3xl font-bold text-orange-600"><?= $totalRetards ?></p>
                        </div>
                        <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <form method="GET" action="/presence" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mois</label>
                        <select name="mois" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                            <?php
                            $mois_fr = [
                                1 => 'Janvier',
                                2 => 'Février',
                                3 => 'Mars',
                                4 => 'Avril',
                                5 => 'Mai',
                                6 => 'Juin',
                                7 => 'Juillet',
                                8 => 'Août',
                                9 => 'Septembre',
                                10 => 'Octobre',
                                11 => 'Novembre',
                                12 => 'Décembre'
                            ];
                            for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $mois == $m ? 'selected' : '' ?>>
                                    <?= $mois_fr[$m] ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="w-32">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Année</label>
                        <select name="annee" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                            <?php for ($a = date('Y'); $a >= date('Y') - 2; $a--): ?>
                                <option value="<?= $a ?>" <?= $annee == $a ? 'selected' : '' ?>>
                                    <?= $a ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </form>
            </div>

            <!-- Liste des joueurs avec stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-users mr-2 text-green-600"></i>
                        Statistiques par joueur
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Poste</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Présent</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Absent</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Retard</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Taux</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($joueursStats as $joueur): ?>
                                <?php
                                $tauxJoueur = $joueur['total_seances'] > 0
                                    ? round(($joueur['nb_presents'] / $joueur['total_seances']) * 100, 1)
                                    : 0;
                                ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                <?= $joueur['numero_maillot'] ?? '?' ?>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">
                                                    <?= htmlspecialchars($joueur['nom']) ?> <?= htmlspecialchars($joueur['prenom']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <?= htmlspecialchars($joueur['poste'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-gray-700">
                                        <?= $joueur['total_seances'] ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check mr-1"></i>
                                            <?= $joueur['nb_presents'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-times mr-1"></i>
                                            <?= $joueur['nb_absents'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-700">
                                            <i class="fas fa-clock mr-1"></i>
                                            <?= $joueur['nb_retards'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold 
                                            <?= $tauxJoueur >= 80 ? 'bg-green-100 text-green-700' : ($tauxJoueur >= 60 ? 'bg-yellow-100 text-yellow-700' :
                                                'bg-red-100 text-red-700') ?>">
                                            <?= $tauxJoueur ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($joueursStats)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p>Aucune statistique disponible</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Liste des séances -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar mr-2 text-green-600"></i>
                        Historique des séances
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Lieu</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Présents</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Absents</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($seances as $seance): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                <?= date('d', strtotime($seance['date'])) ?>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">
                                                    <?php
                                                    $dateObj = new DateTime($seance['date']);
                                                    // Fallback simple si Intl n'est pas activé
                                                    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                                    $mois_fr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                                    $jour = $jours[$dateObj->format('w')];
                                                    $num_jour = $dateObj->format('d');
                                                    $mois = $mois_fr[(int)$dateObj->format('m')];
                                                    $annee = $dateObj->format('Y');
                                                    echo "$jour $num_jour $mois $annee";
                                                    ?>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <?= date('H:i', strtotime($seance['date'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                            <?= $seance['type'] === 'match' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                            <?= ucfirst($seance['type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                        <?= htmlspecialchars($seance['lieu']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check mr-1"></i>
                                            <?= $seance['nb_presents'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-times mr-1"></i>
                                            <?= $seance['nb_absents'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($roleUser !== 'joueur'): ?>
                                            <a href="/presence-marquer?seance_id=<?= $seance['id'] ?>"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                                <i class="fas fa-pencil-alt mr-2"></i>
                                                <?= $seance['total_presences'] > 0 ? 'Modifier' : 'Marquer' ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($seances)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                        <p>Aucune séance disponible</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="p-6 border-t border-gray-100">
                        <nav class="flex items-center justify-center gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&mois=<?= $mois ?>&annee=<?= $annee ?>"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>&mois=<?= $mois ?>&annee=<?= $annee ?>"
                                    class="px-4 py-2 rounded-lg transition 
                                   <?= $i == $page ? 'bg-green-600 text-white' : 'border border-gray-300 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&mois=<?= $mois ?>&annee=<?= $annee ?>"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>