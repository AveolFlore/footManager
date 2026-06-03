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
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');
        
        .cyber-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        }
        
        .holo-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(34, 211, 238, 0.25);
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.1);
        }
        
        .neon-text {
            text-shadow: 0 0 10px rgb(34 211 238),
                        0 0 20px rgb(34 211 238);
        }
        
        .btn-glow {
            position: relative;
            overflow: hidden;
        }
        
        .btn-glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 40%;
            height: 200%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.35), transparent);
            transform: skewX(-25deg);
            animation: scan 4s linear infinite;
        }
        
        @keyframes scan {
            0% { transform: translateX(-150%) skewX(-25deg); }
            100% { transform: translateX(400%) skewX(-25deg); }
        }
        
        .gallery-img {
            transition: all 0.4s ease;
        }
        
        .gallery-card:hover .gallery-img {
            transform: scale(1.08);
            filter: brightness(1.15) contrast(1.1);
        }
    </style>
</head>
<body class="cyber-bg text-slate-200 min-h-screen">

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10">
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text flex items-center gap-4 font-['Orbitron']">
                            <i class="fas fa-images text-cyan-400"></i>
                            GALERIE ARCHIVES
                        </h1>
                        <p class="text-slate-400 mt-2 text-lg">Moments capturés • Mémoire du club</p>
                    </div>
                    
                    <?php if ($isAdmin): ?>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                class="btn-glow flex items-center gap-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-cyan-500/50 transition-all">
                            <i class="fas fa-upload"></i>
                            AJOUTER UNE PHOTO
                        </button>
                    <?php endif; ?>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl flex items-center <?= strpos($_GET['msg'], 'success') !== false ? 'bg-cyan-500/10 border border-cyan-400/30 text-cyan-300' : 'bg-red-500/10 border border-red-400/30 text-red-300' ?>">
                        <i class="fas fa-info-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (empty($photos)): ?>
                        <div class="col-span-full holo-card rounded-3xl p-16 text-center">
                            <i class="fas fa-images text-slate-500 text-8xl mb-6"></i>
                            <p class="text-2xl text-slate-400">Aucune archive visuelle pour le moment</p>
                            <p class="text-slate-500 mt-3">Ajoutez vos premiers souvenirs du terrain.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="holo-card rounded-3xl overflow-hidden group gallery-card relative">
                                <div class="relative">
                                    <img src="/<?= htmlspecialchars($photo['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($photo['titre']) ?>" 
                                         class="gallery-img w-full h-56 object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all"></div>
                                </div>
                                
                                <div class="p-6">
                                    <h3 class="font-semibold text-lg text-white mb-2 line-clamp-2">
                                        <?= htmlspecialchars($photo['titre']) ?>
                                    </h3>
                                    <p class="text-sm text-slate-400 mb-3 flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-cyan-400"></i>
                                        <?= date('d/m/Y', strtotime($photo['date_upload'])) ?>
                                        <?php if ($photo['lieu']): ?>
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-map-marker-alt text-cyan-400"></i>
                                            <?= htmlspecialchars($photo['lieu']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-slate-300 line-clamp-3">
                                        <?= htmlspecialchars($photo['description'] ?? 'Aucune description') ?>
                                    </p>
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
                                        class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all bg-red-600/90 hover:bg-red-700 text-white p-3 rounded-2xl">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal - Cyber Style -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
        <div class="holo-card rounded-3xl w-full max-w-2xl p-10">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold neon-text flex items-center gap-3">
                    <i class="fas fa-upload text-cyan-400"></i>
                    NOUVELLE ARCHIVE
                </h2>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                        class="text-slate-400 hover:text-red-400 text-3xl transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="/galerie-upload" method="POST" enctype="multipart/form-data" class="space-y-8">
                <div>
                    <label class="block text-sm uppercase tracking-widest text-cyan-400 mb-3">Titre de la photo</label>
                    <input type="text" name="titre" required 
                           class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-6 py-5 text-white focus:border-cyan-400 focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm uppercase tracking-widest text-cyan-400 mb-3">Séance associée (optionnel)</label>
                    <select name="seance_id" 
                            class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-6 py-5 text-white focus:border-cyan-400 focus:outline-none">
                        <option value="">-- Aucune séance --</option>
                        <?php foreach ($matches as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= date('d/m/Y H:i', strtotime($m['date'])) ?> - <?= htmlspecialchars($m['lieu']) ?> (<?= $m['type'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm uppercase tracking-widest text-cyan-400 mb-3">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-6 py-5 text-white focus:border-cyan-400 focus:outline-none"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm uppercase tracking-widest text-cyan-400 mb-3">Fichier Image</label>
                    <input type="file" name="photo" accept="image/*" required 
                           class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-6 py-5 text-slate-300 file:mr-4 file:py-3 file:px-8 file:rounded-2xl file:border-0 file:bg-cyan-500/10 file:text-cyan-400">
                </div>
                
                <div class="flex gap-4 pt-6 border-t border-slate-700">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                            class="flex-1 py-4 border border-slate-600 hover:border-slate-400 text-slate-300 rounded-2xl font-medium transition-all">
                        ANNULER
                    </button>
                    <button type="submit" 
                            class="flex-1 btn-glow py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-2xl">
                        <i class="fas fa-upload mr-2"></i>
                        TÉLÉCHARGER
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>