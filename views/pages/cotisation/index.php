<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/caisse/Caisse.php';
require_once __DIR__ . '/../../../models/cotisation/Cotisation.php';

use Config\Database;
use Models\Caisse\Caisse;
use Models\Cotisation\Cotisation;

$db = (new Database())->connect();
$caisseModel = new Caisse($db);
$cotisationModel = new Cotisation($db);

$solde = $caisseModel->getSolde();
$transactions = $caisseModel->getAllTransactions();
$unpaid = $cotisationModel->getUnpaidCurrentMonth(date('m'), date('Y'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Finance - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Gestion Financière</h1>
                <div class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold text-xl">
                    Solde : <?= number_format($solde, 0, ',', ' ') ?> FCFA
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Section: Cotisations Impayées -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-700 mb-6">Cotisations à percevoir (<?= date('m/Y') ?>)</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-sm uppercase">
                                    <th class="pb-3">Joueur</th>
                                    <th class="pb-3 text-right">Montant</th>
                                    <th class="pb-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($unpaid as $item): ?>
                                    <tr>
                                        <td class="py-4 font-medium"><?= $item['nom'] ?> <?= $item['prenom'] ?></td>
                                        <td class="py-4 text-right"><?= number_format($item['montant'], 0, ',', ' ') ?> FCFA</td>
                                        <td class="py-4 text-right">
                                            <form action="/cotisation-payer" method="POST">
                                                <input type="hidden" name="cotisation_id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                                                    Valider
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Historique Caisse -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-700 mb-6">Derniers Mouvements</h2>
                    <div class="space-y-4">
                        <?php foreach ($transactions as $t): ?>
                            <div class="flex items-center justify-between p-3 rounded-lg <?= $t['type'] === 'entree' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
                                <div class="flex items-center space-x-3">
                                    <i class="fas <?= $t['type'] === 'entree' ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
                                    <div>
                                        <p class="font-bold text-sm"><?= $t['libelle'] ?></p>
                                        <p class="text-xs opacity-75"><?= date('d/m/Y', strtotime($t['date_transaction'])) ?></p>
                                    </div>
                                </div>
                                <p class="font-bold"><?= $t['type'] === 'entree' ? '+' : '-' ?> <?= number_format($t['montant'], 0, ',', ' ') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
