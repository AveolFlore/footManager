<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

// seuls president et organisateur peuvent modifier un match
if (!in_array($_SESSION['user']['role'], ['president', 'organisateur'])) {
    header('Location: /page-match');
    exit;
}

// récupérer les données du match depuis la session
$id          = $_SESSION['match_id'] ?? null;
$type        = $_SESSION['match_type'] ?? null;
$date        = $_SESSION['match_date'] ?? null;
$lieu        = $_SESSION['match_lieu'] ?? null;
$description = $_SESSION['match_description'] ?? null;

// si pas de données en session on redirige
if (!$id) {
    header('Location: /page-match');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un match</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6">

            <!-- Retour -->
            <a href="/page-match"
               class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
                ← Retour aux matchs
            </a>

            <!-- Message flash -->
            <?php if (isset($_GET['msg'])): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-700 text-sm">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
            <?php endif; ?>

            <!-- Card formulaire -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">

                <h1 class="text-xl font-semibold text-gray-800 mb-6">Modifier le match</h1>

                <form action="/matchSeance-update" method="POST">

                    <!-- Id caché -->
                    <input type="hidden" name="match_id" value="<?= $id ?>">

                    <!-- Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type de séance
                        </label>
                        <select name="type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="match" <?= $type === 'match' ? 'selected' : '' ?>>Match</option>
                            <option value="entr"  <?= $type === 'entr'  ? 'selected' : '' ?>>Entraînement</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date et heure
                        </label>
                        <input type="datetime-local" name="date"
                               value="<?= date('Y-m-d\TH:i', strtotime($date)) ?>"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>

                    <!-- Lieu -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lieu
                        </label>
                        <input type="text" name="lieu"
                               value="<?= htmlspecialchars($lieu) ?>"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <!-- Boutons -->
                    <div class="flex gap-3">
                        <button type="submit" name="update_match" value="Modifier"
                                class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            Modifier
                        </button>
                        <button type="submit" name="reset" value="Annuler"
                                class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>

</body>

</html>