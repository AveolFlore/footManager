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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap');

        :root {
            --neon-blue: #00f3ff;
            --neon-cyan: #00d4ff;
            --neon-purple: #a855f7;
            --neon-pink: #ec4899;
            --neon-orange: #f97316;
            --neon-green: #22c55e;
            --neon-red: #ef4444;
            --dark-bg: #020617;
            --card-bg: rgba(15, 23, 42, 0.8);
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--dark-bg);
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(0, 243, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.03) 0%, transparent 50%);
        }

        .font-cyber {
            font-family: 'Orbitron', sans-serif;
        }

        /* Scanline effect */
        .scanlines::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.1),
                rgba(0, 0, 0, 0.1) 1px,
                transparent 1px,
                transparent 2px
            );
            pointer-events: none;
            z-index: 9999;
            opacity: 0.3;
        }

        /* Holographic card */
        .holo-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 243, 255, 0.1);
            box-shadow: 
                0 0 20px rgba(0, 243, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .holo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 243, 255, 0.05), transparent);
            transition: left 0.7s;
        }

        .holo-card:hover::before {
            left: 100%;
        }

        .holo-card:hover {
            border-color: rgba(0, 243, 255, 0.3);
            box-shadow: 
                0 0 30px rgba(0, 243, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Neon glow button */
        .btn-glow {
            position: relative;
            background: linear-gradient(135deg, rgba(0, 243, 255, 0.1), rgba(0, 212, 255, 0.05));
            border: 1px solid rgba(0, 243, 255, 0.3);
            color: var(--neon-blue);
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s;
            overflow: hidden;
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 243, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-glow:hover::before {
            left: 100%;
        }

        .btn-glow:hover {
            background: linear-gradient(135deg, rgba(0, 243, 255, 0.2), rgba(0, 212, 255, 0.1));
            border-color: var(--neon-blue);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.3), 0 0 40px rgba(0, 243, 255, 0.1);
            text-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
        }

        /* Neon text */
        .neon-text {
            color: var(--neon-blue);
            text-shadow: 0 0 10px rgba(0, 243, 255, 0.5), 0 0 20px rgba(0, 243, 255, 0.3);
        }

        .neon-text-green {
            color: var(--neon-green);
            text-shadow: 0 0 10px rgba(34, 197, 94, 0.5), 0 0 20px rgba(34, 197, 94, 0.3);
        }

        .neon-text-orange {
            color: var(--neon-orange);
            text-shadow: 0 0 10px rgba(249, 115, 22, 0.5), 0 0 20px rgba(249, 115, 22, 0.3);
        }

        .neon-text-purple {
            color: var(--neon-purple);
            text-shadow: 0 0 10px rgba(168, 85, 247, 0.5), 0 0 20px rgba(168, 85, 247, 0.3);
        }

        .neon-text-red {
            color: var(--neon-red);
            text-shadow: 0 0 10px rgba(239, 68, 68, 0.5), 0 0 20px rgba(239, 68, 68, 0.3);
        }

        /* Cyber table */
        .cyber-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .cyber-table th {
            background: rgba(0, 243, 255, 0.05);
            border-bottom: 2px solid rgba(0, 243, 255, 0.2);
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.75rem;
        }

        .cyber-table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .cyber-table tr:hover td {
            background: rgba(0, 243, 255, 0.02);
        }

        /* Transaction item */
        .tx-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .tx-item::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
        }

        .tx-item.entree::after {
            background: var(--neon-green);
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }

        .tx-item.sortie::after {
            background: var(--neon-red);
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }

        .tx-item:hover {
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        /* Category badge */
        .badge-cyber {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.65rem;
            letter-spacing: 1px;
        }

        /* Pagination */
        .page-btn {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(0, 243, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
        }

        .page-btn:hover {
            border-color: rgba(0, 243, 255, 0.3);
            color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.1);
        }

        .page-btn.active {
            background: rgba(0, 243, 255, 0.1);
            border-color: var(--neon-blue);
            color: var(--neon-blue);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 243, 255, 0.2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 243, 255, 0.4);
        }

        /* Corner accents */
        .corner-accent {
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid var(--neon-blue);
            opacity: 0.3;
        }

        .corner-tl { top: -1px; left: -1px; border-right: 0; border-bottom: 0; }
        .corner-tr { top: -1px; right: -1px; border-left: 0; border-bottom: 0; }
        .corner-bl { bottom: -1px; left: -1px; border-right: 0; border-top: 0; }
        .corner-br { bottom: -1px; right: -1px; border-left: 0; border-top: 0; }
    </style>
</head>

<body class="scanlines text-slate-300">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 relative">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10">
                <!-- Header -->
                <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <i class="fas fa-chart-line text-4xl neon-text"></i>
                            <div class="absolute -inset-2 bg-cyan-500/20 blur-xl rounded-full"></div>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-4xl font-cyber font-black text-white tracking-wider uppercase">
                                Gestion <span class="neon-text">Financière</span>
                            </h1>
                            <p class="text-slate-500 text-sm tracking-widest uppercase mt-1">Système de Monitoring Économique</p>
                        </div>
                    </div>
                    
                    <div class="holo-card px-10 py-4 rounded-2xl font-cyber font-black text-2xl md:text-3xl flex items-center gap-4">
                        <div class="relative">
                            <i class="fas fa-wallet neon-text-green text-2xl"></i>
                            <div class="absolute -inset-2 bg-green-500/20 blur-xl rounded-full"></div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-slate-500 tracking-widest uppercase">Solde Actuel</span>
                            <span class="neon-text-green tracking-wider"><?= number_format($solde, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div class="corner-accent corner-tl"></div>
                        <div class="corner-accent corner-tr"></div>
                        <div class="corner-accent corner-bl"></div>
                        <div class="corner-accent corner-br"></div>
                    </div>
                </header>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Cotisations -->
                    <div class="holo-card rounded-2xl p-6 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center relative">
                                <i class="fas fa-users text-blue-400 text-2xl relative z-10"></i>
                                <div class="absolute inset-0 bg-blue-500/20 blur-lg rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="text-xs font-cyber text-slate-600 tracking-widest">ENTRÉE</div>
                        </div>
                        <p class="text-slate-500 text-xs font-cyber uppercase tracking-widest mb-2">Total Cotisations</p>
                        <p class="text-2xl font-cyber font-black neon-text tracking-wider"><?= number_format($totalCotisations, 0, ',', ' ') ?> <span class="text-sm text-slate-600">FCFA</span></p>
                        <div class="mt-3 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-400 w-3/4 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                        </div>
                    </div>

                    <!-- Sanctions -->
                    <div class="holo-card rounded-2xl p-6 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center relative">
                                <i class="fas fa-gavel text-orange-400 text-2xl relative z-10"></i>
                                <div class="absolute inset-0 bg-orange-500/20 blur-lg rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="text-xs font-cyber text-slate-600 tracking-widest">ENTRÉE</div>
                        </div>
                        <p class="text-slate-500 text-xs font-cyber uppercase tracking-widest mb-2">Total Sanctions</p>
                        <p class="text-2xl font-cyber font-black neon-text-orange tracking-wider"><?= number_format($totalSanctions, 0, ',', ' ') ?> <span class="text-sm text-slate-600">FCFA</span></p>
                        <div class="mt-3 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-yellow-400 w-1/2 rounded-full shadow-[0_0_10px_rgba(249,115,22,0.5)]"></div>
                        </div>
                    </div>

                    <!-- Dons -->
                    <div class="holo-card rounded-2xl p-6 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center relative">
                                <i class="fas fa-gift text-purple-400 text-2xl relative z-10"></i>
                                <div class="absolute inset-0 bg-purple-500/20 blur-lg rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="text-xs font-cyber text-slate-600 tracking-widest">ENTRÉE</div>
                        </div>
                        <p class="text-slate-500 text-xs font-cyber uppercase tracking-widest mb-2">Total Dons</p>
                        <p class="text-2xl font-cyber font-black neon-text-purple tracking-wider"><?= number_format($totalDons, 0, ',', ' ') ?> <span class="text-sm text-slate-600">FCFA</span></p>
                        <div class="mt-3 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-pink-400 w-2/3 rounded-full shadow-[0_0_10px_rgba(168,85,247,0.5)]"></div>
                        </div>
                    </div>

                    <!-- Dépenses -->
                    <div class="holo-card rounded-2xl p-6 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center relative">
                                <i class="fas fa-shopping-cart text-red-400 text-2xl relative z-10"></i>
                                <div class="absolute inset-0 bg-red-500/20 blur-lg rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="text-xs font-cyber text-slate-600 tracking-widest">SORTIE</div>
                        </div>
                        <p class="text-slate-500 text-xs font-cyber uppercase tracking-widest mb-2">Total Dépenses</p>
                        <p class="text-2xl font-cyber font-black neon-text-red tracking-wider"><?= number_format($totalDepenses, 0, ',', ' ') ?> <span class="text-sm text-slate-600">FCFA</span></p>
                        <div class="mt-3 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-red-500 to-pink-400 w-1/3 rounded-full shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                    <!-- Section: Cotisations Impayées -->
                    <div class="holo-card rounded-2xl p-8 relative">
                        <div class="corner-accent corner-tl"></div>
                        <div class="corner-accent corner-tr"></div>
                        
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-blue-400"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-cyber font-bold text-white tracking-wider uppercase">
                                    Cotisations à percevoir
                                </h2>
                                <p class="text-xs text-slate-500 font-cyber tracking-widest"><?= date('m/Y') ?></p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left cyber-table">
                                <thead>
                                    <tr>
                                        <th class="pb-4 pl-4 font-bold">Joueur</th>
                                        <th class="pb-4 text-right font-bold">Montant</th>
                                        <th class="pb-4 text-right font-bold pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($unpaid as $item): ?>
                                        <tr class="transition-all duration-300">
                                            <td class="py-4 pl-4 font-semibold text-slate-300">
                                                <span class="text-cyan-400 font-cyber"><?= htmlspecialchars($item['nom']) ?></span>
                                                <span class="text-slate-500"><?= htmlspecialchars($item['prenom']) ?></span>
                                            </td>
                                            <td class="py-4 text-right text-lg font-cyber font-black text-white tracking-wider">
                                                <?= number_format($item['montant'], 0, ',', ' ') ?> <span class="text-xs text-slate-600">FCFA</span>
                                            </td>
                                            <td class="py-4 text-right pr-4">
                                                <form action="/cotisation-payer" method="POST">
                                                    <input type="hidden" name="cotisation_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn-glow px-6 py-2 rounded-lg text-xs font-cyber font-bold">
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
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage - 1) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesUnpaid; $i++): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $i) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg font-cyber font-bold text-sm <?= $i == $unpaidPage ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($unpaidPage < $totalPagesUnpaid): ?>
                                    <a href="<?= buildPaginationUrl('unpaid_page', $unpaidPage + 1) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section: Historique Caisse -->
                    <div class="holo-card rounded-2xl p-8 relative">
                        <div class="corner-accent corner-tl"></div>
                        <div class="corner-accent corner-tr"></div>
                        
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                                <i class="fas fa-history text-green-400"></i>
                            </div>
                            <h2 class="text-xl font-cyber font-bold text-white tracking-wider uppercase">
                                Derniers Mouvements
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <?php foreach ($transactions as $t): ?>
                                <div class="tx-item <?= $t['type'] === 'entree' ? 'entree' : 'sortie' ?> flex items-center justify-between p-5 rounded-xl transition-all duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-lg bg-slate-900/50 border border-slate-700 flex items-center justify-center">
                                            <i class="fas text-xl <?= $t['type'] === 'entree' ? 'fa-arrow-down text-green-400' : 'fa-arrow-up text-red-400' ?>"></i>
                                        </div>
                                        <div>
                                            <p class="font-cyber font-bold text-white tracking-wide"><?= htmlspecialchars($t['libelle']) ?></p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="badge-cyber inline-block px-3 py-1 rounded text-xs font-bold uppercase
                                                <?= $t['categorie'] === 'cotisation' ? 'text-blue-400 border-blue-500/30' : ($t['categorie'] === 'sanction' ? 'text-orange-400 border-orange-500/30' : ($t['categorie'] === 'don' ? 'text-purple-400 border-purple-500/30' : ($t['categorie'] === 'depense' ? 'text-red-400 border-red-500/30' : 'text-slate-400 border-slate-500/30'))) ?>">
                                                    <?= ucfirst(htmlspecialchars($t['categorie'])) ?>
                                                </span>
                                                <p class="text-xs text-slate-500 font-cyber tracking-wider">
                                                    <i class="far fa-calendar mr-1"></i>
                                                    <?= date('d/m/Y', strtotime($t['date_transaction'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="font-cyber font-black text-xl tracking-wider <?= $t['type'] === 'entree' ? 'neon-text-green' : 'neon-text-red' ?>">
                                        <?= $t['type'] === 'entree' ? '+' : '-' ?> <?= number_format($t['montant'], 0, ',', ' ') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination for Transactions -->
                        <?php if ($totalPagesTransactions > 1): ?>
                            <div class="flex items-center justify-center gap-3 mt-8">
                                <?php if ($transactionsPage > 1): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage - 1) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPagesTransactions; $i++): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $i) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg font-cyber font-bold text-sm <?= $i == $transactionsPage ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($transactionsPage < $totalPagesTransactions): ?>
                                    <a href="<?= buildPaginationUrl('transactions_page', $transactionsPage + 1) ?>" class="page-btn flex items-center justify-center w-10 h-10 rounded-lg">
                                        <i class="fas fa-chevron-right text-xs"></i>
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