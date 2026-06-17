<?php
require_once __DIR__ . '/../../../middleware/Role.php';
require_once __DIR__ . '/../../../middleware/Admin.php';
requireAdmin();

require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/utilisateur/User.php';

use Config\Database;
use Models\Utilisateur\User;

$database = new Database();
$pdo = $database->connect();
$userModel = new User($pdo);

// Paramètres pagination
$limit = 5;
$pageDemandes = isset($_GET['page_d']) ? max(1, (int)$_GET['page_d']) : 1;
$pageUsers = isset($_GET['page_u']) ? max(1, (int)$_GET['page_u']) : 1;

// Données brutes
$allDemandes = $userModel->getPendingUsers();
$allUsers = $userModel->readAll();

// Calcul du nombre de pages
$nbPagesDemandes = max(1, ceil(count($allDemandes) / $limit));
$nbPagesUsers = max(1, ceil(count($allUsers) / $limit));

// Ajustement sécurité si page out of bounds
if ($pageDemandes > $nbPagesDemandes) $pageDemandes = $nbPagesDemandes;
if ($pageUsers > $nbPagesUsers) $pageUsers = $nbPagesUsers;

// Slice des tableaux pour affichage
$demandes = array_slice($allDemandes, ($pageDemandes - 1) * $limit, $limit);
$users = array_slice($allUsers, ($pageUsers - 1) * $limit, $limit);

$pageTitle = "Administration";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            /* Linear gradient sombre + image */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('/assets/images/welcome.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #1e293b;
        }
        .bg-white-glass {
            /* Glassmorphism appliqué */
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">Administration</h1>
                    <p class="text-slate-100">Gestion des membres et du club</p>
                </div>
                <a href="/page-admincreateuser" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-plus mr-2"></i>Créer utilisateur
                </a>
            </div>

            <div class="bg-white-glass rounded-2xl shadow-lg p-6 mb-8 text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-user-clock text-amber-400"></i> Demandes en attente (<?= count($allDemandes) ?>)
                </h2>
                <div class="space-y-4">
                    <?php if (empty($demandes)): ?>
                        <p class="text-slate-200 italic">Aucune demande en attente.</p>
                    <?php else: ?>
                        <?php foreach ($demandes as $d): ?>
                            <div class="border border-white/20 rounded-xl p-4 flex justify-between items-center bg-black/20">
                                <div>
                                    <p class="font-bold"><?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?></p>
                                    <p class="text-sm text-slate-200"><?= htmlspecialchars($d['email']) ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <form action="/admin-validateuser" method="POST" class="flex gap-2">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <button name="equipe_id" value="1" class="bg-green-600 text-white px-4 py-1 rounded-md text-sm font-semibold hover:bg-green-700">Valider A</button>
                                        <button name="equipe_id" value="2" class="bg-purple-600 text-white px-4 py-1 rounded-md text-sm font-semibold hover:bg-purple-700">Valider B</button>
                                    </form>
                                    <form action="/admin-rejetuser" method="POST">
                                        <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                        <button class="bg-red-500 text-white px-4 py-1 rounded-md text-sm font-semibold hover:bg-red-600">Refuser</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-6 flex gap-2">
                    <?php for($i=1; $i<=$nbPagesDemandes; $i++): ?>
                        <a href="?page_d=<?= $i ?>&page_u=<?= $pageUsers ?>" class="px-3 py-1 border rounded <?= $pageDemandes == $i ? 'bg-blue-600 text-white' : 'bg-white/20 hover:bg-white/40' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="bg-white-glass rounded-2xl shadow-lg overflow-hidden text-white">
                <table class="w-full text-left">
                    <thead class="bg-black/20 text-white">
                        <tr>
                            <th class="p-4">Nom</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Rôle</th>
                            <th class="p-4">Statut</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-t border-white/10">
                                <td class="p-4 font-semibold"><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></td>
                                <td class="p-4"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="p-4 capitalize"><?= htmlspecialchars($user['role']) ?></td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold <?= $user['statut'] === 'actif' ? 'bg-green-600 text-white' : 'bg-yellow-600 text-white' ?>">
                                        <?= htmlspecialchars($user['statut']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="/admin-edituser?id=<?= $user['id'] ?>" class="text-blue-300 hover:text-white mr-4"><i class="fas fa-edit"></i></a>
                                    <a href="/admin-deleteuser?id=<?= $user['id'] ?>" class="text-red-300 hover:text-white" onclick="return confirm('Confirmer la suppression ?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="p-4 border-t border-white/10 bg-black/10">
                    <?php for($i=1; $i<=$nbPagesUsers; $i++): ?>
                        <a href="?page_d=<?= $pageDemandes ?>&page_u=<?= $i ?>" class="px-3 py-1 border rounded <?= $pageUsers == $i ? 'bg-blue-600 text-white' : 'bg-white/20 hover:bg-white/40' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>