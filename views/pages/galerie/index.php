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
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen font-sans">

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 bg-slate-50 overflow-y-auto">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">
                
                <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 border border-slate-200 rounded-xl shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-images text-slate-500"></i>
                            <span>Galerie Photos</span>
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">Moments capturés • Mémoire du club</p>
                    </div>
                    
                    <?php if ($isAdmin): ?>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-upload text-xs"></i>
                            <span>Ajouter une photo</span>
                        </button>
                    <?php endif; ?>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="px-4 py-3 border rounded-lg text-sm font-medium shadow-sm flex items-center <?= strpos($_GET['msg'], 'success') !== false ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
                        <i class="fas fa-info-circle mr-3 <?= strpos($_GET['msg'], 'success') !== false ? 'text-emerald-500' : 'text-red-500' ?>"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($photos)): ?>
                    <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-sm">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4 text-slate-400">
                            <i class="fas fa-images text-2xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-slate-900">Aucune archive visuelle pour le moment</p>
                        <p class="text-sm text-slate-500 mt-1">Ajoutez vos premiers souvenirs du terrain.</p>
                    </div>
                <?php ?>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($photos as $photo): ?>
                            <div class="group bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm relative flex flex-col hover:shadow-md transition-all duration-200">
                                
                                <div class="relative overflow-hidden bg-slate-100 aspect-video sm:h-48">
                                    <img src="/<?= htmlspecialchars($photo['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($photo['titre']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base mb-1 line-clamp-1">
                                            <?= htmlspecialchars($photo['titre']) ?>
                                        </h3>
                                        
                                        <div class="flex items-center flex-wrap gap-x-3 gap-y-1 text-xs text-slate-400 mb-3">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-calendar-alt"></i>
                                                <?= date('d/m/Y', strtotime($photo['date_upload'])) ?>
                                            </span>
                                            <?php if ($photo['lieu']): ?>
                                                <span class="flex items-center gap-1 truncate max-w-[120px]">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?= htmlspecialchars($photo['lieu']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="text-xs text-slate-600 line-clamp-2">
                                            <?= htmlspecialchars($photo['description'] ?? 'Aucune description') ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if ($isAdmin): ?>
                                    <button onclick="openConfirmModal(
                                        'Supprimer cette archive ?',
                                        'Cette action est irréversible.',
                                        () => {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = '/galerie-delete';
                                            const input = document.createElement('input');
                                            input.type = 'hidden';
                                            input.name = 'id';
                                            input.value = '<?= $photo['id'] ?>';
                                            form.appendChild(input);
                                            document.body.appendChild(form);
                                            form.submit();
                                        }
                                    )" 
                                        class="absolute top-3 right-3 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 p-2 rounded-lg shadow-sm"
                                        title="Supprimer la photo">
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

    <div id="uploadModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-lg p-6 shadow-xl space-y-6">
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-upload text-slate-500"></i>
                    <span>Nouvelle archive</span>
                </h2>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <form action="/galerie-upload" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Titre de la photo</label>
                    <input type="text" name="titre" required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:bg-white focus:outline-none transition-colors">
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Séance associée (optionnel)</label>
                    <select name="seance_id" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:bg-white focus:outline-none transition-colors">
                        <option value="">-- Aucune séance --</option>
                        <?php foreach ($matches as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= date('d/m/Y H:i', strtotime($m['date'])) ?> - <?= htmlspecialchars($m['lieu']) ?> (<?= ucfirst($m['type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:bg-white focus:outline-none transition-colors"></textarea>
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fichier Image</label>
                    <input type="file" name="photo" accept="image/*" required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row gap-2 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                            class="w-full sm:flex-1 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-sm font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="w-full sm:flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-upload text-xs"></i>
                        <span>Télécharger</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>