<?php
$pageTitle = "Gestion des Sanctions";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sanctions - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-gavel mr-2 text-green-600"></i>
                Gestion des Sanctions
            </h1>

            <!-- Alertes -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 rounded-lg flex items-center gap-3 
                    <?php echo in_array($_GET['msg'], ['sanction_payee']) ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200'; ?>">
                    <span class="text-xl">
                        <?php echo $_GET['msg'] === 'sanction_payee' ? '✅' : '⚠️'; ?>
                    </span>
                    <p class="text-sm font-medium">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-clock mr-2 text-yellow-600"></i>
                        Sanctions en attente de paiement
                    </h2>
                </div>

                <?php if (empty($sanctions)): ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-lg">Aucune sanction en attente pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Joueur</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Motif</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Montant (FCFA)</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($sanctions as $sanction): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                                    <?php echo strtoupper(substr($sanction['nom'], 0, 1) . substr($sanction['prenom'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($sanction['nom']); ?> <?php echo htmlspecialchars($sanction['prenom']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <?php echo htmlspecialchars($sanction['titre']); ?>
                                            </span>
                                            <p class="mt-1 text-sm text-gray-600"><?php echo htmlspecialchars($sanction['motif']); ?></p>
                                        </td>
                                        <td class="px-6 py-4 text-lg font-bold text-red-600">
                                            <?php echo number_format($sanction['montant'], 0, ',', ' '); ?> FCFA
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo date('d/m/Y', strtotime($sanction['date_sanction'])); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <form method="POST" action="/sanction-marquerpayee" class="inline">
                                                <input type="hidden" name="sanction_id" value="<?php echo $sanction['id']; ?>">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition duration-200">
                                                    <i class="fas fa-check-circle"></i>
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
        </main>
    </div>
</body>

</html>