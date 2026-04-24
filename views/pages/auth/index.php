<?php
$msg = $_GET['msg'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Joueur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box fade-in">

        <h2>Inscription Joueur</h2>

        <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="#" method="POST" enctype="multipart/form-data">

            <!-- IDENTITÉ -->
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>

            <!-- CONTACT -->
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="telephone" placeholder="Téléphone">

            <!-- DATE NAISSANCE -->
            <input type="date" name="date_naissance" required>

            <!-- SPORT -->
            <select name="poste" required>
                <option value="">-- Poste --</option>
                <option value="gard">Gardien</option>
                <option value="def">Défenseur</option>
                <option value="mil">Milieu</option>
                <option value="att">Attaquant</option>
            </select>

            <select name="pied_dominant">
                <option value="">-- Pied dominant --</option>
                <option value="droit">Droit</option>
                <option value="gauche">Gauche</option>
                <option value="2">Les deux</option>
            </select>

            <input type="number" name="numero_maillot" placeholder="Numéro maillot (optionnel)">

            <!-- PHOTO PROFIL -->
            <label>Photo de profil</label>
            <input type="file" name="photo_profil" accept="image/*">

            <!-- MOT DE PASSE -->
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required minlength="6">
            <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer mot de passe" required minlength="6">

            <input type="submit" value="S'inscrire">

        </form>

        <a href="/page-login">Déjà un compte ? Se connecter</a>

    </div>
</div>

</body>
</html>