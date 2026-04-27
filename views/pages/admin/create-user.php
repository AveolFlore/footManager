<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    require_once __DIR__ . '/../../../middleware/Role.php';

    requireRole('president');
    $msg = $_GET['msg'] ?? null;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer utilisateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <!-- CONTENU -->
    <main class="flex-1 p-6 overflow-y-auto">

        <h1 class="text-2xl font-semibold mb-6">Créer un utilisateur</h1>

        <div class="bg-white p-6 rounded-xl shadow max-w-lg">

        
            <?php if ($msg): ?>
                <p class="bg-yellow-100 text-yellow-800 text-sm text-center p-2 rounded mb-4">
                    <?= htmlspecialchars($msg) ?>
                </p>
            <?php endif; ?>

            <form action="/auth-adminstoreuser" method="POST" class="space-y-4">

                <input type="text" name="nom" placeholder="Nom"
                    class="w-full p-3 border rounded-lg" required>

                <input type="text" name="prenom" placeholder="Prénom"
                    class="w-full p-3 border rounded-lg" required>

                <input type="email" name="email" placeholder="Email"
                    class="w-full p-3 border rounded-lg" required>

                <input type="text" name="telephone" placeholder="Téléphone"
                    class="w-full p-3 border rounded-lg">

                <input type="date" name="date_naissance"
                    class="w-full p-3 border rounded-lg">

                <select name="poste" class="w-full p-3 border rounded-lg">
                    <option value="">-- Poste --</option>
                    <option value="gard">Gardien</option>
                    <option value="def">Défenseur</option>
                    <option value="mil">Milieu</option>
                    <option value="att">Attaquant</option>
                </select>

                <select name="pied_dominant" class="w-full p-3 border rounded-lg">
                    <option value="">-- Pied dominant --</option>
                    <option value="droit">Droit</option>
                    <option value="gauche">Gauche</option>
                    <option value="2">Les deux</option>
                </select>

                <input type="number" name="numero_maillot"
                    placeholder="Numéro maillot"
                    class="w-full p-3 border rounded-lg">

                <input type="password" name="mot_de_passe"
                    placeholder="Mot de passe"
                    class="w-full p-3 border rounded-lg" required>

                <!-- ROLE -->
                <select name="role" required
                    class="w-full p-3 border rounded-lg">
                    <option value="">-- Rôle --</option>
                    <option value="joueur">Joueur</option>
                    <option value="medecin">Medecin</option>
                    <option value="censeur">Censeur</option>
                    <option value="entraineur">Entraineur</option>
                    <option value="organisateur">Organisateur</option>
                    <option value="president">President</option>
                </select>

                <button
                    class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">
                    Créer utilisateur
                </button>

            </form>
        </div>

    </main>
</div>

</body>
</html>