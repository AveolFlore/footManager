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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
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
            opacity: 0.4;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
        .btn-glow-cyan {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);
            transition: all 0.3s ease;
        }
        .btn-glow-cyan:hover {
            box-shadow: 0 0 25px rgba(6, 182, 212, 0.5);
        }
        /* Style custom pour les inputs natifs sombres */
        input[-webkit-calendar-picker-indicator] {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>

<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">

                <a href="/page-match"
                    class="inline-flex items-center gap-2 text-cyan-500/60 hover:text-cyan-400 mb-8 font-mono text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    [ Retour aux matchs ]
                </a>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-none font-mono text-sm uppercase tracking-wider bg-rose-950/40 border-l-4 border-rose-500 border-t border-b border-r border-rose-500/20 text-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.1)]">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        SYSTEM_ERROR : <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-slate-900/40 border border-slate-800/80 backdrop-blur-md p-8 max-w-2xl relative">
                    <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t-2 border-l-2 border-cyan-500"></div>
                    <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t-2 border-r-2 border-cyan-500"></div>
                    <div class="absolute -bottom-[1px] -left-[1px] w-3 h-3 border-b-2 border-l-2 border-cyan-500/40"></div>
                    <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b-2 border-r-2 border-cyan-500/40"></div>

                    <h1 class="text-2xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-cyan-400 flex items-center gap-4 mb-8 font-mono">
                        <i class="fas fa-plus-circle text-cyan-500 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.6)]"></i>
                        INITIALISATION_SÉANCE
                    </h1>

                    <form action="/matchSeance-create" method="POST" class="space-y-6 font-mono text-sm">

                        <div>
                            <label class="block text-xs font-bold text-cyan-400 mb-2 uppercase tracking-widest">
                                Type de séance
                            </label>
                            <select name="type"
                                class="w-full bg-slate-950 border border-slate-800 text-slate-300 rounded-none px-5 py-4 outline-none focus:border-cyan-500 font-bold uppercase tracking-wider">
                                <option value="match">Match (BL-SÉLECTION)</option>
                                <option value="entr">Entraînement (BL-TRAINING)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-cyan-400 mb-2 uppercase tracking-widest">
                                Horloge système (Date et heure)
                            </label>
                            <input type="datetime-local" name="date"
                                class="w-full bg-slate-950 border border-slate-800 text-slate-300 rounded-none px-5 py-4 outline-none focus:border-cyan-500 font-bold tracking-wide"
                                id="match-date"
                                required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-cyan-400 mb-2 uppercase tracking-widest">
                                Zone de déploiement (Lieu)
                            </label>
                            <input type="text" name="lieu"
                                placeholder="EX: STADE MUNICIPAL / LABORATOIRE"
                                class="w-full bg-slate-950 border border-slate-800 text-slate-300 rounded-none px-5 py-4 outline-none focus:border-cyan-500 placeholder-slate-700 font-bold uppercase tracking-wide"
                                required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-cyan-400 mb-2 uppercase tracking-widest">
                                Paramètres additionnels (Description)
                            </label>
                            <textarea name="description" rows="4"
                                placeholder="DIRECTIVES COMPLÉMENTAIRES DE L'ÉGOCENTRISME..."
                                class="w-full bg-slate-950 border border-slate-800 text-slate-300 rounded-none px-5 py-4 outline-none focus:border-cyan-500 placeholder-slate-700 tracking-wide"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4 text-xs font-black uppercase tracking-widest">
                            <button type="submit" name="add_match" value="Créer"
                                class="flex-1 group relative px-8 py-4 bg-cyan-950/60 border border-cyan-500 text-cyan-400 hover:bg-cyan-500 hover:text-black btn-glow-cyan text-center transition-all duration-300">
                                <span class="absolute top-0 left-0 w-2 h-2 border-t-2 border-l-2 border-cyan-400 group-hover:border-black"></span>
                                <span class="absolute bottom-0 right-0 w-2 h-2 border-b-2 border-r-2 border-cyan-400 group-hover:border-black"></span>
                                <i class="fas fa-check mr-2"></i> Générer la matrice
                            </button>
                            <a href="/page-match"
                                class="flex-1 px-8 py-4 bg-slate-950 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 text-center transition-all flex items-center justify-center">
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