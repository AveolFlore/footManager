<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Protection de la route : Seuls les utilisateurs connectés (Coach/Admin) accèdent au marquage
if (!isset($_SESSION['user'])) {
    header("Location: /login");
    exit;
}

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
if (in_array($roleUser, ['joueur', 'spectateur'])) {
    header("Location: /presence");
    exit;
}

$pageTitle = "Marquer les présences";
$currentUserId = $_SESSION['user']['id'] ?? null;

// Configuration des classes Tailwind dynamiques pour le JS
$themes = [
    'present' => ['active' => 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200', 'inactive' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 hover:from-green-200 hover:to-emerald-200'],
    'retard'  => ['active' => 'bg-gradient-to-r from-orange-600 to-yellow-600 text-white shadow-lg shadow-orange-200', 'inactive' => 'bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 hover:from-orange-200 hover:to-yellow-200'],
    'absent'  => ['active' => 'bg-gradient-to-r from-red-600 to-pink-600 text-white shadow-lg shadow-red-200', 'inactive' => 'bg-gradient-to-r from-red-100 to-pink-100 text-red-700 hover:from-red-200 hover:to-pink-200'],
    'excuse'  => ['active' => 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-200', 'inactive' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 hover:from-blue-200 hover:to-indigo-200']
];
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

        <main class="flex-1">
            <div class="p-6 md:p-8 lg:p-10">
                <a href="/presence" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold mb-8 text-lg">
                    <i class="fas fa-arrow-left mr-3 text-xl"></i>Retour à la liste
                </a>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-8 flex items-center gap-4">
                    <i class="fas fa-clipboard-check text-green-600 text-4xl"></i>Marquer les présences
                </h1>

                <div class="bg-white rounded-3xl shadow-xl p-6 md:p-8 border border-slate-100 mb-8">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-green-600"></i>
                        <?php
                            $dateObj = new DateTime($seance['date']);
                            $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                            $mois_fr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                            echo ucfirst($seance['type']) . ' - ' . $jours[$dateObj->format('w')] . ' ' . $dateObj->format('d') . ' ' . $mois_fr[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
                        ?>
                    </h2>
                    <p class="text-slate-600 text-lg">
                        <i class="fas fa-map-marker-alt mr-3 text-slate-400"></i><?= htmlspecialchars($seance['lieu']) ?>
                        <span class="mx-4 text-slate-300">•</span>
                        <i class="fas fa-clock mr-3 text-slate-400"></i><?= date('H:i', strtotime($seance['date'])) ?>
                    </p>
                </div>

                <form method="POST" action="/presence-marquer" class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 md:p-8">
                    <input type="hidden" name="seance_id" value="<?= $seance['id'] ?>">

                    <div class="mb-8 flex flex-wrap gap-4">
                        <button type="button" onclick="marquerTous('present')" class="px-6 py-3 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-2xl hover:from-green-200 hover:to-emerald-200 transition-all font-bold shadow-sm">
                            <i class="fas fa-check mr-3"></i>Tous présents
                        </button>
                        <button type="button" onclick="marquerTous('absent')" class="px-6 py-3 bg-gradient-to-r from-red-100 to-pink-100 text-red-700 rounded-2xl hover:from-red-200 hover:to-pink-200 transition-all font-bold shadow-sm">
                            <i class="fas fa-times mr-3"></i>Tous absents
                        </button>
                    </div>

                    <div class="space-y-5 mb-10">
                        <?php foreach ($joueurs as $joueur): 
                            $presenceExistante = $presencesMap[$joueur['id']] ?? null;
                            $typeSelectionne = $presenceExistante ? $presenceExistante['type_presence'] : 'present';
                            $noteExistante = $presenceExistante ? $presenceExistante['note'] : '';
                        ?>
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6 p-6 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border border-slate-100 hover:border-slate-200 transition-all shadow-sm">
                                <div class="flex items-center flex-1">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center text-white font-extrabold text-xl mr-5 shadow-lg shadow-green-200">
                                        <?= $joueur['numero_maillot'] ?? '?' ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-xl"><?= htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']) ?></p>
                                        <p class="text-slate-500 font-medium"><?= htmlspecialchars($joueur['poste'] ?? '-') ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 flex-wrap">
                                    <?php foreach ($themes as $key => $classes): ?>
                                        <label data-type="<?= $key ?>" data-active="<?= $classes['active'] ?>" data-inactive="<?= $classes['inactive'] ?>" 
                                            class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl cursor-pointer transition-all shadow-sm <?= $typeSelectionne === $key ? $classes['active'] : $classes['inactive'] ?>">
                                            <input type="radio" name="presences[<?= $joueur['id'] ?>][type]" value="<?= $key ?>" class="presence-radio hidden" <?= $typeSelectionne === $key ? 'checked' : '' ?> onchange="changerType(this)">
                                            <span class="font-bold">
                                                <i class="fas <?php echo $key==='present'?'fa-check':($key==='retard'?'fa-clock':($key==='absent'?'fa-times':'fa-sticky-note')) ?> mr-2"></i><?= ucfirst($key === 'excuse' ? 'excusé' : $key) ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <div class="w-full lg:w-72">
                                    <input type="text" name="presences[<?= $joueur['id'] ?>][note]" value="<?= htmlspecialchars($noteExistante) ?>" placeholder="Note (optionnelle)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex flex-wrap gap-4 justify-end pt-8 border-t border-slate-200">
                        <a href="/presence" class="px-8 py-3 border-2 border-slate-300 text-slate-700 rounded-2xl hover:bg-slate-50 transition-all font-bold text-lg">Annuler</a>
                        <button type="submit" class="px-10 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl transition-all font-bold text-lg shadow-lg shadow-green-200">
                            <i class="fas fa-save mr-3"></i>Enregistrer les présences
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function marquerTous(type) {
            document.querySelectorAll(`input[value="${type}"]`).forEach(radio => {
                radio.checked = true;
                changerType(radio);
            });
        }

        function changerType(radio) {
            const container = radio.closest('.flex.flex-wrap');
            const labels = container.querySelectorAll('label');

            labels.forEach(label => {
                const input = label.querySelector('input');
                const activeClasses = label.getAttribute('data-active').split(' ');
                const inactiveClasses = label.getAttribute('data-inactive').split(' ');

                if (input.checked) {
                    label.classList.remove(...inactiveClasses);
                    label.classList.add(...activeClasses);
                } else {
                    label.classList.remove(...activeClasses);
                    label.classList.add(...inactiveClasses);
                }
            });
        }
    </script>
</body>
</html>