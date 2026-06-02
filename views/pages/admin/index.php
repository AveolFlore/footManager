<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');

// DB + MODEL
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Utilisateur/User.php';

use Config\Database;
use Models\Utilisateur\User;

// connexion
$database = new Database();
$pdo = $database->connect();

// model
$userModel = new User($pdo);

// récupérer les utilisateurs
$users = $userModel->readAll();

// compter les demandes en attente
$total_attente = $userModel->countPending();

// récupérer les demandes en attente
$demandes = $userModel->getPendingUsers();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin - Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <!-- CONTENU -->
        <main class="flex-1 p-6 overflow-y-auto">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Administration</h1>
                    <p class="text-sm text-gray-500">Gestion des membres et du club</p>
                </div>

                <a href="/page-admincreateuser"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    + Créer un utilisateur
                </a>
            </div>

            <!-- TABS -->
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">

                <div class="flex">

                    <!-- Validation -->
                    <button class="flex-1 px-4 py-3 bg-green-600 text-white flex items-center justify-center gap-2">
                        Validation Joueurs
                        <span class="bg-white text-green-600 rounded-full px-2 text-xs">
                            <?= $total_attente ?>
                        </span>
                    </button>

                    <!-- Paramètres -->
                    <button class="flex-1 px-4 py-3 text-gray-600 hover:bg-gray-100">
                        Paramètres Club
                    </button>

                </div>

                <!-- DEMANDES -->
                <div class="p-5">

                    <h3 class="text-lg font-medium mb-4">Demandes en attente</h3>

                    <div class="space-y-4">

                        <?php foreach ($demandes as $d): ?>
                            <div class="border border-orange-300 rounded-xl p-4 flex justify-between items-center">

                                <div>
                                    <p class="font-semibold">
                                        <?= $d['nom'] . ' ' . $d['prenom'] ?>
                                    </p>

                                    <p class="text-sm text-gray-500"><?= $d['email'] ?></p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Demande récente
                                    </p>

                                    <div class="mt-3 flex gap-2">

                                        <!-- ÉQUIPE A -->
                                        <form action="/admin-validateuser" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                            <input type="hidden" name="equipe_id" value="1">

                                            <button class="bg-blue-600 text-white px-3 py-1 rounded">
                                                Équipe A
                                            </button>
                                        </form>

                                        <!-- ÉQUIPE B -->
                                        <form action="/admin-validateuser" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                            <input type="hidden" name="equipe_id" value="2">

                                            <button class="bg-purple-600 text-white px-3 py-1 rounded">
                                                Équipe B
                                            </button>
                                        </form>

                                    </div>

                                </div>

                                <div class="flex flex-col items-end gap-3">

                                    <span class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full">
                                        En attente
                                    </span>

                                    <!-- REFUSER -->
                                    <form action="/admin-rejetuser" method="POST">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">

                                        <button class="text-red-600 text-sm">
                                            Refuser
                                        </button>
                                    </form>

                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>

                </div>
            </div>

            <!-- LISTE UTILISATEURS -->
            <div class="bg-white rounded-xl shadow overflow-hidden">

                <div class="p-4 border-b">
                    <h3 class="text-lg font-medium">Tous les utilisateurs</h3>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="p-3">Nom</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Rôle</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">

                                <td class="p-3">
                                    <?= $user['nom'] . ' ' . $user['prenom'] ?>
                                </td>

                                <td class="p-3"><?= $user['email'] ?></td>

                                <td class="p-3">
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">
                                        <?= $user['role'] ?>
                                    </span>
                                </td>

                                <td class="p-3">
                                    <span class="px-2 py-1 text-sm rounded 
                        <?= $user['statut'] === 'actif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= $user['statut'] ?>
                                    </span>
                                </td>

                                <td class="p-3 space-x-2">
                                    <a href="/admin-edituser?id=<?= $user['id'] ?>" class="text-blue-600 text-sm">
                                        Modifier
                                    </a>
                                    <a href="/admin-deleteuser?id=<?= $user['id'] ?>" class="text-red-600 text-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        Supprimer
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div>

        </main>
    </div>

</body>

</html>