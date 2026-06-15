<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user'])) {
    header("Location:/page-home");
    exit;
}

$msg = $_GET['msg'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FC Blue Lock - Inscription</title>
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        :root {
            --blue-glow: #0052ff;
            --blue-hover: #003ec2;
            --text-dark: #0f172a;
            --text-muted: #475569;
            
            /* Glassmorphism Cristallin Épuré (Identique connexion) */
            --glass-bg: rgba(255, 255, 255, 0.45);
            --glass-border: rgba(255, 255, 255, 0.6);
            --input-glass: rgba(255, 255, 255, 0.7);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            /* Rendu net 4K sans étirement destructeur */
            background: url('/assets/images/page_inscript.jpg') no-repeat center center fixed;
            background-size: cover;
            
            /* Force le rendu propre des pixels */
            image-rendering: -webkit-optimize-contrast;
            image-rendering: quality;
            
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            overflow: hidden;
        }

        /* ------------------------------------
           STRUCTURE DU MODAL CONTAINER
           ------------------------------------ */
        .modal-container {
            width: 100%;
            max-width: 1150px; 
            height: 80vh; /* Hauteur fixe basée sur le viewport pour l'alignement parfait du GIF */
            min-height: 680px;
            background: transparent;
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 30px 100px rgba(0, 82, 255, 0.12);
            z-index: 10;
        }

        /* PARTIE GAUCHE : FORMULAIRE AVEC SCROLL INTERNE */
        .modal-left {
            flex: 40;
            padding: 50px;
            display: flex;
            flex-direction: column;
            
            /* Isolation du flou de verre */
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            
            border-right: 1px solid var(--glass-border);
            overflow-y: auto; /* Permet de défiler proprement si l'écran est petit */
        }

        /* Personnalisation de la barre de défilement interne */
        .modal-left::-webkit-scrollbar {
            width: 6px;
        }
        .modal-left::-webkit-scrollbar-track {
            background: transparent;
        }
        .modal-left::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .auth-header {
            margin-bottom: 30px;
        }

        .auth-header h1 {
            font-size: 36px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1.5px;
            line-height: 1;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .auth-header h1 span {
            color: var(--blue-glow);
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .msg {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 24px;
            font-weight: 600;
        }

        /* Grille adaptative pour les nombreux champs */
        form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        /* Les éléments comme les boutons, la photo ou les messages prennent toute la largeur */
        .full-width {
            grid-column: span 2;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="password"],
        select {
            background: var(--input-glass);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            color: var(--text-dark);
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            outline: none;
        }

        /* Style spécifique et propre pour l'input file */
        input[type="file"] {
            background: rgba(255, 255, 255, 0.3);
            border: 1px dashed var(--glass-border);
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
        }

        input:focus, select:focus {
            border-color: var(--blue-glow);
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 82, 255, 0.15);
        }

        input[type="submit"] {
            background: var(--blue-glow);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
            box-shadow: 0 6px 20px rgba(0, 82, 255, 0.25);
            width: 100%;
        }

        input[type="submit"]:hover {
            background: var(--blue-hover);
            transform: translateY(-1px);
        }

        .auth-footer {
            margin-top: 30px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            font-weight: 500;
        }

        .auth-footer a {
            color: var(--blue-glow);
            text-decoration: none;
            font-weight: 700;
        }

        /* PARTIE DROITE : GIF PROPRE (60%) */
        .modal-right {
            flex: 60;
            position: relative;
            background: rgba(255, 255, 255, 0.15);
            overflow: hidden;
        }

        .modal-media {
            width: 100%;
            height: 100%;
            object-fit: cover; 
            position: absolute;
            inset: 0;
        }

        /* Responsive */
        @media (max-width: 850px) {
            body { overflow-y: auto; }
            .modal-container {
                flex-direction: column;
                max-width: 460px;
                height: auto;
                min-height: auto;
            }
            .modal-right { display: none; }
            form {
                grid-template-columns: 1fr;
            }
            .full-width {
                grid-column: span 1;
            }
            .modal-left {
                flex: 1;
                padding: 40px 24px;
                border-right: none;
                overflow-y: visible;
            }
        }
    </style>
</head>
<body>

    <!-- MODAL PRINCIPAL -->
    <div class="modal-container">
        
        <!-- PARTIE GAUCHE (GLASSMORPHISM AVEC CHAMPS EN GRILLE) -->
        <div class="modal-left">
            <div class="auth-header">
                <h1>FC <span>Blue Lock</span></h1>
                <p>Création du profil d'égoïste</p>
            </div>
            
            <div class="auth-body">
                <?php if ($msg): ?>
                    <div class="full-width msg"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>

                <form action="auth-signup" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" required placeholder="Ex: Isagi">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" required placeholder="Ex: Yoichi">
                    </div>

                    <div class="form-group full-width">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="egoist@bluelock.jp">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" placeholder="+33 6 ...">
                    </div>

                    <div class="form-group">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" required>
                    </div>

                    <div class="form-group">
                        <label for="poste">Poste</label>
                        <select id="poste" name="poste" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="gard">Gardien</option>
                            <option value="def">Défenseur</option>
                            <option value="mil">Milieu</option>
                            <option value="att">Attaquant</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pied_dominant">Pied dominant</label>
                        <select id="pied_dominant" name="pied_dominant">
                            <option value="">-- Sélectionner --</option>
                            <option value="droit">Droit</option>
                            <option value="gauche">Gauche</option>
                            <option value="2">Les deux</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="numero_maillot">Numéro maillot</label>
                        <input type="number" id="numero_maillot" name="numero_maillot" min="1" max="999" placeholder="15">
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="6" placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label for="confirm_mot_de_passe">Confirmation</label>
                        <input type="password" id="confirm_mot_de_passe" name="confirm_mot_de_passe" required minlength="6" placeholder="••••••••">
                    </div>

                    <div class="form-group full-width">
                        <label for="photo_profil">Photo de profil (optionnel)</label>
                        <input type="file" id="photo_profil" name="photo_profil" accept="image/*">
                    </div>

                    <div class="full-width">
                        <input type="submit" value="S'inscrire">
                    </div>
                </form>

                <div class="auth-footer full-width">
                    <p>Déjà membre ? <a href="/page-login">Se connecter</a></p>
                </div>
            </div>
        </div>

        <!-- PARTIE DROITE (GIF TOTALEMENT NET ET EXTENSIBLE SUR 60%) -->
        <div class="modal-right">
            <img class="modal-media" src="/assets/images/Blue_Lock.jpg" alt="Egoist Dev Flow">
        </div>
    </div>

</body>
</html>