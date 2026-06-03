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
$totalCotisations = $caisseModel->getTotalByCategory('cotisation', 'entree');
$totalSanctions = $caisseModel->getTotalByCategory('sanction', 'entree');
$totalDons = $caisseModel->getTotalByCategory('don', 'entree');
$totalDepenses = $caisseModel->getTotalByCategory('depense', 'sortie');

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

$pageTitle = "Gestion Financière";
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

<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-6 md:p-8 lg:p-10">
                <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-4">
                        <i class="fas fa-chart-line text-green-600 text-4xl"></i>
                        Gestion Financière
                    </h1>
                    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-10 py-4 rounded-3xl font-extrabold text-3xl shadow-xl shadow-green-200">
                        <i class="fas fa-wallet mr-3"></i>
                        Solde : <?= number_format($solde, 0, ',', ' ') ?> FCFA
                    </div>
                </header>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Cotisations -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Cotisations</p>
                            <p class="text-3xl font-extrabold text-blue-600"><?= number_format($totalCotisations, 0, ',', ' ') ?> FCFA</p>
                        </div>
                    </div>

                    <!-- Sanctions -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
                                    <i class="fas fa-gavel text-white text-2xl"></i>
                                </div>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Sanctions</p>
                            <p class="text-3xl font-extrabold text-orange-600"><?= number_format($totalSanctions, 0, ',', ' ') ?> FCFA</p>
                        </div>
                    </div>

                    <!-- Dons -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-700 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200">
                                    <i class="fas fa-gift text-white text-2xl"></i>
                                </div>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Dons</p>
                            <p class="text-3xl font-extrabold text-purple-600"><?= number_format($totalDons, 0, ',', ' ') ?> FCFA</p>
                        </div>
                    </div>

                    <!-- Dépenses -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-100 to-pink-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-700 rounded-2xl flex items-center justify-center shadow-lg shadow-red-200">
                                    <i class="fas fa-shopping-cart text-white text-2xl"></i>
                                </div>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Dépenses</p>
                            <p class="text-3xl font-extrabold text-red-600"><?= number_format($totalDepenses, 0, ',', ' ') ?> FCFA</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                    <!-- Section: Cotisations Impayées -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                        <h2 class="text-2xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                            <i class="fas fa-money-bill-wave text-blue-600"></i>
                            Cotisations à percevoir (<?= date('m/Y') ?>)
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-slate-400 text-sm uppercase tracking-wider border-b-2 border-slate-100">
                                        <th class="pb-4 font-bold">Joueur</th>
                                        <th class="pb-4 text-right font-bold">Montant</th>
                                        <th class="pb-4 text-right font-bold">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($unpaid as $item): ?>
                                        <tr class="hover:bg-slate-50 transition-all duration-200">
                                            <td class="py-5 font-semibold text-slate-800"><?= htmlspecialchars($item['nom']) ?> <?= htmlspecialchars($item['prenom']) ?></td>
                                            <td class="py-5 text-right text-xl font-extrabold text-slate-700"><?= number_format($item['montant'], 0, ',', ' ') ?> FCFA</td>
                                            <td class="py-5 text-right">
                                                <form action="/cotisation-payer" method="POST">
                                                    <input type="hidden" name="cotisation_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-3 rounded-2xl text-sm font-bold transition-all shadow-lg shadow-blue-200">
                                                        <i class="fas fa-check mr-2"></i> Valider
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
                            <div class="flex items-center justify-center gap-3 mt-8">
                                <?php if ($unpaidPage > 1): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage - 1) ?>" class="flex items-center justify-center w-12 h-12 border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesUnpaid; $i++): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $i) ?>" class="flex items-center justify-center w-12 h-12 rounded-2xl transition-all font-bold <?= $i == $unpaidPage ? 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-200' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($unpaidPage < $totalPagesUnpaid): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage + 1) ?>" class="flex items-center justify-center w-12 h-12 border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section: Historique Caisse -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                        <h2 class="text-2xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                            <i class="fas fa-history text-green-600"></i>
                            Derniers Mouvements
                        </h2>
                        <div class="space-y-5">
                            <?php foreach ($transactions as $t): ?>
                                <div class="flex items-center justify-between p-5 rounded-2xl transition-all duration-200 hover:shadow-md <?= $t['type'] === 'entree' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200' : 'bg-gradient-to-r from-red-50 to-pink-50 text-red-700 border border-red-200' ?>">
                                    <div class="flex items-center space-x-4">
                                        <i class="fas text-3xl <?= $t['type'] === 'entree' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600' ?>"></i>
                                        <div>
                                            <p class="font-bold text-lg"><?= htmlspecialchars($t['libelle']) ?></p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                                                <?= $t['categorie'] === 'cotisation' ? 'bg-blue-200 text-blue-800' : ($t['categorie'] === 'sanction' ? 'bg-orange-200 text-orange-800' : ($t['categorie'] === 'don' ? 'bg-purple-200 text-purple-800' : ($t['categorie'] === 'depense' ? 'bg-red-200 text-red-800' : 'bg-slate-200 text-slate-800'))) ?>">
                                                    <?= ucfirst(htmlspecialchars($t['categorie'])) ?>
                                                </span>
                                                <p class="text-xs opacity-75 font-medium">
                                                    <i class="far fa-calendar mr-1"></i>
                                                    <?= date('d/m/Y', strtotime($t['date_transaction'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="font-extrabold text-2xl"><?= $t['type'] === 'entree' ? '+' : '-' ?> <?= number_format($t['montant'], 0, ',', ' ') ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination for Transactions -->
                        <?php if ($totalPagesTransactions > 1): ?>
                            <div class="flex items-center justify-center gap-3 mt-8">
                                <?php if ($transactionsPage > 1): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage - 1) ?>" class="flex items-center justify-center w-12 h-12 border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesTransactions; $i++): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $i) ?>" class="flex items-center justify-center w-12 h-12 rounded-2xl transition-all font-bold <?= $i == $transactionsPage ? 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-200' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($transactionsPage < $totalPagesTransactions): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage + 1) ?>" class="flex items-center justify-center w-12 h-12 border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>

</html>