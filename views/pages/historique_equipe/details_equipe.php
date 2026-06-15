<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

$pageTitle = "Détails Équipe - " . htmlspecialchars($equipe['nom']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen font-sans">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 bg-slate-50 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 border border-slate-200 rounded-xl shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-users text-slate-500"></i>
                            <span>Joueurs • <?= htmlspecialchars($equipe['nom']) ?></span>
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">Effectif actuel et informations techniques</p>
                    </div>
                    <a href="/admin-team"
                        class="w-full sm:w-auto text-center px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-sm rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Retour à la liste</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($joueurs)): ?>
                        <?php foreach ($joueurs as $j): ?>
                            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                
                                <div class="flex items-center mb-6">
                                    <div class="h-14 w-14 bg-slate-900 text-white rounded-lg flex items-center justify-center text-xl font-bold shadow-sm">
                                        <?= strtoupper(substr($j['nom'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4 truncate">
                                        <h3 class="font-bold text-lg text-slate-900 truncate">
                                            <?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?>
                                        </h3>
                                        <p class="text-slate-500 text-xs truncate" title="<?= htmlspecialchars($j['email']) ?>">
                                            <?= htmlspecialchars($j['email']) ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-3 text-sm text-slate-600 border-t border-slate-100 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-400 font-medium">Poste</span>
                                        <span class="font-semibold text-slate-800 uppercase text-xs bg-slate-100 px-2.5 py-1 rounded">
                                            <?= htmlspecialchars($j['poste'] ?? 'N/A') ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-400 font-medium">Pied dominant</span>
                                        <span class="font-medium text-slate-800"><?= htmlspecialchars($j['pied_dominant'] ?? 'Non défini') ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-400 font-medium">Date de naissance</span>
                                        <span class="font-medium text-slate-800"><?= date('d/m/Y', strtotime($j['date_naissance'])) ?></span>
                                    </div>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full bg-white border border-slate-200 rounded-xl p-12 text-center shadow-sm">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4 text-slate-400">
                                <i class="fas fa-user-slash text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold text-slate-900">Aucun joueur assigné à cette équipe</p>
                            <p class="text-sm text-slate-500 mt-1">Les joueurs apparaitront ici dès qu'ils seront associés à ce groupe.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </main>
    </div>
</body>

</html>