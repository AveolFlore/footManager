<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/galerie/Galerie.php';
require_once __DIR__ . '/../../../models/MatchSeance.php';

use Config\Database;
use Models\Galerie\Galerie;
use Models\MatchSeance;

requireLogin();

$db = (new Database())->connect();
$galerieModel = new Galerie($db);
$matchModel = new MatchSeance($db);

$photos = $galerieModel->getAll();
$matches = $matchModel->read();

$isAdmin = in_array($_SESSION['user']['role'], ['admin', 'president', 'organisateur']);
$pageTitle = "Galerie Photos";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .bg-galerie {
            background-image: url('/assets/images/Galerie.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-galerie min-h-screen text-slate-800">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">
                
                <!-- En-tête avec fond léger pour la lisibilité -->
                <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 p-6 border border-white/50 rounded-2xl shadow-lg">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                            <i class="fas fa-images text-blue-600"></i>
                            <span>Galerie Photos</span>
                        </h1>
                        <p class="text-sm font-bold text-slate-600 mt-1">Moments capturés • Mémoire du club</p>
                    </div>
                    
                    <?php if ($isAdmin): ?>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-black rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-upload text-xs"></i>
                            <span>Ajouter une photo</span>
                        </button>
                    <?php endif; ?>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="px-4 py-3 border rounded-xl text-sm font-bold shadow-sm flex items-center bg-white/80 border-white/50">
                        <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($photos)): ?>
                    <div class="bg-white/80 border border-white/50 rounded-2xl p-12 text-center shadow-lg">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-200/50 rounded-full mb-4 text-slate-500">
                            <i class="fas fa-images text-2xl"></i>
                        </div>
                        <p class="text-lg font-black text-slate-900">Aucune archive visuelle pour le moment</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($photos as $photo): ?>
                            <div class="group bg-white/80 border border-white/50 rounded-2xl overflow-hidden shadow-lg relative flex flex-col hover:shadow-2xl transition-all">
                                <div class="relative overflow-hidden bg-slate-200 aspect-video sm:h-48">
                                    <img src="/<?= htmlspecialchars($photo['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($photo['titre']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                
                                <div class="p-4 flex-1">
                                    <h3 class="font-black text-slate-900 text-base mb-1 line-clamp-1"><?= htmlspecialchars($photo['titre']) ?></h3>
                                    <div class="flex items-center gap-3 text-[10px] font-bold text-slate-500 mb-3 uppercase tracking-wider">
                                        <span><i class="fas fa-calendar-alt mr-1"></i><?= date('d/m/Y', strtotime($photo['date_upload'])) ?></span>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-2"><?= htmlspecialchars($photo['description'] ?? 'Aucune description') ?></p>
                                </div>

                                <?php if ($isAdmin): ?>
                                    <button onclick="if(confirm('Supprimer cette photo ?')) { window.location.href='/galerie-delete?id=<?= $photo['id'] ?>'; }" 
                                        class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal Upload -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-2xl">
            <h2 class="text-xl font-black mb-6">Nouvelle archive</h2>
            <form action="/galerie-upload" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="text" name="titre" placeholder="Titre" required class="w-full bg-slate-100 border-0 rounded-xl px-4 py-3">
                <textarea name="description" placeholder="Description" rows="3" class="w-full bg-slate-100 border-0 rounded-xl px-4 py-3"></textarea>
                <input type="file" name="photo" accept="image/*" required class="w-full text-sm">
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="flex-1 py-3 bg-slate-200 rounded-xl font-bold">Annuler</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-black">Publier</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>