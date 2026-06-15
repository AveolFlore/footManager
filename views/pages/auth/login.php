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
    <title>FC Blue Lock - Connexion</title>
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        :root {
            --blue-glow: #0052ff;
            --blue-hover: #003ec2;
            --text-dark: #0f172a;
            --text-muted: #475569;
            
            /* Glassmorphism Cristallin Épuré */
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
            background: url('/assets/images/page_connect.jpg') no-repeat center center fixed;
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
            min-height: 640px;
            background: transparent; /* On laisse transparent pour ne pas interférer */
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 30px 100px rgba(0, 82, 255, 0.12);
            z-index: 10;
        }

        /* 
           PARTIE GAUCHE : FORMULAIRE
           C'est ICI et UNIQUEMENT ICI qu'on applique le flou glassmorphism 
        */
        .modal-left {
            flex: 40;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            
            /* Le flou est isolé ici, il ne bavera plus sur le reste de l'écran */
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            
            border-right: 1px solid var(--glass-border);
        }

        .auth-header {
            margin-bottom: 40px;
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

        form {
            display: flex;
            flex-direction: column;
            gap: 24px;
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

        input[type="email"],
        input[type="password"] {
            background: var(--input-glass);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            color: var(--text-dark);
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
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
            margin-top: 12px;
            box-shadow: 0 6px 20px rgba(0, 82, 255, 0.25);
        }

        input[type="submit"]:hover {
            background: var(--blue-hover);
            transform: translateY(-1px);
        }

        .auth-footer {
            margin-top: 40px;
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

        /* 
           PARTIE DROITE : GIF (60%)
           Aucun flou ici, rendu brut et propre pour le média
        */
        .modal-right {
            flex: 60;
            position: relative;
            background: rgba(255, 255, 255, 0.15); /* Fond transparent pour lier avec la gauche */
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
                min-height: auto;
            }
            .modal-right { display: none; }
            .modal-left {
                flex: 1;
                padding: 40px 24px;
                border-right: none;
            }
        }
    </style>
</head>
<body>

    <!-- MODAL PRINCIPAL -->
    <div class="modal-container">
        
        <!-- PARTIE GAUCHE (GLASSMORPHISM EMBEDDED) -->
        <div class="modal-left">
            <div class="auth-header">
                <h1>FC <span>Blue Lock</span></h1>
                <p>Système d'évaluation de l'égoïsme</p>
            </div>
            
            <div class="auth-body">
                <?php if ($msg): ?>
                    <div class="msg"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>

                <form action="auth-signin" method="POST">
                    <div class="form-group">
                        <label for="email">Identifiant Email</label>
                        <input type="email" id="email" name="email" required placeholder="egoist@bluelock.jp">
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe">Code d'accès</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" required placeholder="••••••••">
                    </div>

                    <input type="submit" value="Initialiser la connexion">
                </form>

                <div class="auth-footer">
                    <p>Non enregistré dans le projet ? <a href="/page-register">Rejoindre la sélection</a></p>
                </div>
            </div>
        </div>

        <!-- PARTIE DROITE (GIF PROPRE ET DÉGAGE SANS CONFLIT DE FLOU) -->
        <div class="modal-right">
            <img class="modal-media" src="/assets/images/blue-lock-10.gif" alt="Egoist Dev Flow">
        </div>
    </div>

</body>
</html>