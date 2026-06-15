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

$solde = $caisseModel->getSolde() ?? 0;
$totalCotisations = $caisseModel->getTotalByCategory('cotisation', 'entree') ?? 0;
$totalSanctions = $caisseModel->getTotalByCategory('sanction', 'entree') ?? 0;
$totalDons = $caisseModel->getTotalByCategory('don', 'entree') ?? 0;
$totalDepenses = $caisseModel->getTotalByCategory('depense', 'sortie') ?? 0;

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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 bg-slate-50">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6">
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900"><?= $pageTitle ?></h1>
                        <p class="text-sm text-slate-500">Suivi comptable de la caisse</p>
                    </div>
                    
                    <div class="bg-white border border-slate-200 px-6 py-3 rounded-lg flex items-center gap-4 shadow-sm">
                        <i class="fas fa-wallet text-emerald-500 text-xl"></i>
                        <div>
                            <span class="block text-xs uppercase text-slate-400 font-semibold tracking-wider">Solde Actuel</span>
                            <span class="text-xl font-bold text-slate-900"><?= number_format($solde, 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase font-bold text-slate-400">Total Cotisations</span>
                            <i class="fas fa-users text-blue-500"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-950"><?= number_format($totalCotisations, 0, ',', ' ') ?> FCFA</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase font-bold text-slate-400">Total Sanctions</span>
                            <i class="fas fa-gavel text-orange-500"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-950"><?= number_format($totalSanctions, 0, ',', ' ') ?> FCFA</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase font-bold text-slate-400">Total Dons</span>
                            <i class="fas fa-gift text-purple-500"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-950"><?= number_format($totalDons, 0, ',', ' ') ?> FCFA</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase font-bold text-slate-400">Total Dépenses</span>
                            <i class="fas fa-shopping-cart text-red-500"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-950"><?= number_format($totalDepenses, 0, ',', ' ') ?> FCFA</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                            <h2 class="text-base font-bold text-slate-900">Cotisations à percevoir</h2>
                            <span class="text-xs text-slate-400 font-semibold"><?= date('m/Y') ?></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase">
                                        <th class="pb-2">Joueur</th>
                                        <th class="pb-2 text-right">Montant</th>
                                        <th class="pb-2 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    <?php if (!empty($unpaid)): ?>
                                        <?php foreach ($unpaid as $item): ?>
                                            <tr>
                                                <td class="py-3">
                                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($item['nom']) ?></span>
                                                    <span class="text-slate-400 text-xs"><?= htmlspecialchars($item['prenom']) ?></span>
                                                </td>
                                                <td class="py-3 text-right font-medium text-slate-900">
                                                    <?= number_format($item['montant'], 0, ',', ' ') ?> FCFA
                                                </td>
                                                <td class="py-3 text-right">
                                                    <form action="/cotisation-payer" method="POST" class="inline-block">
                                                        <input type="hidden" name="cotisation_id" value="<?= $item['id'] ?>">
                                                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs px-3 py-1.5 rounded font-medium transition-colors">
                                                            Valider
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs italic">
                                                Aucune cotisation en attente
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPagesUnpaid > 1): ?>
                            <div class="flex items-center justify-center gap-1 mt-4">
                                <?php if ($unpaidPage > 1): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage - 1) ?>" class="border border-slate-200 hover:bg-slate-50 w-8 h-8 rounded flex items-center justify-center text-slate-600 text-xs">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesUnpaid; $i++): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $i) ?>" class="border text-xs w-8 h-8 rounded flex items-center justify-center font-medium <?= $i == $unpaidPage ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($unpaidPage < $totalPagesUnpaid): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage + 1) ?>" class="border border-slate-200 hover:bg-slate-50 w-8 h-8 rounded flex items-center justify-center text-slate-600 text-xs">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
                        <div class="mb-4 border-b border-slate-100 pb-3">
                            <h2 class="text-base font-bold text-slate-900">Derniers Mouvements</h2>
                        </div>

                        <div class="space-y-3">
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $t): ?>
                                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg text-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded flex items-center justify-center text-xs <?= $t['type'] === 'entree' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                                <i class="fas <?= $t['type'] === 'entree' ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900"><?= htmlspecialchars($t['libelle']) ?></p>
                                                <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                                    <span class="uppercase font-semibold text-slate-500 bg-slate-200/60 px-1.5 py-0.5 rounded text-[10px]">
                                                        <?= htmlspecialchars($t['categorie']) ?>
                                                    </span>
                                                    <span><?= date('d/m/Y', strtotime($t['date_transaction'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="font-bold <?= $t['type'] === 'entree' ? 'text-emerald-600' : 'text-red-600' ?>">
                                            <?= $t['type'] === 'entree' ? '+' : '-' ?> <?= number_format($t['montant'], 0, ',', ' ') ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-6 text-slate-400 text-xs italic bg-slate-50 border border-dashed border-slate-200 rounded-lg">
                                    Aucun mouvement enregistré
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($totalPagesTransactions > 1): ?>
                            <div class="flex items-center justify-center gap-1 mt-4">
                                <?php if ($transactionsPage > 1): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage - 1) ?>" class="border border-slate-200 hover:bg-slate-50 w-8 h-8 rounded flex items-center justify-center text-slate-600 text-xs">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesTransactions; $i++): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $i) ?>" class="border text-xs w-8 h-8 rounded flex items-center justify-center font-medium <?= $i == $transactionsPage ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($transactionsPage < $totalPagesTransactions): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage + 1) ?>" class="border border-slate-200 hover:bg-slate-50 w-8 h-8 rounded flex items-center justify-center text-slate-600 text-xs">
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