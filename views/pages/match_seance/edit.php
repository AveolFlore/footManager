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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></link>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/assets/images/impact.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="text-white min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-8 lg:p-10 max-w-2xl mx-auto">

            <div class="mb-6">
                <a href="/page-match" class="text-blue-400 hover:text-blue-300 text-sm inline-flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-left"></i> Annuler et retourner aux matchs
                </a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 bg-red-900/50 border border-red-500 text-white rounded-xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    <div>
                        <strong class="font-semibold">Erreur :</strong>
                        <span><?= htmlspecialchars($_GET['msg']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="glass-panel p-6 md:p-8 rounded-2xl">
                <h1 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <i class="fas fa-edit text-blue-400 text-3xl"></i>
                    Modifier la séance
                </h1>

                <form action="/matchSeance-update" method="POST" class="space-y-5">

                    <input type="hidden" name="match_id" value="<?= $id ?>">

                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-tasks mr-2 text-blue-400"></i>Type de séance
                        </label>
                        <select name="type" class="w-full bg-white/10 border border-white/20 text-white rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none backdrop-blur-sm transition-all">
                            <option value="match" <?= $type === 'match' ? 'selected' : '' ?> class="bg-slate-800">Match Amical</option>
                            <option value="entr"  <?= $type === 'entr'  ? 'selected' : '' ?> class="bg-slate-800">Entraînement</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-blue-400"></i>Date et heure
                        </label>
                        <input type="datetime-local" name="date"
                               value="<?= date('Y-m-d\TH:i', strtotime($date)) ?>"
                               class="w-full bg-white/10 border border-white/20 text-white rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none backdrop-blur-sm transition-all"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>Lieu
                        </label>
                        <input type="text" name="lieu"
                               value="<?= htmlspecialchars($lieu) ?>"
                               class="w-full bg-white/10 border border-white/20 text-white placeholder-white/50 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none backdrop-blur-sm transition-all"
                               placeholder="Ex : Stade Municipal"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-align-left mr-2 text-blue-400"></i>Description / Informations complémentaires
                        </label>
                        <textarea name="description" rows="4"
                                  class="w-full bg-white/10 border border-white/20 text-white placeholder-white/50 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none backdrop-blur-sm transition-all resize-none"
                                  placeholder="Détails de la séance..."><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <button type="submit" name="update_match" value="Modifier"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Sauvegarder
                        </button>
                        <button type="submit" name="reset" value="Annuler"
                                class="flex-1 bg-white/10 hover:bg-white/20 text-white font-semibold py-3 rounded-xl transition-all border border-white/20">
                            Annuler les modifications
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>

</html>
