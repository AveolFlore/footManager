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

// Pagination params
$unpaidPage = isset($_GET['unpaid_page']) ? max(1, (int)$_GET['unpaid_page']) : 1;
$transactionsPage = isset($_GET['transactions_page']) ? max(1, (int)$_GET['transactions_page']) : 1;
$perPage = 10;

$solde = $caisseModel->getSolde();
$transactions = $caisseModel->getAllTransactions($transactionsPage, $perPage);
$totalTransactions = $caisseModel->getTotalTransactions();
$totalPagesTransactions = ceil($totalTransactions / $perPage);

$currentMonth = date('m');
$currentYear = date('Y');
$unpaid = $cotisationModel->getUnpaidCurrentMonth($currentMonth, $currentYear, $unpaidPage, $perPage);
$totalUnpaid = $cotisationModel->getTotalUnpaidCurrentMonth($currentMonth, $currentYear);
$totalPagesUnpaid = ceil($totalUnpaid / $perPage);

// Function to build pagination URL with preserving other params
function buildPaginationUrl($paramName, $page)
{
    $params = $_GET;
    $params[$paramName] = $page;
    return '?' . http_build_query($params);
}
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

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

                    <!-- Pagination for Unpaid -->
                    <?php if ($totalPagesUnpaid > 1): ?>
                        <div class="flex items-center justify-center gap-2 mt-6">
                            <?php if ($unpaidPage > 1): ?>
                                <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage - 1) ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPagesUnpaid; $i++): ?>
                                <a href="<?= buildPaginationUrl('unpaid_page', $i) ?>" class="px-3 py-1 border rounded transition <?= $i == $unpaidPage ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($unpaidPage < $totalPagesUnpaid): ?>
                                <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage + 1) ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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

                    <!-- Pagination for Transactions -->
                    <?php if ($totalPagesTransactions > 1): ?>
                        <div class="flex items-center justify-center gap-2 mt-6">
                            <?php if ($transactionsPage > 1): ?>
                                <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage - 1) ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPagesTransactions; $i++): ?>
                                <a href="<?= buildPaginationUrl('transactions_page', $i) ?>" class="px-3 py-1 border rounded transition <?= $i == $transactionsPage ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($transactionsPage < $totalPagesTransactions): ?>
                                <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage + 1) ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

</body>

</html>