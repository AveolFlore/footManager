<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Equipe - <?= htmlspecialchars($equipe['nom']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar (Verte comme demandé) -->
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header -->
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>

            <main class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Joueurs de l'équipe : <?= htmlspecialchars($equipe['nom']) ?></h1>
                    <a href="/admin-team" class="text-green-600 hover:text-green-800 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Retour à la liste
                    </a>
                </div>

                <!-- Grille des joueurs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($joueurs)): ?>
                        <?php foreach ($joueurs as $j): ?>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-6">
                                    <div class="h-14 w-14 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xl font-bold">
                                        <?= strtoupper(substr($j['nom'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-bold text-lg text-gray-900"><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?></h3>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($j['email']) ?></p>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b pb-2">
                                        <span class="text-gray-500 text-sm">Poste</span>
                                        <span class="font-semibold text-gray-700 uppercase"><?= htmlspecialchars($j['poste'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex justify-between border-b pb-2">
                                        <span class="text-gray-500 text-sm">Pied dominant</span>
                                        <span class="font-semibold text-gray-700"><?= htmlspecialchars($j['pied_dominant'] ?? 'Non défini') ?></span>
                                    </div>
                                    <div class="flex justify-between pt-1">
                                        <span class="text-gray-500 text-sm">Né le</span>
                                        <span class="text-gray-700"><?= date('d/m/Y', strtotime($j['date_naissance'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full bg-white p-10 rounded-xl text-center border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 italic">Aucun joueur trouvé pour cette équipe.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>