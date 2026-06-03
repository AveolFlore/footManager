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
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">

                <!-- Retour -->
                <a href="/page-match"
                    class="flex items-center gap-2 text-slate-500 hover:text-slate-700 mb-6 font-semibold">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux matchs
                </a>

                <!-- Message flash -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl bg-gradient-to-r from-red-50 to-pink-50 text-red-700 font-semibold border border-red-200 shadow-sm">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <!-- Card formulaire -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 max-w-2xl shadow-xl">

                    <h1 class="text-3xl font-extrabold text-slate-800 mb-8 flex items-center gap-4">
                        <i class="fas fa-plus-circle text-green-600"></i>
                        Créer un match
                    </h1>

                    <form action="/matchSeance-create" method="POST" class="space-y-6">

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Type de séance
                            </label>
                            <select name="type"
                                class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                                <option value="match">Match</option>
                                <option value="entr">Entraînement</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Date et heure
                            </label>
                            <input type="datetime-local" name="date"
                                class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"
                                id="match-date"
                                required>
                        </div>

                        <!-- Lieu -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Lieu
                            </label>
                            <input type="text" name="lieu"
                                placeholder="Ex: Stade Municipal"
                                class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"
                                required>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" rows="4"
                                placeholder="Informations supplémentaires..."
                                class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"></textarea>
                        </div>

                        <!-- Boutons -->
                        <div class="flex gap-4 pt-4">
                            <button type="submit" name="add_match" value="Créer"
                                class="flex-1 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-lg font-extrabold rounded-2xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                                <i class="fas fa-check mr-2"></i>
                                Créer
                            </button>
                            <a href="/page-match"
                                class="flex-1 px-8 py-4 border-2 border-slate-200 text-slate-700 text-lg font-bold rounded-2xl hover:bg-slate-50 transition-all bg-white shadow-sm">
                                Annuler
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </main>
    </div>

</body>
<script>
    // Définir la date minimale comme la date et l'heure actuelles
    document.addEventListener('DOMContentLoaded', function() {
        const matchDate = document.getElementById('match-date');
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        matchDate.setAttribute('min', minDateTime);
    });
</script>

</html>
