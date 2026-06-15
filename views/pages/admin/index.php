<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');

// DB + MODEL
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/utilisateur/User.php';

use Config\Database;
use Models\Utilisateur\User;

// connexion
$database = new Database();
$pdo = $database->connect();

// model
$userModel = new User($pdo);

// récupérer les utilisateurs
$users = $userModel->readAll();

// compter les demandes en attente
$total_attente = $userModel->countPending();

// récupérer les demandes en attente
$demandes = $userModel->getPendingUsers();

$pageTitle = "Administration";
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
            --text-dark: #0f172a;
            --text-muted: #475569;
            --success-green: #10b981;
            --purple-team: #7c3aed;
            --danger-red: #ef4444;
            --warning-gold: #f59e0b;
            
            /* Glassmorphism Cristallin Config */
            --panel-glass: rgba(255, 255, 255, 0.45);
            --card-glass: rgba(255, 255, 255, 0.6);
            --glass-border: rgba(255, 255, 255, 0.6);
            --th-glass: rgba(255, 255, 255, 0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            /* Application du background demandé */
             background: url('/assets/images/page_connect.jpg') no-repeat center center fixed;
            background-size: cover;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: quality;
            
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* Conteneur principal décalé pour laisser place à la sidebar fixe (280px) */
        main {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
            max-width: 1400px;
            width: calc(100% - 280px);
        }

        /* ------------------------------------
           HEADER SECTION
           ------------------------------------ */
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
            color: var(--text-dark);
        }

        .subtitle {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* ------------------------------------
           BOUTONS ET ACTIONS
           ------------------------------------ */
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

        .btn-blue {
            background-color: var(--blue-glow);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 82, 255, 0.15);
        }
        .btn-blue:hover {
            background-color: var(--blue-hover);
            transform: translateY(-1px);
        }

        .btn-purple {
            background-color: var(--purple-team);
            color: white;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
        }
        .btn-purple:hover {
            background-color: #6d28d9;
            transform: translateY(-1px);
        }

        .btn-action-edit {
            color: var(--blue-glow);
            background: none;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }
        .btn-action-edit:hover { color: var(--blue-hover); text-decoration: underline; }

        .btn-action-delete {
            color: var(--danger-red);
            background: none;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            margin-left: 16px;
            transition: color 0.2s;
        }
        .btn-action-delete:hover { color: #b91c1c; text-decoration: underline; }

        .btn-reject {
            background: none;
            border: none;
            color: var(--danger-red);
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px;
            transition: opacity 0.2s;
        }
        .btn-reject:hover { text-decoration: underline; opacity: 0.8; }

        /* ------------------------------------
           PANELS & ONGLETS (GLASS)
           ------------------------------------ */
        .panel {
            background: var(--panel-glass);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 82, 255, 0.04);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .tabs {
            display: flex;
            background-color: rgba(255, 255, 255, 0.2);
            border-bottom: 1px solid var(--glass-border);
        }

        .tab-btn {
            flex: 1;
            padding: 20px;
            background: none;
            border: none;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
        }
        .tab-btn:hover { background-color: rgba(255, 255, 255, 0.3); color: var(--text-dark); }
        
        .tab-active {
            background-color: rgba(255, 255, 255, 0.5);
            color: var(--text-dark);
            border-bottom: 3px solid var(--blue-glow);
        }

        .badge {
            background-color: var(--blue-glow);
            color: white;
            border-radius: 8px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 800;
            margin-left: 6px;
        }

        .panel-content { padding: 30px; }
        .panel-title { margin-top: 0; margin-bottom: 24px; font-size: 16px; font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ------------------------------------
           DEMANDES EN ATTENTE (CARDS)
           ------------------------------------ */
        .card-list { display: flex; flex-direction: column; gap: 16px; }
        
        .request-card {
            border: 1px solid var(--glass-border);
            border-left: 6px solid var(--warning-gold);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-glass);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .user-info h4 { margin: 0 0 4px 0; font-size: 18px; font-weight: 800; color: var(--text-dark); }
        .user-info p { margin: 0; font-size: 14px; color: var(--text-muted); font-weight: 500; }
        
        .user-info .status-text { 
            font-size: 12px; 
            margin-top: 10px; 
            color: #b45309; 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .team-actions { display: flex; gap: 10px; margin-top: 16px; }
        .right-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 16px; }

        .status-badge {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 800;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ------------------------------------
           TABLEAUX DE DONNÉES
           ------------------------------------ */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 18px 24px;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
            font-size: 14px;
        }

        th {
            background-color: var(--th-glass);
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        tr { transition: background-color 0.2s; }
        tr:hover { background-color: rgba(255, 255, 255, 0.3); }
        .user-name-cell { font-weight: 700; color: var(--text-dark); }
        
        /* Badges de statuts */
        .badge-info { 
            background-color: rgba(0, 82, 255, 0.1); 
            color: var(--blue-glow); 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 11px; 
            font-weight: 800; 
            text-transform: uppercase; 
        }
        .badge-success { 
            background-color: rgba(16, 185, 129, 0.1); 
            color: var(--success-green); 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 11px; 
            font-weight: 800; 
            text-transform: uppercase; 
        }
        .badge-warning { 
            background-color: rgba(245, 158, 11, 0.1); 
            color: var(--warning-gold); 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 11px; 
            font-weight: 800; 
            text-transform: uppercase; 
        }

        /* ------------------------------------
           RESPONSIVE
           ------------------------------------ */
        @media (max-width: 1024px) {
            main {
                margin-left: 0;
                width: 100%;
                padding: 24px;
            }
            body { flex-direction: column; }
        }

        @media (max-width: 768px) {
            .header-section { flex-direction: column; align-items: flex-start; gap: 16px; }
            .request-card { flex-direction: column; align-items: flex-start; gap: 20px; }
            .right-actions { align-items: flex-start; width: 100%; border-top: 1px solid var(--glass-border); padding-top: 16px; }
            .tabs { flex-direction: column; }
            th, td { padding: 14px 16px; font-size: 13px; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>

<body>

    <!-- Inclusion de la sidebar filtrée light -->
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main>
        <!-- SECTION EN-TÊTE -->
        <div class="header-section">
            <div>
                <h1>Administration</h1>
                <p class="subtitle">Gestion des membres et du protocole de sélection</p>
            </div>
            <a href="/page-admincreateuser" class="btn btn-create">Créer un utilisateur</a>
        </div>

        <!-- DEMANDES DE VALIDATION -->
        <div class="panel">
            <div class="tabs">
                <button class="tab-btn tab-active">
                    Validation Joueurs <span class="badge"><?= $total_attente ?></span>
                </button>
                <button class="tab-btn">
                    Paramètres Club
                </button>
            </div>

            <div class="panel-content">
                <h3 class="panel-title">Demandes en attente</h3>

                <div class="card-list">
                    <?php foreach ($demandes as $d): ?>
                        <div class="request-card">
                            
                            <div style="flex: 1;">
                                <div class="user-info">
                                    <h4><?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?></h4>
                                    <p><?= htmlspecialchars($d['email']) ?></p>
                                    <div class="status-text">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        En attente de traitement du dossier
                                    </div>
                                </div>

                                <div class="team-actions">
                                    <form action="/admin-validateuser" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="equipe_id" value="1">
                                        <button type="submit" class="btn btn-blue" style="padding: 10px 16px; font-size: 11px; border-radius: 8px;">Équipe A</button>
                                    </form>

                                    <form action="/admin-validateuser" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="equipe_id" value="2">
                                        <button type="submit" class="btn btn-purple" style="padding: 10px 16px; font-size: 11px; border-radius: 8px;">Équipe B</button>
                                    </form>
                                </div>
                            </div>

                            <div class="right-actions">
                                <span class="status-badge">Attente</span>

                                <form action="/admin-rejetuser" method="POST">
                                    <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn-reject">Refuser</button>
                                </form>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- LISTE GLOBALE DES MEMBRES -->
        <div class="panel">
            <div class="panel-content" style="padding: 0;">
                <div style="padding: 24px 30px; border-bottom: 1px solid var(--glass-border);">
                    <h3 class="panel-title" style="margin: 0;">Tous les utilisateurs</h3>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="user-name-cell">
                                        <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-weight: 500;"><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge-info">
                                            <?= htmlspecialchars($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['statut'] === 'actif'): ?>
                                            <span class="badge-success"><?= htmlspecialchars($user['statut']) ?></span>
                                        <?php else: ?>
                                            <span class="badge-warning"><?= htmlspecialchars($user['statut']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <a href="/admin-edituser?id=<?= $user['id'] ?>" class="btn-action-edit">Modifier</a>
                                        <a href="/admin-deleteuser?id=<?= $user['id'] ?>" class="btn-action-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
</html>