<?php
// Initialisation
$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$pageTitle = "Marquer les Présences - " . htmlspecialchars($seance['lieu'] ?? 'Séance');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('/assets/images/Presence.jpg') no-repeat center center fixed;
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
<body class="min-h-screen text-white">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>

    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-10 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="/presence" class="text-blue-400 hover:text-blue-300 text-sm inline-flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-left"></i> Retour aux présences
                </a>
            </div>

            <!-- Séance Info -->
            <div class="glass-panel rounded-3xl p-6 mb-8 border border-white/10">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold">
                            <i class="fas fa-clipboard-list mr-3 text-blue-400"></i>
                            Feuille de Présence
                        </h1>
                        <p class="text-slate-300 mt-2 text-lg">
                            <i class="fas fa-calendar mr-2"></i><?= date('d/m/Y à H:i', strtotime($seance['date'])) ?>
                        </p>
                        <p class="text-slate-400">
                            <i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($seance['lieu']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="glass-panel rounded-3xl p-6 mb-8 border border-white/10">
                <h2 class="text-lg font-bold mb-4 text-slate-200">Actions Rapides</h2>
                <div class="flex flex-wrap gap-3">
                    <button onclick="marquerTous('present')" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-check mr-2"></i>Tous Présents
                    </button>
                    <button onclick="marquerTous('absent')" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-times mr-2"></i>Tous Absents
                    </button>
                    <button onclick="marquerTous('retard')" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-clock mr-2"></i>Tous en Retard
                    </button>
                </div>
            </div>

            <!-- Formulaire -->
            <form action="/presence-marquer" method="POST" class="space-y-6">
                <input type="hidden" name="seance_id" value="<?= $seance['id'] ?>">

                <!-- Liste des joueurs -->
                <div class="glass-panel rounded-3xl overflow-hidden border border-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-black/20 text-slate-300">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Joueur</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Note</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-white">
                                <?php if (!empty($joueurs)): foreach ($joueurs as $joueur): 
                                    $presenceExistante = $presencesMap[$joueur['id']] ?? null;
                                    $typeSelectionne = $presenceExistante['type_presence'] ?? 'present';
                                ?>
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-600/30 flex items-center justify-center font-bold text-blue-300 border border-blue-500/30">
                                                <?= $joueur['numero_maillot'] ?? '?' ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-white"><?= htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']) ?></p>
                                                <p class="text-xs text-slate-400"><?= htmlspecialchars($joueur['poste'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap justify-center gap-2">
                                            <!-- Présent -->
                                            <label data-type="present"
                                                   data-active="bg-green-600 text-white border-green-500"
                                                   data-inactive="bg-green-950/40 text-green-400 border border-green-800/50 hover:bg-green-900/40 cursor-pointer"
                                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="present" class="presence-radio sr-only" <?= $typeSelectionne === 'present' ? 'checked' : '' ?> onchange="changerType(this)">
                                                <i class="fas fa-check mr-1"></i>Présent
                                            </label>

                                            <!-- Retard -->
                                            <label data-type="retard"
                                                   data-active="bg-orange-600 text-white border-orange-500"
                                                   data-inactive="bg-orange-950/40 text-orange-400 border border-orange-800/50 hover:bg-orange-900/40 cursor-pointer"
                                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="retard" class="presence-radio sr-only" <?= $typeSelectionne === 'retard' ? 'checked' : '' ?> onchange="changerType(this)">
                                                <i class="fas fa-clock mr-1"></i>Retard
                                            </label>

                                            <!-- Absent -->
                                            <label data-type="absent"
                                                   data-active="bg-red-600 text-white border-red-500"
                                                   data-inactive="bg-red-950/40 text-red-400 border border-red-800/50 hover:bg-red-900/40 cursor-pointer"
                                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="absent" class="presence-radio sr-only" <?= $typeSelectionne === 'absent' ? 'checked' : '' ?> onchange="changerType(this)">
                                                <i class="fas fa-times mr-1"></i>Absent
                                            </label>

                                            <!-- Excusé -->
                                            <label data-type="excuse"
                                                   data-active="bg-blue-600 text-white border-blue-500"
                                                   data-inactive="bg-blue-950/40 text-blue-400 border border-blue-800/50 hover:bg-blue-900/40 cursor-pointer"
                                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="excuse" class="presence-radio sr-only" <?= $typeSelectionne === 'excuse' ? 'checked' : '' ?> onchange="changerType(this)">
                                                <i class="fas fa-info-circle mr-1"></i>Excusé
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="presences[<?= $joueur['id'] ?>][note]"
                                               value="<?= htmlspecialchars($presenceExistante['note'] ?? '') ?>"
                                               placeholder="Note (optionnelle)"
                                               class="w-full max-w-xs bg-black/30 border border-white/10 rounded-xl px-4 py-2 text-white text-sm outline-none focus:border-blue-400">
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">Aucun joueur disponible.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bouton de validation -->
                <div class="flex justify-end">
                    <button type="submit" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-extrabold text-lg rounded-2xl shadow-xl transition transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Enregistrer les Présences
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // --- Gestion du Dark Mode ---
        function appliquerTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            // Rafraîchir les labels de présence
            document.querySelectorAll('.presence-radio').forEach(radio => {
                if (!radio.checked) changerType(radio);
            });
        }

        appliquerTheme();

        // --- Logique d'interaction des Présences ---
        function marquerTous(type) {
            document.querySelectorAll(`input[value="${type}"]`).forEach(radio => {
                radio.checked = true;
                changerType(radio);
            });
        }

        function changerType(radio) {
            const container = radio.closest('.flex.flex-wrap');
            const labels = container.querySelectorAll('label');
            const isDark = document.documentElement.classList.contains('dark');

            labels.forEach(label => {
                const input = label.querySelector('input');
                const type = label.getAttribute('data-type');
                
                const activeClasses = label.getAttribute('data-active').split(' ');
                let inactiveClasses = label.getAttribute('data-inactive').split(' ');

                if (isDark) {
                    const darkInactiveThemes = {
                        'present': ['bg-green-950/40', 'text-green-400', 'border', 'border-green-800/50', 'hover:bg-green-900/40'],
                        'retard':  ['bg-orange-950/40', 'text-orange-400', 'border', 'border-orange-800/50', 'hover:bg-orange-900/40'],
                        'absent':  ['bg-red-950/40', 'text-red-400', 'border', 'border-red-800/50', 'hover:bg-red-900/40'],
                        'excuse':  ['bg-blue-950/40', 'text-blue-400', 'border', 'border-blue-800/50', 'hover:bg-blue-900/40']
                    };
                    inactiveClasses = darkInactiveThemes[type];
                }

                const allPossibleClasses = [
                    ...activeClasses, 
                    ...label.getAttribute('data-inactive').split(' '),
                    'bg-green-950/40', 'text-green-400', 'border-green-800/50', 'hover:bg-green-900/40',
                    'bg-orange-950/40', 'text-orange-400', 'border-orange-800/50', 'hover:bg-orange-900/40',
                    'bg-red-950/40', 'text-red-400', 'border-red-800/50', 'hover:bg-red-900/40',
                    'bg-blue-950/40', 'text-blue-400', 'border-blue-800/50', 'hover:bg-blue-900/40',
                    'border'
                ];
                label.classList.remove(...allPossibleClasses);

                if (input.checked) {
                    label.classList.add(...activeClasses);
                } else {
                    label.classList.add(...inactiveClasses);
                }
            });
        }

        // Initialiser les états
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.presence-radio').forEach(radio => {
                changerType(radio);
            });
        });
    </script>
</body>
</html>
