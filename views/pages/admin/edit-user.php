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

// Récupérer les équipes pour le select
$equipes = $pdo->query("SELECT * FROM equipes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
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
                    <h1 class="text-2xl font-semibold text-gray-800">Modifier Utilisateur</h1>
                    <p class="text-sm text-gray-500">Gestion des informations du membre</p>
                </div>
                <a href="/page-admin" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Retour
                </a>
            </div>

            <!-- FORMULAIRE -->
            <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
                <form action="/admin-updateuser" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom & Prénom</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" value="<?= htmlspecialchars($user['nom']) ?>" class="border rounded-lg px-3 py-2 bg-gray-50" disabled>
                                <input type="text" value="<?= htmlspecialchars($user['prenom']) ?>" class="border rounded-lg px-3 py-2 bg-gray-50" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border rounded-lg px-3 py-2 bg-gray-50" disabled>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                                <select name="role" class="w-full border rounded-lg px-3 py-2">
                                    <option value="joueur" <?= $user['role'] === 'joueur' ? 'selected' : '' ?>>Joueur</option>
                                    <option value="president" <?= $user['role'] === 'president' ? 'selected' : '' ?>>Président</option>
                                    <option value="entraineur" <?= $user['role'] === 'entraineur' ? 'selected' : '' ?>>Entraîneur</option>
                                    <option value="tresorier" <?= $user['role'] === 'tresorier' ? 'selected' : '' ?>>Trésorier</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <select name="statut" class="w-full border rounded-lg px-3 py-2">
                                    <option value="en_attente" <?= $user['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="valide" <?= $user['statut'] === 'valide' ? 'selected' : '' ?>>Validé</option>
                                    <option value="refuse" <?= $user['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Équipe</label>
                            <select name="equipe_id" class="w-full border rounded-lg px-3 py-2">
                                <option value="">Aucune équipe</option>
                                <?php foreach ($equipes as $e): ?>
                                    <option value="<?= $e['id'] ?>" <?= $user['equipe_id'] == $e['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <a href="/page-admin" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                                Annuler
                            </a>
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

</body>

</html>