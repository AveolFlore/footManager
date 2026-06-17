<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

use Config\Database;
use Models\Match_seance\MatchEntity;
use Models\Caisse\Caisse;
use Models\Activite_log\Activite;
use Models\utilisateur\User;

$db = (new Database())->connect();
$matchModel = new MatchEntity($db);
$caisseModel = new Caisse($db);
$activiteModel = new Activite($db);
$userModel = new User($db);

// Get Data
$nextMatch = $db->query("SELECT * FROM match_seance WHERE date >= CURDATE() ORDER BY date ASC LIMIT 1")->fetch();
$recentMatches = $db->query("SELECT * FROM match_seance ORDER BY date DESC LIMIT 4")->fetchAll();
$solde = $caisseModel->getSolde();
$activities = $activiteModel->getLatest(8);
$totalJoueurs = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'joueur'")->fetch()['count'];
$totalMatchs = $db->query("SELECT COUNT(*) as count FROM match_seance")->fetch()['count'];
$totalEquipes = 3; // Static for demo
$tauxPresence = 87; // Static for demo
$totalConvocations = 24; // Static for demo

// Mock classement data
$classement = [
    ['position' => 1, 'club' => 'FC Blue Lock', 'matches' => 12, 'wins' => 10, 'draws' => 1, 'losses' => 1, 'points' => 31],
    ['position' => 2, 'club' => 'Team V', 'matches' => 12, 'wins' => 9, 'draws' => 2, 'losses' => 1, 'points' => 29],
    ['position' => 3, 'club' => 'Team Z', 'matches' => 12, 'wins' => 8, 'draws' => 2, 'losses' => 2, 'points' => 26],
    ['position' => 4, 'club' => 'Team X', 'matches' => 12, 'wins' => 7, 'draws' => 2, 'losses' => 3, 'points' => 23],
    ['position' => 5, 'club' => 'Team Y', 'matches' => 12, 'wins' => 6, 'draws' => 3, 'losses' => 3, 'points' => 21],
];

