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
            <h1 class="text-2xl font-semibold text-gray-800">
                Gestion des utilisateurs
            </h1>

            <a href="/page-admincreateuser"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                + Créer un utilisateur
            </a>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
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

                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">

                                <td class="p-3">
                                    <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                                </td>

                                <td class="p-3">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>

                                <td class="p-3">
                                    <span class="px-2 py-1 text-sm rounded bg-green-100 text-green-700">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>

                                <td class="p-3">
                                    <span class="px-2 py-1 text-sm rounded 
                                        <?= $user['statut'] === 'actif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= htmlspecialchars($user['statut']) ?>
                                    </span>
                                </td>

                                <td class="p-3 space-x-2">

                                    <a href="/admin-edit-user?id=<?= $user['id'] ?>"
                                       class="text-blue-600 hover:underline text-sm">
                                        Modifier
                                    </a>

                                    <a href="/admin-delete-user?id=<?= $user['id'] ?>"
                                       class="text-red-600 hover:underline text-sm"
                                       onclick="return confirm('Supprimer cet utilisateur ?')">
                                        Supprimer
                                    </a>

                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>