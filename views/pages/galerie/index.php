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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-6 md:p-8 lg:p-10">
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800">Galerie Photos</h1>
                        <p class="text-slate-600 mt-2">Souvenirs et moments forts du club</p>
                    </div>
                    <?php if ($isAdmin): ?>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="flex items-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-200">
                            <i class="fas fa-upload"></i>
                            Ajouter une photo
                        </button>
                    <?php endif; ?>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl font-semibold <?= strpos($_GET['msg'], 'success') !== false ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200' : 'bg-gradient-to-r from-red-50 to-pink-50 text-red-700 border border-red-200' ?>">
                        <i class="fas fa-info-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (empty($photos)): ?>
                        <div class="col-span-full text-center py-20">
                            <i class="fas fa-images text-slate-300 text-8xl mb-6"></i>
                            <p class="text-slate-500 italic text-xl">Aucune photo dans la galerie pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-100 hover:shadow-2xl transition-all duration-300 group relative">
                                <img src="/<?= htmlspecialchars($photo['image_url']) ?>" alt="<?= htmlspecialchars($photo['titre']) ?>" class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="p-6">
                                    <h3 class="font-bold text-xl text-slate-800 mb-2 truncate"><?= htmlspecialchars($photo['titre']) ?></h3>
                                    <p class="text-sm text-slate-500 mb-3">
                                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i><?= date('d/m/Y', strtotime($photo['date_upload'])) ?>
                                        <?php if ($photo['lieu']): ?>
                                            <span class="mx-2 text-slate-300">•</span><i class="fas fa-map-marker-alt mr-1 text-purple-600"></i><?= htmlspecialchars($photo['lieu']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($photo['description']) ?></p>
                                </div>
                                <?php if ($isAdmin): ?>
                                    <button onclick="openConfirmModal(
                                        'Supprimer la photo ?',
                                        'Êtes-vous sûr de vouloir supprimer cette photo ?',
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
                                    )" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all bg-gradient-to-r from-red-600 to-pink-600 text-white p-3 rounded-2xl hover:shadow-lg hover:from-red-700 hover:to-pink-700">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-2xl p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <i class="fas fa-plus-circle text-green-600"></i>
                    Ajouter une photo
                </h2>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-3xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="/galerie-upload" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Titre</label>
                    <input type="text" name="titre" required class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Séance associée (optionnel)</label>
                    <select name="seance_id" class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                        <option value="">-- Choisir une séance --</option>
                        <?php foreach ($matches as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= date('d/m/Y H:i', strtotime($m['date'])) ?> - <?= htmlspecialchars($m['lieu']) ?> (<?= $m['type'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full border-2 border-slate-200 rounded-2xl px-5 py-4 text-base focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Image</label>
                    <input type="file" name="photo" accept="image/*" required class="w-full text-sm text-slate-600 file:mr-4 file:py-3 file:px-6 file:rounded-2xl file:border-0 file:text-sm file:font-bold file:bg-gradient-to-r file:from-green-50 file:to-emerald-50 file:text-green-700 hover:file:from-green-100 hover:file:to-emerald-100 transition-all">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="flex-1 px-6 py-4 border-2 border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all bg-white">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white font-extrabold py-4 rounded-2xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                        <i class="fas fa-upload mr-2"></i>
                        Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
