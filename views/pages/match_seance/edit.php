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

$pageTitle = "Modifier le match";
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
                   class="flex items-center gap-2 text-slate-500 hover:text-slate-700 mb-8 font-semibold">
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
                        <i class="fas fa-edit text-green-600"></i>
                        Modifier le match
                    </h1>

                    <form action="/matchSeance-update" method="POST">

                        <!-- Id caché -->
                        <input type="hidden" name="match_id" value="<?= $id ?>">

                        <!-- Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Type de séance
                            </label>
                            <select name="type"
                                    class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                                <option value="match" <?= $type === 'match' ? 'selected' : '' ?>>Match</option>
                                <option value="entr"  <?= $type === 'entr'  ? 'selected' : '' ?>>Entraînement</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Date et heure
                            </label>
                            <input type="datetime-local" name="date"
                                   value="<?= date('Y-m-d\TH:i', strtotime($date)) ?>"
                                   class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"
                                   required>
                        </div>

                        <!-- Lieu -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Lieu
                            </label>
                            <input type="text" name="lieu"
                                   value="<?= htmlspecialchars($lieu) ?>"
                                   class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"
                                   required>
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" rows="4"
                                      class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"><?= htmlspecialchars($description) ?></textarea>
                        </div>

                        <!-- Boutons -->
                        <div class="flex gap-4">
                            <button type="submit" name="update_match" value="Modifier"
                                    class="flex-1 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-lg font-extrabold rounded-2xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                                <i class="fas fa-save mr-2"></i>
                                Modifier
                            </button>
                            <button type="submit" name="reset" value="Annuler"
                                    class="flex-1 px-8 py-4 border-2 border-slate-200 text-slate-700 text-lg font-bold rounded-2xl hover:bg-slate-50 transition-all bg-white shadow-sm">
                                Annuler
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </main>
    </div>

</body>

</html>
