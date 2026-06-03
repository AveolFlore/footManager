<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

$pageTitle = "Détails Equipe - " . htmlspecialchars($equipe['nom']);
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
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-3">
                        <i class="fas fa-users text-green-600"></i>
                        Joueurs de l'équipe : <?= htmlspecialchars($equipe['nom']) ?>
                    </h1>
                    <a href="/admin-team" class="flex items-center gap-3 bg-gradient-to-r from-slate-200 to-slate-300 text-slate-800 px-6 py-3 rounded-2xl font-bold hover:from-slate-300 hover:to-slate-400 transition-all shadow-md">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                </div>

                <!-- Grille des joueurs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($joueurs)): ?>
                        <?php foreach ($joueurs as $j): ?>
                            <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 hover:shadow-2xl transition-all duration-300">
                                <div class="flex items-center mb-8">
                                    <div class="h-20 w-20 bg-gradient-to-br from-green-100 to-emerald-100 text-green-700 rounded-3xl flex items-center justify-center text-3xl font-extrabold border border-green-200">
                                        <?= strtoupper(substr($j['nom'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="font-extrabold text-xl text-slate-800"><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?></h3>
                                        <p class="text-base text-slate-500 font-semibold"><?= htmlspecialchars($j['email']) ?></p>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between pb-3 border-b border-slate-100">
                                        <span class="text-slate-500 text-base font-semibold">Poste</span>
                                        <span class="font-extrabold text-slate-700 uppercase"><?= htmlspecialchars($j['poste'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex justify-between pb-3 border-b border-slate-100">
                                        <span class="text-slate-500 text-base font-semibold">Pied dominant</span>
                                        <span class="font-extrabold text-slate-700"><?= htmlspecialchars($j['pied_dominant'] ?? 'Non défini') ?></span>
                                    </div>
                                    <div class="flex justify-between pt-1">
                                        <span class="text-slate-500 text-base font-semibold">Né le</span>
                                        <span class="font-semibold text-slate-700"><?= date('d/m/Y', strtotime($j['date_naissance'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full bg-white p-16 rounded-3xl text-center border-2 border-dashed border-slate-200 shadow-md">
                            <i class="fas fa-user-slash text-slate-400 text-8xl mb-6"></i>
                            <p class="text-slate-400 italic text-xl font-semibold">Aucun joueur trouvé pour cette équipe.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
