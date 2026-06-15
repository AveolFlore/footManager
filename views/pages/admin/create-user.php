<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');
$msg = $_GET['msg'] ?? null;

$pageTitle = "Créer Utilisateur";
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
        .alert {
            background-color: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #f8b4b4;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
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
        button[type="submit"] {
            width: 100%;
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
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
        }
    </style>
</head>

<body>

    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main>
        <div class="container">

            <div class="header">
                <div>
                    <h1>Créer un utilisateur</h1>
                    <p class="subtitle">Espace d'administration / Recrutement Elite</p>
                </div>
                <a href="/page-admin" class="btn-back">Retour</a>
            </div>

            <?php if ($msg): ?>
                <div class="alert">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <form action="/auth-adminstoreuser" method="POST">

                <div class="form-group grid-2">
                    <div>
                        <label>Nom</label>
                        <input type="text" name="nom" placeholder="Nom" required>
                    </div>
                    <div>
                        <label>Prénom</label>
                        <input type="text" name="prenom" placeholder="Prénom" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email professionnel" required>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" placeholder="Téléphone">
                </div>

                <div class="form-group">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance">
                </div>

                <div class="form-group grid-2">
                    <div>
                        <label>Poste</label>
                        <select name="poste">
                            <option value="">-- Poste --</option>
                            <option value="gard">Gardien</option>
                            <option value="def">Défenseur</option>
                            <option value="mil">Milieu</option>
                            <option value="att">Attaquant</option>
                        </select>
                    </div>
                    <div>
                        <label>Pied dominant</label>
                        <select name="pied_dominant">
                            <option value="">-- Pied dominant --</option>
                            <option value="droit">Droit</option>
                            <option value="gauche">Gauche</option>
                            <option value="2">Les deux</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Numéro de maillot</label>
                    <input type="number" name="numero_maillot" placeholder="Numéro maillot" min="1" max="99">
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" placeholder="Mot de passe confidentiel" required>
                </div>

                <div class="form-group">
                    <label>Rôle système</label>
                    <select name="role" required>
                        <option value="">-- Rôle --</option>
                        <option value="joueur">Joueur</option>
                        <option value="medecin">Médecin</option>
                        <option value="censeur">Censeur</option>
                        <option value="entraineur">Entraîneur</option>
                        <option value="organisateur">Organisateur</option>
                        <option value="president">Président</option>
                    </select>
                </div>

                <button type="submit">Créer l'utilisateur interne</button>

            </form>
        </div>
    </main>

</body>
</html>