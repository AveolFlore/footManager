<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/match_seance/MatchEntity.php';
require_once __DIR__ . '/../../../models/utilisateur/User.php';
require_once __DIR__ . '/../../../models/presence/Presence.php';
require_once __DIR__ . '/../../../models/performance/Performance.php';

use Config\Database;
use Models\Match_seance\MatchEntity;
use Models\Utilisateur\User;
use Models\Presence\Presence;
use Models\Performance\Performance;

$db = (new Database())->connect();
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: /page-match");
    exit;
}

$matchModel = new MatchEntity($db);
$userModel = new User($db);
$presenceModel = new Presence($db);
$performanceModel = new Performance($db);

$match = $matchModel->getFindId($id);
$players = $userModel->readAll(); // Idéalement filter par validé
$presences = $presenceModel->getBySeance($id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Séance - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-8">
            <header class="mb-8">
                <a href="/page-match" class="text-green-600 hover:underline text-sm mb-2 inline-block">← Retour à la liste</a>
                <h1 class="text-3xl font-bold text-gray-800"><?= strtoupper($match['type']) ?> du <?= date('d/m/Y', strtotime($match['date'])) ?></h1>
                <p class="text-gray-500"><?= $match['lieu'] ?> - <?= $match['statut'] ?></p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Section: Présences -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-700 mb-6">Feuille de présence</h2>
                    <form action="/presence-marquer" method="POST" class="space-y-4">
                        <input type="hidden" name="seance_id" value="<?= $id ?>">
                        <div class="space-y-3">
                            <?php foreach ($players as $p): 
                                if ($p['statut'] !== 'valide') continue;
                                $alreadyMarked = false;
                                foreach($presences as $pr) if($pr['joueur_id'] == $p['id']) $alreadyMarked = $pr;
                            ?>
                                <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <img src="/<?= $p['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-8 h-8 rounded-full">
                                        <span class="text-sm font-medium"><?= $p['nom'] ?> <?= $p['prenom'] ?></span>
                                    </div>
                                    
                                    <?php if ($alreadyMarked): ?>
                                        <span class="text-xs font-bold uppercase px-2 py-1 rounded bg-gray-100 text-gray-500">
                                            <?= $alreadyMarked['type_presence'] ?>
                                        </span>
                                    <?php else: ?>
                                        <div class="flex space-x-1">
                                            <input type="radio" name="presences[<?= $p['id'] ?>][type]" value="present" id="p-<?= $p['id'] ?>" class="hidden peer/p" checked>
                                            <label for="p-<?= $p['id'] ?>" class="cursor-pointer px-2 py-1 border rounded text-xs hover:bg-green-50 peer-checked/p:bg-green-500 peer-checked/p:text-white transition">P</label>
                                            
                                            <input type="radio" name="presences[<?= $p['id'] ?>][type]" value="retard" id="r-<?= $p['id'] ?>" class="hidden peer/r">
                                            <label for="r-<?= $p['id'] ?>" class="cursor-pointer px-2 py-1 border rounded text-xs hover:bg-yellow-50 peer-checked/r:bg-yellow-500 peer-checked/r:text-white transition">R</label>
                                            
                                            <input type="radio" name="presences[<?= $p['id'] ?>][type]" value="absent" id="a-<?= $p['id'] ?>" class="hidden peer/a">
                                            <label for="a-<?= $p['id'] ?>" class="cursor-pointer px-2 py-1 border rounded text-xs hover:bg-red-50 peer-checked/a:bg-red-500 peer-checked/a:text-white transition">A</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!$alreadyMarked): ?>
                            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold mt-4">Enregistrer les présences</button>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Section: Performances (si match ou entraînement terminé) -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-700 mb-6">Performances</h2>
                    <form action="/performance-enregistrer" method="POST" class="space-y-4">
                        <input type="hidden" name="seance_id" value="<?= $id ?>">
                        <div class="space-y-3">
                            <?php foreach ($players as $p): 
                                if ($p['statut'] !== 'valide') continue;
                            ?>
                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                    <span class="text-sm font-medium"><?= $p['nom'] ?></span>
                                    <div class="flex space-x-2">
                                        <input type="number" name="perfs[<?= $p['id'] ?>][buts]" placeholder="Buts" class="w-16 p-1 border rounded text-xs outline-none">
                                        <input type="number" name="perfs[<?= $p['id'] ?>][passes]" placeholder="Passes" class="w-16 p-1 border rounded text-xs outline-none">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold mt-4">Saisir les stats</button>
                    </form>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
