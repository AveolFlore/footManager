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
    <style>
        .cyber-bg {
            background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
            position: relative;
        }
        .cyber-bg::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(6, 182, 212, 0.04), rgba(0, 0, 0, 0), rgba(244, 63, 94, 0.04));
            z-index: 99999;
            opacity: 0.3;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
        .glow-cyan { box-shadow: 0 0 15px rgba(6, 182, 212, 0.15); }
        .glow-rose { box-shadow: 0 0 15px rgba(244, 63, 94, 0.15); }
    </style>
</head>

<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-4xl mx-auto">

                <a href="/page-match"
                   class="inline-flex items-center gap-2 text-cyan-500/60 hover:text-cyan-400 mb-8 font-mono text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    [ Annuler et retourner aux matchs ]
                </a>

                <?php if (isset($_GET['msg'])): ?>
                <div class="mb-8 px-6 py-4 rounded-none bg-rose-950/30 text-rose-400 font-mono text-xs uppercase tracking-wider border border-rose-500/30 shadow-[0_0_15px_rgba(244,63,94,0.15)] relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
                    <i class="fas fa-exclamation-circle mr-3 text-rose-500"></i>
                    CRITICAL_ERROR // <?= htmlspecialchars($_GET['msg']) ?>
                </div>
                <?php endif; ?>

                <div class="bg-slate-900/40 border border-slate-800/80 backdrop-blur-md p-8 max-w-2xl relative overflow-hidden">
                    <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t-2 border-l-2 border-cyan-500"></div>
                    <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t-2 border-r-2 border-cyan-500"></div>

                    <h1 class="text-2xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 mb-8 flex items-center gap-4 font-mono">
                        <i class="fas fa-edit text-cyan-400 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.5)]"></i>
                        Configuration_Match
                    </h1>

                    <form action="/matchSeance-update" method="POST" class="font-mono text-xs uppercase tracking-widest text-slate-400">

                        <input type="hidden" name="match_id" value="<?= $id ?>">

                        <div class="mb-6">
                            <label class="block text-slate-400 font-bold mb-2">
                                // Type_De_Seance
                            </label>
                            <select name="type"
                                    class="w-full bg-slate-950/60 border border-slate-800/80 rounded-none px-5 py-4 text-slate-200 outline-none focus:border-cyan-500 transition-colors">
                                <option value="match" <?= $type === 'match' ? 'selected' : '' ?> class="bg-slate-900 text-slate-200">Match Amical</option>
                                <option value="entr"  <?= $type === 'entr'  ? 'selected' : '' ?> class="bg-slate-900 text-slate-200">Entraînement</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-slate-400 font-bold mb-2">
                                // Index_Temporel_Local
                            </label>
                            <input type="datetime-local" name="date"
                                   value="<?= date('Y-m-d\TH:i', strtotime($date)) ?>"
                                   class="w-full bg-slate-950/60 border border-slate-800/80 rounded-none px-5 py-4 text-slate-200 outline-none focus:border-cyan-500 transition-colors"
                                   required>
                        </div>

                        <div class="mb-6">
                            <label class="block text-slate-400 font-bold mb-2">
                                // Coordonnees_Zone
                            </label>
                            <input type="text" name="lieu"
                                   value="<?= htmlspecialchars($lieu) ?>"
                                   class="w-full bg-slate-950/60 border border-slate-800/80 rounded-none px-5 py-4 text-slate-200 outline-none focus:border-cyan-500 transition-colors placeholder-slate-700"
                                   placeholder="Ex: TERRAIN_SYNTHÉTIQUE_A"
                                   required>
                        </div>

                        <div class="mb-8">
                            <label class="block text-slate-400 font-bold mb-2">
                                // Metadonnees_Logs
                            </label>
                            <textarea name="description" rows="4"
                                      class="w-full bg-slate-950/60 border border-slate-800/80 rounded-none px-5 py-4 text-slate-200 outline-none focus:border-cyan-500 transition-colors placeholder-slate-700"
                                      placeholder="Détails tactiques facultatifs..."><?= htmlspecialchars($description) ?></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-800/60">
                            <button type="submit" name="update_match" value="Modifier"
                                    class="flex-1 px-6 py-4 bg-cyan-950/40 border border-cyan-500 text-cyan-400 font-black hover:bg-cyan-500 hover:text-black transition-all glow-cyan text-center">
                                <i class="fas fa-save mr-2"></i> Sauvegarder
                            </button>
                            <button type="submit" name="reset" value="Annuler"
                                    class="flex-1 px-6 py-4 bg-slate-950/20 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 transition-all text-center">
                                Annuler_Modif
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </main>
    </div>

</body>

</html>