$pageTitle = "Tableau de bord";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <style>
    :root {
        --blue-glow: #0052ff;
        --blue-hover: #003ec2;
        --text-dark: #ffffff;
        --text-muted: #cbd5e1;
        --success-green: #10b981;
        --purple-team: #7c3aed;
        --danger-red: #ef4444;
        --warning-gold: #f59e0b;
        --cyan-neon: #38bdf8;
        
        --panel-glass: rgba(15, 23, 42, 0.75);
        --card-glass: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.15);
        --th-glass: rgba(255, 255, 255, 0.08);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
        background: url('/assets/images/page_connect.jpg') no-repeat center center fixed;
        background-size: cover;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: quality;
        color: var(--text-dark);
        min-height: 100vh;
        padding-top: 80px;
    }

    main {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* En-tête */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 24px;
        margin-bottom: 32px;
        border-bottom: 1px solid var(--glass-border);
    }

    h1 {
        font-size: 32px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -1px;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .subtitle {
        margin-top: 4px;
        font-size: 12px;
        font-weight: 700;
        color: #60a5fa;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    /* Boutons */
    .btn {
        text-decoration: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-create {
        background-color: var(--success-green);
        color: white;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
    }
    .btn-create:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(5px);
    }
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
    }

    /* Grid des statistiques avec variations néon */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--panel-glass);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
    }

    /* Thématiques des cartes statistiques */
    .card-players::before { background: var(--cyan-neon); }
    .card-players p { color: var(--cyan-neon) !important; }

    .card-teams::before { background: var(--purple-team); }
    .card-teams p { color: var(--purple-team) !important; }

    .card-matches::before { background: var(--success-green); }
    .card-matches p { color: var(--success-green) !important; }

    .card-presence::before { background: var(--warning-gold); }
    .card-presence p { color: var(--warning-gold) !important; }

    .stat-card h3 {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 10px;
    }

    .stat-card p {
        font-size: 38px;
        font-weight: 900;
        letter-spacing: -1px;
    }

    /* Organisation Layout Multi-colonnes */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
        align-items: start;
    }

    .dashboard-main-col, .dashboard-side-col {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Panneaux Glassmorphism */
    .panel {
        background: var(--panel-glass);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        padding: 30px;
    }

    .panel-title {
        margin-top: 0;
        margin-bottom: 24px;
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 12px;
    }

    /* Matchs liste dynamique */
    .match-list {
        list-style: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .match-item {
        padding: 18px;
        background: linear-gradient(135deg, rgba(0, 82, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
        border: 1px solid rgba(0, 82, 255, 0.15);
        border-left: 5px solid var(--blue-glow);
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .match-item:hover {
        background: linear-gradient(135deg, rgba(0, 82, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
        border-color: rgba(0, 82, 255, 0.3);
        transform: scale(1.02);
    }

    .match-item strong {
        display: block;
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 6px;
    }

    .match-item span {
        font-size: 13px;
        font-weight: 600;
        color: #93c5fd;
    }

    /* Tableaux */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 16px 20px;
        text-align: left;
        border-bottom: 1px solid var(--glass-border);
        font-size: 14px;
        color: #e2e8f0;
    }

    th {
        background-color: var(--th-glass);
        color: #94a3b8;
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    tr { transition: background-color 0.2s; }
    tr:hover { background-color: rgba(255, 255, 255, 0.05); }

    .row-highlight {
        background: rgba(0, 82, 255, 0.25) !important;
        font-weight: 700;
    }
    .row-highlight td {
        color: #ffffff !important;
    }
    .row-highlight td:nth-child(2) {
        color: var(--cyan-neon) !important;
    }

    .caisse-amount {
        font-size: 38px; 
        font-weight: 900; 
        letter-spacing: -1px;
        color: var(--success-green);
        margin-bottom: 16px;
    }

    .link-details {
        color: var(--cyan-neon); 
        text-decoration: none; 
        font-weight: 700; 
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .link-details:hover {
        color: #7dd3fc;
        text-decoration: underline;
    }

    /* Responsive Layout */
    @media (max-width: 1150px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 1024px) {
        main {
            padding: 24px;
        }
    }

    @media (max-width: 768px) {
        .header-section { flex-direction: column; align-items: flex-start; gap: 16px; width: 100%; }
        .header-section div:last-child { display: flex; flex-direction: column; width: 100%; gap: 10px; }
        .btn { width: 100%; justify-content: center; }
        th, td { padding: 12px 14px; font-size: 13px; }
    }
</style>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>

    <main>
        <div class="header-section">
            <div>
                <h1>Tableau de bord</h1>
                <p class="subtitle">Bonjour, <?= htmlspecialchars($_SESSION['user']['prenom']) ?></p>
            </div>
            <div>
                <a href="/page-matchcreate" class="btn btn-create">Planifier Match</a>
                <a href="/page-convocation" class="btn btn-secondary">Convocation</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card card-players">
                <h3>Joueurs actifs</h3>
                <p><?= $totalJoueurs ?></p>
            </div>
            <div class="stat-card card-teams">
                <h3>Équipes</h3>
                <p><?= $totalEquipes ?></p>
            </div>
            <div class="stat-card card-matches">
                <h3>Matchs</h3>
                <p><?= $totalMatchs ?></p>
            </div>
            <div class="stat-card card-presence">
                <h3>Taux présence</h3>
                <p><?= $tauxPresence ?>%</p>
            </div>
        </div>

        <div class="dashboard-layout">
            
            <div class="dashboard-main-col">
                <div class="panel">
                    <h2 class="panel-title">Classement général</h2>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Club</th>
                                    <th>M</th>
                                    <th>V</th>
                                    <th>N</th>
                                    <th>D</th>
                                    <th>Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classement as $equipe): ?>
                                    <tr class="<?= $equipe['club'] === 'FC Blue Lock' ? 'row-highlight' : '' ?>">
                                        <td><?= $equipe['position'] ?></td>
                                        <td><?= htmlspecialchars($equipe['club']) ?></td>
                                        <td><?= $equipe['matches'] ?></td>
                                        <td><?= $equipe['wins'] ?></td>
                                        <td><?= $equipe['draws'] ?></td>
                                        <td><?= $equipe['losses'] ?></td>
                                        <td><?= $equipe['points'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard-side-col">
                <div class="panel">
                    <h2 class="panel-title">Derniers matchs</h2>
                    <?php if (empty($recentMatches)): ?>
                        <p style="color: var(--text-muted); font-size: 14px; font-weight: 500;">Aucun match récent</p>
                    <?php else: ?>
                        <ul class="match-list">
                            <?php foreach ($recentMatches as $match): ?>
                                <li class="match-item">
                                    <strong><?= htmlspecialchars($match['titre'] ?? 'Match') ?></strong>
                                    <span><?= date('d/m/Y H:i', strtotime($match['date'])) ?></span><br>
                                    <span style="font-size: 12px; opacity: 0.7; color: #ffffff; margin-top: 4px; display: inline-block;">
                                        <?= htmlspecialchars($match['lieu'] ?? 'Lieu inconnu') ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <?php if (in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur'])): ?>
                    <div class="panel">
                        <h2 class="panel-title">Solde caisse</h2>
                        <p class="caisse-amount"><?= number_format($solde ?? 0, 0, ',', ' ') ?> <span style="font-size: 18px; font-weight:700; color: #ffffff;">FCFA</span></p>
                        <a href="/page-finance" class="link-details">
                            Voir les détails financiers
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>
</html>