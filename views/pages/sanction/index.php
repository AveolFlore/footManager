<?php
$pageTitle = "Gestion des Sanctions";
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

        <main class="flex-1">
            <div class="p-6 md:p-8 lg:p-10">
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-8 flex items-center gap-4">
                    <i class="fas fa-gavel text-orange-600 text-4xl"></i>
                    Gestion des Sanctions
                </h1>

                <!-- Alertes -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 p-5 rounded-2xl flex items-center gap-4 shadow-md
                        <?php echo in_array($_GET['msg'], ['sanction_payee']) ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200' : 'bg-gradient-to-r from-yellow-50 to-orange-50 text-yellow-700 border border-yellow-200'; ?>">
                        <span class="text-3xl">
                            <?php echo $_GET['msg'] === 'sanction_payee' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>'; ?>
                        </span>
                        <p class="text-lg font-semibold">
                            <?php
                            $messages = [
                                'sanction_payee' => "Sanction marquée comme payée !",
                                'sanction_introuvable' => "Sanction introuvable.",
                                'deja_payee' => "Sanction déjà payée.",
                                'requete_invalide' => "Requête invalide."
                            ];
                            echo $messages[$_GET['msg']] ?? '';
                            ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Liste des sanctions en attente -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
                        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                            Sanctions en attente de paiement
                        </h2>
                    </div>

                    <?php if (empty($sanctions)): ?>
                        <div class="text-center py-16 text-slate-500">
                            <i class="fas fa-inbox text-6xl mb-4 text-slate-300"></i>
                            <p class="text-xl font-medium">Aucune sanction en attente pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50 border-b-2 border-slate-100">
                                    <tr>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Joueur</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Motif</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Montant (FCFA)</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                                        <th class="text-left px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($sanctions as $sanction): ?>
                                        <tr class="hover:bg-slate-50 transition-all duration-200">
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-orange-200">
                                                        <?php echo strtoupper(substr($sanction['nom'], 0, 1) . substr($sanction['prenom'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-slate-800 text-lg"><?php echo htmlspecialchars($sanction['nom']); ?> <?php echo htmlspecialchars($sanction['prenom']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm font-bold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 mb-2">
                                                    <?php echo htmlspecialchars($sanction['titre']); ?>
                                                </span>
                                                <p class="mt-1 text-sm text-slate-600"><?php echo htmlspecialchars($sanction['motif']); ?></p>
                                            </td>
                                            <td class="px-8 py-6 text-2xl font-extrabold text-orange-600">
                                                <?php echo number_format($sanction['montant'], 0, ',', ' '); ?> FCFA
                                            </td>
                                            <td class="px-8 py-6 text-sm text-slate-600 font-semibold">
                                                <?php echo date('d/m/Y', strtotime($sanction['date_sanction'])); ?>
                                            </td>
                                            <td class="px-8 py-6">
                                                <form method="POST" action="/sanction-marquerpayee" class="inline">
                                                    <input type="hidden" name="sanction_id" value="<?php echo $sanction['id']; ?>">
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-bold rounded-2xl transition-all duration-200 shadow-lg shadow-green-200">
                                                        <i class="fas fa-check-circle text-xl"></i>
                                                        Marquer comme payée
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>