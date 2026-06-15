<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/performance/Performance.php';

use Config\Database;
use Models\Performance\Performance;

requireLogin();

$db = (new Database())->connect();
$performanceModel = new Performance($db);

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$ranking = $performanceModel->getRanking($month, $year);

$months = [
    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
    '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
    '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
];

$pageTitle = "Classement des Joueurs";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <style>
        /* Base standard */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        main {
            flex: 1;
            padding: 20px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        h1 {
            margin: 0;
            font-size: 26px;
            color: #222;
        }
        .subtitle {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Formulaire de filtre */
        .filter-form {
            display: flex;
            gap: 10px;
            background: #fff;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .select-wrapper {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0 10px;
        }
        .select-wrapper select {
            border: none;
            background: transparent;
            padding: 10px;
            font-weight: bold;
            font-size: 13px;
            outline: none;
            cursor: pointer;
        }
        .btn-filter {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
        }
        .btn-filter:hover {
            background-color: #218838;
        }

        /* Conteneur Tableau */
        .panel {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
            color: #495057;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tr:hover { background-color: #f1f3f5; }

        /* Rangs (Médailles / Chiffres) */
        .rank-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
        }
        .rank-1 { background-color: #ffe8a1; color: #856404; border: 1px solid #ffd452; }
        .rank-2 { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .rank-3 { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .rank-default { font-weight: bold; color: #6c757d; padding-left: 10px; }

        /* Cellule Joueur */
        .player-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .avatar-wrapper {
            position: relative;
            width: 45px;
            height: 45px;
        }
        .player-avatar {
            width: 100%;
            height: 100%;
            border-radius: 6px;
            object-cover: cover;
            border: 1px solid #dee2e6;
            background-color: #e9ecef;
        }
        .crown-icon {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ffc107;
            font-size: 9px;
            padding: 2px;
            border-radius: 50%;
            border: 1px solid #fff;
        }
        .player-name {
            margin: 0;
            font-weight: bold;
            color: #212529;
        }
        .player-status {
            margin: 2px 0 0 0;
            font-size: 11px;
            color: #6c757d;
        }

        /* Points & Ratio */
        .points-badge {
            background-color: #e2f0fe;
            color: #004085;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
            border: 1px solid #b8daff;
        }
        .ratio-container {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 150px;
        }
        .progress-bar-bg {
            flex: 1;
            background-color: #e9ecef;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .progress-bar-fill {
            background-color: #007bff;
            height: 100%;
            border-radius: 10px;
        }
        .ratio-text {
            font-size: 12px;
            font-weight: bold;
            color: #495057;
            width: 35px;
            text-align: right;
        }

        /* Liste Vide */
        .empty-row {
            text-align: center;
            padding: 50px !important;
            color: #6c757d;
            font-style: italic;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .header-section { flex-direction: column; align-items: flex-start; gap: 15px; }
            .filter-form { width: 100%; flex-direction: column; }
            .select-wrapper { width: 100%; }
            .select-wrapper select { width: 100%; }
        }
    </style>
</head>
<body>

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div style="display: flex; width: 100%;">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main>
            <div class="header-section">
                <div>
                    <h1>Classement des Joueurs</h1>
                    <p class="subtitle">Critères d'évaluation : But (3 pts) | Passe décisive (2 pts)</p>
                </div>
                
                <form action="/page-classement" method="GET" class="filter-form">
                    <div class="select-wrapper">
                        <select name="month">
                            <?php foreach ($months as $mNum => $mName): ?>
                                <option value="<?= $mNum ?>" <?= $month == $mNum ? 'selected' : '' ?>><?= $mName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="select-wrapper">
                        <select name="year">
                            <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">Filtrer</button>
                </form>
            </div>

            <div class="panel">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 70px;">Rang</th>
                                <th>Joueur</th>
                                <th style="text-align: center;">Buts</th>
                                <th style="text-align: center;">Passes</th>
                                <th style="text-align: center;">Points</th>
                                <th>Ratio Global</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ranking)): ?>
                                <tr>
                                    <td colspan="6" class="empty-row">
                                        Aucune donnée de performance enregistrée pour cette phase.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ranking as $index => $row): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index === 0): ?>
                                                <span class="rank-badge rank-1">1</span>
                                            <?php elseif ($index === 1): ?>
                                                <span class="rank-badge rank-2">2</span>
                                            <?php elseif ($index === 2): ?>
                                                <span class="rank-badge rank-3">3</span>
                                            <?php else: ?>
                                                <span class="rank-default">#<?= $index + 1 ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="player-cell">
                                                <div class="avatar-wrapper">
                                                    <img src="/<?= $row['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="player-avatar" alt="Avatar">
                                                    <?php if ($index === 0): ?>
                                                        <span class="crown-icon">👑</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="player-name"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></p>
                                                    <p class="player-status"><?= $month == date('m') ? 'Évaluation active' : 'Phase archivée' ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <td style="text-align: center; font-weight: bold;"><?= $row['total_buts'] ?></td>
                                        <td style="text-align: center; color: #495057;"><?= $row['total_passes'] ?></td>
                                        
                                        <td style="text-align: center;">
                                            <span class="points-badge">
                                                <?= $row['score'] ?> pts
                                            </span>
                                        </td>

                                        <td>
                                            <div class="ratio-container">
                                                <?php 
                                                $maxScore = $ranking[0]['score'] ?: 1;
                                                $percent = ($row['score'] / $maxScore) * 100;
                                                ?>
                                                <div class="progress-bar-bg">
                                                    <div class="progress-bar-fill" style="width: <?= $percent ?>%"></div>
                                                </div>
                                                <span class="ratio-text"><?= round($percent) ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>