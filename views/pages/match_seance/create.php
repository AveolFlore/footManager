<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

if (!in_array($_SESSION['user']['role'], ['president', 'organisateur'])) {
    header('Location: /page-match');
    exit;
}

$pageTitle = "Créer un match";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-6 max-w-2xl mx-auto">
            
            <div class="mb-6">
                <a href="/page-match" class="text-blue-600 hover:underline text-sm">
                    &larr; Retour aux matchs
                </a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <strong>Erreur :</strong> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-6 text-gray-800">
                    Créer une nouvelle séance
                </h1>

                <form action="/matchSeance-create" method="POST" class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type de séance
                        </label>
                        <select name="type" class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none">
                            <option value="match">Match</option>
                            <option value="entr">Entraînement</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date et heure
                        </label>
                        <input type="datetime-local" name="date" id="match-date" required
                            class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lieu
                        </label>
                        <input type="text" name="lieu" required placeholder="Ex : Stade Municipal"
                            class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description / Informations complémentaires
                        </label>
                        <textarea name="description" rows="4" placeholder="Ajouter des détails sur la séance..."
                            class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none"></textarea>
                    </div>

                    <div class="flex gap-4 pt-2">
                        <button type="submit" name="add_match" value="Créer"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition-colors">
                            Créer la séance
                        </button>
                        <a href="/page-match"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 rounded text-center transition-colors">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const matchDate = document.getElementById('match-date');
            if (matchDate) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                matchDate.setAttribute('min', minDateTime);
            }
        });
    </script>
</body>
</html>