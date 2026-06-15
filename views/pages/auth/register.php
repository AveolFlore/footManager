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
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>FC Blue Lock</h1>
                <p>Inscription</p>
            </div>
            <div class="auth-body">
                <?php if ($msg): ?>
                    <div class="msg"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>

                <form action="auth-signup" method="POST" enctype="multipart/form-data">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>

                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone">

                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" required>

                    <label for="poste">Poste</label>
                    <select id="poste" name="poste" required>
                        <option value="">-- Poste --</option>
                        <option value="gard">Gardien</option>
                        <option value="def">Défenseur</option>
                        <option value="mil">Milieu</option>
                        <option value="att">Attaquant</option>
                    </select>

                    <label for="pied_dominant">Pied dominant</label>
                    <select id="pied_dominant" name="pied_dominant">
                        <option value="">-- Pied dominant --</option>
                        <option value="droit">Droit</option>
                        <option value="gauche">Gauche</option>
                        <option value="2">Les deux</option>
                    </select>

                    <label for="numero_maillot">Numéro maillot</label>
                    <input type="number" id="numero_maillot" name="numero_maillot" min="1" max="999">

                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="6">

                    <label for="confirm_mot_de_passe">Confirmer mot de passe</label>
                    <input type="password" id="confirm_mot_de_passe" name="confirm_mot_de_passe" required minlength="6">

                    <label for="photo_profil">Photo de profil (optionnel)</label>
                    <input type="file" id="photo_profil" name="photo_profil" accept="image/*">

                    <input type="submit" value="S'inscrire">
                </form>

                <div class="auth-footer">
                    <p>Déjà membre ? <a href="/page-login">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
