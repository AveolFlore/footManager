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
    <title>Galerie - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-8">
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Galerie Photos</h1>
                        <p class="text-gray-600">Souvenirs et moments forts du club</p>
                    </div>
                    <?php if ($isAdmin): ?>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                            <i class="fas fa-upload"></i>
                            <span>Ajouter une photo</span>
                        </button>
                    <?php endif; ?>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-6 p-4 rounded-lg <?= strpos($_GET['msg'], 'success') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (empty($photos)): ?>
                        <div class="col-span-full text-center py-20">
                            <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500 italic">Aucune photo dans la galerie pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition group relative">
                                <img src="/<?= htmlspecialchars($photo['image_url']) ?>" alt="<?= htmlspecialchars($photo['titre']) ?>" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-800 truncate"><?= htmlspecialchars($photo['titre']) ?></h3>
                                    <p class="text-xs text-gray-500 mb-2">
                                        <i class="fas fa-calendar-alt mr-1"></i> <?= date('d/m/Y', strtotime($photo['date_upload'])) ?>
                                        <?php if ($photo['lieu']): ?>
                                            | <i class="fas fa-map-marker-alt ml-1 mr-1"></i> <?= htmlspecialchars($photo['lieu']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($photo['description']) ?></p>
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
                            )" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition bg-red-600 text-white p-2 rounded-full hover:bg-red-700">
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

    <!-- Upload Modal -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Ajouter une photo</h2>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="/galerie-upload" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                    <input type="text" name="titre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Séance associée (optionnel)</label>
                    <select name="seance_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="">-- Choisir une séance --</option>
                        <?php foreach ($matches as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= date('d/m/Y H:i', strtotime($m['date'])) ?> - <?= htmlspecialchars($m['lieu']) ?> (<?= $m['type'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="photo" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                        Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>