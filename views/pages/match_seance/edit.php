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
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-6 md:p-8 lg:p-10 max-w-2xl mx-auto">

            <div class="mb-6">
                <a href="/page-match" class="text-blue-600 hover:underline text-sm inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Annuler et retourner aux matchs
                </a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <strong>Erreur :</strong> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-gray-600"></i> Modifier la séance
                </h1>

                <form action="/matchSeance-update" method="POST" class="space-y-4">

                    <input type="hidden" name="match_id" value="<?= $id ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type de séance
                        </label>
                        <select name="type" class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none">
                            <option value="match" <?= $type === 'match' ? 'selected' : '' ?>>Match Amical</option>
                            <option value="entr"  <?= $type === 'entr'  ? 'selected' : '' ?>>Entraînement</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date et heure
                        </label>
                        <input type="datetime-local" name="date"
                               value="<?= date('Y-m-d\TH:i', strtotime($date)) ?>"
                               class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lieu
                        </label>
                        <input type="text" name="lieu"
                               value="<?= htmlspecialchars($lieu) ?>"
                               class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none"
                               placeholder="Ex : Stade Municipal"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description / Informations complémentaires
                        </label>
                        <textarea name="description" rows="4"
                                  class="w-full bg-white border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 outline-none"
                                  placeholder="Détails de la séance..."><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <button type="submit" name="update_match" value="Modifier"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Sauvegarder
                        </button>
                        <button type="submit" name="reset" value="Annuler"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 rounded transition-colors text-center">
                            Annuler les modifications
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>
</body>

</html>