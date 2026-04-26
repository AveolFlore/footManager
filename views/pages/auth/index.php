<?php
$msg = $_GET['msg'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Joueur</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-700 to-green-900 min-h-screen">

<?php include_once __DIR__ . '/../../partials/header.php'; ?>

<div class="flex justify-center items-center min-h-[90vh] px-4">

    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- HEADER -->
        <div class="bg-green-600 text-white text-center px-6 py-8">
            <h1 class="text-lg font-semibold">FC Green Lions</h1>
            <p class="text-sm opacity-90 mt-1">Rejoignez le club</p>
        </div>

        <!-- BODY -->
        <div class="p-6">
            <h2 class="text-center text-lg font-medium mb-5">Inscription</h2>

            <?php if ($msg): ?>
                <p class="bg-yellow-100 text-yellow-800 text-sm text-center p-2 rounded mb-4">
                    <?= htmlspecialchars($msg) ?>
                </p>
            <?php endif; ?>

            <form action="auth-signup" method="POST" enctype="multipart/form-data" class="space-y-3">

                <input type="text" name="nom" placeholder="Nom" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <input type="text" name="prenom" placeholder="Prénom" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <input type="email" name="email" placeholder="Email" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <input type="text" name="telephone" placeholder="Téléphone"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <input type="date" name="date_naissance" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <select name="poste" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">-- Poste --</option>
                    <option value="gard">Gardien</option>
                    <option value="def">Défenseur</option>
                    <option value="mil">Milieu</option>
                    <option value="att">Attaquant</option>
                </select>

                <select name="pied_dominant"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pied dominant --</option>
                    <option value="droit">Droit</option>
                    <option value="gauche">Gauche</option>
                    <option value="2">Les deux</option>
                </select>

                <input type="number" name="numero_maillot" placeholder="Numéro maillot (optionnel)"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <div>
                    <label class="text-sm mb-1 block">Photo de profil</label>
                    <input type="file" name="photo_profil" accept="image/*"
                        class="w-full text-sm">
                </div>

                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required minlength="6"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer mot de passe" required minlength="6"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                    S'inscrire
                </button>

            </form>
        </div>

        <!-- FOOTER -->
        <div class="text-center p-4 text-sm">
            <a href="/page-login" class="text-green-600 hover:underline">
                Déjà un compte ? Se connecter
            </a>
        </div>

    </div>

</div>

</body>
</html>