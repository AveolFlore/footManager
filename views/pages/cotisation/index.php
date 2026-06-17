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

function buildPaginationUrl($paramName, $page) {
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-finance {
            background-image: url('/assets/images/Finance.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="bg-finance min-h-screen">
    <?php include __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main>
            <div class="p-6 md:p-8">
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-white drop-shadow-lg"><?= $pageTitle ?></h1>
                        <p class="text-white font-medium drop-shadow-md">Suivi comptable de la caisse</p>
                    </div>
                    
                    <div class="bg-white px-8 py-4 rounded-2xl flex items-center gap-4 shadow-xl border border-slate-100">
                        <i class="fas fa-wallet text-emerald-600 text-2xl"></i>
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest font-bold text-slate-400">Solde Actuel</span>
                            <span class="text-2xl font-black text-slate-900"><?= number_format($solde, 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <?php 
                    $stats = [
                        ['Cotisations', $totalCotisations, 'fa-users', 'text-blue-600'],
                        ['Sanctions', $totalSanctions, 'fa-gavel', 'text-orange-600'],
                        ['Dons', $totalDons, 'fa-gift', 'text-purple-600'],
                        ['Dépenses', $totalDepenses, 'fa-shopping-cart', 'text-red-600']
                    ];
                    foreach ($stats as $s): ?>
                    <div class="bg-white p-5 rounded-2xl shadow-lg border border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] uppercase font-black text-slate-400"><?= $s[0] ?></span>
                            <i class="fas <?= $s[2] ?> <?= $s[3] ?>"></i>
                        </div>
                        <p class="text-lg font-black text-slate-900"><?= number_format($s[1], 0, ',', ' ') ?> <span class="text-[10px] text-slate-400">FCFA</span></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-3xl shadow-lg border border-slate-100">
                        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-slate-400"></i> Cotisations à percevoir
                        </h2>
                        <div class="overflow-hidden">
                            <table class="w-full">
                                <tbody class="divide-y divide-slate-100">
                                    <?php if (!empty($unpaid)): foreach ($unpaid as $item): ?>
                                    <tr class="group">
                                        <td class="py-4">
                                            <p class="font-bold text-slate-800"><?= htmlspecialchars($item['nom'] . ' ' . $item['prenom']) ?></p>
                                        </td>
                                        <td class="py-4 text-right font-black text-emerald-600"><?= number_format($item['montant'], 0, ',', ' ') ?></td>
                                        <td class="py-4 text-right">
                                            <form action="/cotisation-payer" method="POST">
                                                <input type="hidden" name="cotisation_id" value="<?= $item['id'] ?>">
                                                <button class="bg-slate-900 hover:bg-emerald-600 text-white text-[10px] px-3 py-1.5 rounded-lg font-bold transition-all">Valider</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="py-6 text-center text-slate-400 text-xs italic">Aucune cotisation en attente</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-lg border border-slate-100">
                        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-history text-slate-400"></i> Derniers Mouvements
                        </h2>
                        <div class="space-y-3">
                            <?php if (!empty($transactions)): foreach ($transactions as $t): ?>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] <?= $t['type'] === 'entree' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' ?>">
                                        <i class="fas <?= $t['type'] === 'entree' ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900"><?= htmlspecialchars($t['libelle']) ?></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase"><?= htmlspecialchars($t['categorie']) ?></p>
                                    </div>
                                </div>
                                <span class="text-xs font-black <?= $t['type'] === 'entree' ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= $t['type'] === 'entree' ? '+' : '-' ?> <?= number_format($t['montant'], 0, ',', ' ') ?>
                                </span>
                            </div>
                            <?php endforeach; else: ?>
                            <p class="text-center py-6 text-slate-400 text-xs italic">Aucun mouvement</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>