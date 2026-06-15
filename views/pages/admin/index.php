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
        
        /* Boutons globaux */
        .btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-create { background-color: #28a745; color: white; }
        .btn-create:hover { background-color: #218838; }
        .btn-blue { background-color: #007bff; color: white; }
        .btn-blue:hover { background-color: #0069d9; }
        .btn-purple { background-color: #6f42c1; color: white; }
        .btn-purple:hover { background-color: #5a32a3; }
        .btn-action-edit { color: #007bff; background: none; font-size: 12px; font-weight: bold; text-decoration: none; }
        .btn-action-edit:hover { text-decoration: underline; }
        .btn-action-delete { color: #dc3545; background: none; font-size: 12px; font-weight: bold; text-decoration: none; margin-left: 10px; }
        .btn-action-delete:hover { text-decoration: underline; }

        /* Conteneurs & Onglets */
        .panel {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .tabs {
            display: flex;
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
        }
        .tab-btn {
            flex: 1;
            padding: 15px;
            background: none;
            border: none;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            color: #495057;
            cursor: pointer;
            text-align: center;
        }
        .tab-btn:hover { background-color: #dfe2e6; }
        .tab-active {
            background-color: #fff;
            color: #212529;
            border-bottom: 3px solid #28a745;
        }
        .badge {
            background-color: #28a745;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: 5px;
        }
        .panel-content { padding: 20px; }
        .panel-title { margin-top: 0; margin-bottom: 20px; font-size: 18px; color: #495057; text-transform: uppercase; }

        /* Demandes d'attente (Cartes listes) */
        .card-list { display: flex; flex-direction: column; gap: 15px; }
        .request-card {
            border: 1px solid #dee2e6;
            border-left: 5px solid #ffc107;
            border-radius: 4px;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
        }
        .user-info h4 { margin: 0 0 5px 0; font-size: 18px; }
        .user-info p { margin: 0; font-size: 14px; color: #666; }
        .user-info .status-text { font-size: 12px; margin-top: 8px; color: #856404; display: flex; align-items: center; gap: 5px; }
        .team-actions { display: flex; gap: 10px; margin-top: 12px; }
        .right-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 15px; }
        .status-badge {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .btn-reject {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px;
        }
        .btn-reject:hover { text-decoration: underline; }

        /* Tableaux */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        th {
            background-color: #f8f9fa;
            color: #495057;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tr:hover { background-color: #f1f3f5; }
        .user-name-cell { font-weight: bold; color: #212529; }
        
        /* Badges de rôles et statuts génériques */
        .badge-info { background-color: #e2f0fe; color: #004085; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-success { background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-warning { background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }

        /* Responsive */
        @media (max-width: 768px) {
            .header-section { flex-direction: column; align-items: flex-start; gap: 15px; }
            .request-card { flex-direction: column; align-items: flex-start; gap: 15px; }
            .right-actions { align-items: flex-start; width: 100%; border-top: 1px solid #dee2e6; padding-top: 10px; }
            .tabs { flex-direction: column; }
            th, td { padding: 10px; font-size: 13px; }
        }
    </style>
</head>

<body>

    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main>
        <div class="header-section">
            <div>
                <h1>Administration</h1>
                <p class="subtitle">Gestion des membres et du protocole de sélection</p>
            </div>
            <a href="/page-admincreateuser" class="btn btn-create">Créer un utilisateur</a>
        </div>

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
                                    <div class="status-text">En attente de traitement du dossier</div>
                                </div>

                                <div class="team-actions">
                                    <form action="/admin-validateuser" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="equipe_id" value="1">
                                        <button type="submit" class="btn btn-blue" style="padding: 6px 12px; font-size: 11px;">Équipe A</button>
                                    </form>

                                    <form action="/admin-validateuser" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="equipe_id" value="2">
                                        <button type="submit" class="btn btn-purple" style="padding: 6px 12px; font-size: 11px;">Équipe B</button>
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

        <div class="panel">
            <div class="panel-content" style="padding: 0;">
                <div style="padding: 20px; border-bottom: 1px solid #dee2e6;">
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
                                    <td style="color: #495057;"><?= htmlspecialchars($user['email']) ?></td>
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