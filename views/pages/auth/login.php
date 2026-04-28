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
    <title>Connexion</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-700 to-green-900 min-h-screen">

<div class="flex justify-center items-center min-h-[90vh] px-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- HEADER -->
        <div class="bg-green-600 text-white text-center px-6 py-8">
            <h1 class="text-lg font-semibold">FC Green Lions</h1>
            <p class="text-sm opacity-90 mt-1">Accédez à votre compte</p>
        </div>

        <!-- BODY -->
        <div class="p-6">
            <h2 class="text-center text-lg font-medium mb-5">Connexion</h2>

            <?php if ($msg): ?>
                <p class="bg-yellow-100 text-yellow-800 text-sm text-center p-2 rounded mb-4">
                    <?= htmlspecialchars($msg) ?>
                </p>
            <?php endif; ?>

            <form action="auth-signin" method="POST" class="space-y-3">

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                >

                <input 
                    type="password" 
                    name="mot_de_passe" 
                    placeholder="Mot de passe"
                    required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                >

                <button 
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition"
                >
                    Se connecter
                </button>

            </form>
        </div>

        <!-- FOOTER -->
        <div class="text-center p-4 text-sm">
            <a href="/page-register" class="text-green-600 hover:underline">
                Créer un compte
            </a>
        </div>

    </div>

</div>

</body>
</html>