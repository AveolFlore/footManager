<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/match_seance/MatchEntity.php';

use Config\Database;
use Models\Match_seance\MatchEntity;

$db = (new Database())->connect();
$matchModel = new MatchEntity($db);
$events = $matchModel->readAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matchs & Séances - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Matchs & Séances</h1>
                <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    Nouvel événement
                </button>
            </header>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Lieu</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($events as $e): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800"><?= date('d/m/Y', strtotime($e['date'])) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold <?= $e['type'] === 'match' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' ?>">
                                        <?= strtoupper($e['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600"><?= $e['lieu'] ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium <?= $e['statut'] === 'termine' ? 'text-gray-400' : 'text-green-600' ?>">
                                        <?= ucfirst($e['statut']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/page-detail?id=<?= $e['id'] ?>" class="text-green-600 hover:underline font-medium">Détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Ajouter -->
    <div id="modal-add" class="fixed inset-0 bg-black/50 z-50 flex justify-center items-center hidden">
        <div class="bg-white w-full max-w-lg p-8 rounded-xl">
            <h2 class="text-2xl font-bold mb-6">Planifier une séance</h2>
            <form action="/match-creer" method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full p-2 border rounded-lg outline-none">
                            <option value="match">Match</option>
                            <option value="entr">Entraînement</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" required class="w-full p-2 border rounded-lg outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
                    <input type="text" name="lieu" required class="w-full p-2 border rounded-lg outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full p-2 border rounded-lg outline-none"></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="px-4 py-2 text-gray-500">Annuler</button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold">Créer</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
