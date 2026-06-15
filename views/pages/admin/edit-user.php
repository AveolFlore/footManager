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

// Récupérer les équipes pour le select
$equipes = $pdo->query("SELECT * FROM equipes")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Modifier Utilisateur";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <style>
        body {
            font-family: sans-serif;
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
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .subtitle {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .btn-back {
            text-decoration: none;
            color: #555;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-back:hover {
            background-color: #eee;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #0066cc;
        }
        input:disabled {
            background-color: #f0f0f0;
            color: #777;
            cursor: not-allowed;
            border-color: #ddd;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .btn-cancel {
            text-decoration: none;
            color: #333;
            background-color: #e0e0e0;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }
        .btn-cancel:hover {
            background-color: #d5d5d5;
        }
        button[type="submit"] {
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #0052a3;
        }
        @media (max-width: 600px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .form-actions {
                flex-direction: column;
            }
            .btn-cancel, button[type="submit"] {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>

    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main>
        <div class="container">

            <div class="header">
                <div>
                    <h1>Modifier Utilisateur</h1>
                    <p class="subtitle">Gestion des informations et critères du membre</p>
                </div>
                <a href="/page-admin" class="btn-back">Retour</a>
            </div>

            <form action="/admin-updateuser" method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                <div class="form-group">
                    <label>Nom & Prénom</label>
                    <div class="grid-2">
                        <input type="text" value="<?= htmlspecialchars($user['nom']) ?>" disabled>
                        <input type="text" value="<?= htmlspecialchars($user['prenom']) ?>" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>

                <div class="form-group grid-2">
                    <div>
                        <label>Rôle</label>
                        <select name="role">
                            <option value="joueur" <?= $user['role'] === 'joueur' ? 'selected' : '' ?>>Joueur</option>
                            <option value="president" <?= $user['role'] === 'president' ? 'selected' : '' ?>>Président</option>
                            <option value="entraineur" <?= $user['role'] === 'entraineur' ? 'selected' : '' ?>>Entraîneur</option>
                            <option value="tresorier" <?= $user['role'] === 'tresorier' ? 'selected' : '' ?>>Trésorier</option>
                        </select>
                    </div>
                    <div>
                        <label>Statut</label>
                        <select name="statut">
                            <option value="en_attente" <?= $user['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="valide" <?= $user['statut'] === 'valide' ? 'selected' : '' ?>>Validé</option>
                            <option value="refuse" <?= $user['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Équipe</label>
                    <select name="equipe_id">
                        <option value="">Aucune équipe</option>
                        <?php foreach ($equipes as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $user['equipe_id'] == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="/page-admin" class="btn-cancel">Annuler</a>
                    <button type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </main>

</body>

</html>