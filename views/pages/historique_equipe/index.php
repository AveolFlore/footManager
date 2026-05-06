<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/equipe/Equipe.php';
require_once __DIR__ . '/../../../models/utilisateur/User.php';

use Config\Database;
use Models\Equipe\Equipe;
use Models\Utilisateur\User;

requireLogin();

$db = (new Database())->connect();
$equipeModel = new Equipe($db);
$userModel = new User($db);

$equipes = $equipeModel->getAll();
$selected_equipe_id = $_GET['equipe_id'] ?? ($equipes[0]['id'] ?? null);

$members = [];
if ($selected_equipe_id) {
    $members = $equipeModel->getMembers($selected_equipe_id);
}

$isAdmin = in_array($_SESSION['user']['role'], ['admin', 'president', 'entraineur']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Équipes - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 p-8">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestion des Équipes</h1>
                    <p class="text-gray-600">Consultez et gérez les membres de chaque équipe</p>
                </div>
            </header>

            <!-- Équipes Tabs -->
            <div class="flex space-x-4 mb-8 border-b border-gray-200">
                <?php foreach ($equipes as $equipe): ?>
                    <a href="/page-team?equipe_id=<?= $equipe['id'] ?>" 
                       class="pb-4 px-2 font-medium transition <?= $selected_equipe_id == $equipe['id'] ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' ?>">
                        <?= htmlspecialchars($equipe['nom']) ?>
                        <span class="ml-2 bg-gray-100 text-gray-600 text-xs py-0.5 px-2 rounded-full">
                            <?= count($equipeModel->getMembers($equipe['id'])) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Members Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php if (empty($members)): ?>
                    <div class="col-span-full text-center py-20 bg-white rounded-2xl shadow-sm">
                        <i class="fas fa-users-slash text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 italic">Aucun membre dans cette équipe.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($members as $member): ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <div class="h-24 bg-gradient-to-r from-green-400 to-blue-500"></div>
                            <div class="px-6 pb-6 -mt-12 text-center">
                                <img src="/<?= $member['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-24 h-24 rounded-full border-4 border-white mx-auto object-cover bg-gray-100 shadow-sm">
                                <h3 class="mt-4 font-bold text-gray-800"><?= htmlspecialchars($member['nom'] . ' ' . $member['prenom']) ?></h3>
                                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2"><?= htmlspecialchars($member['poste'] ?: 'Joueur') ?></p>
                                
                                <div class="flex justify-center space-x-2 text-gray-500 text-sm mb-4">
                                    <span title="Numéro"><i class="fas fa-tshirt mr-1"></i> <?= $member['numero_maillot'] ?: '-' ?></span>
                                    <span>|</span>
                                    <span title="Pied dominant"><i class="fas fa-shoe-prints mr-1"></i> <?= strtoupper($member['pied_dominant'] ?: '-') ?></span>
                                </div>

                                <div class="flex flex-col space-y-2">
                                    <a href="/users-profile?id=<?= $member['id'] ?>" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg transition font-medium">
                                        Voir Profil 360°
                                    </a>
                                    <?php if ($isAdmin): ?>
                                    <button onclick="openChangeTeamModal(<?= $member['id'] ?>, '<?= htmlspecialchars($member['nom'] . ' ' . $member['prenom']) ?>', <?= $member['equipe_id'] ?>)" 
                                            class="text-sm bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-lg transition font-medium">
                                        Transférer
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Change Team Modal -->
    <div id="changeTeamModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Transférer le joueur</h2>
                <button onclick="document.getElementById('changeTeamModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="/admin-changeteam" method="POST" class="space-y-4">
                <input type="hidden" name="user_id" id="modal_user_id">
                <p class="text-gray-600 text-sm mb-4">
                    Vous allez transférer <span id="modal_user_name" class="font-bold text-gray-800"></span> vers une nouvelle équipe.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle Équipe</label>
                    <select name="equipe_id" id="modal_equipe_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif (optionnel)</label>
                    <input type="text" name="motif" placeholder="ex: Rééquilibrage, Promotion..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                        Confirmer le transfert
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openChangeTeamModal(userId, userName, currentEquipeId) {
            document.getElementById('modal_user_id').value = userId;
            document.getElementById('modal_user_name').textContent = userName;
            document.getElementById('modal_equipe_id').value = currentEquipeId;
            document.getElementById('changeTeamModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
