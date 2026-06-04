<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');

// DB + MODEL
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/utilisateur/User.php';

use Config\Database;
use Models\Utilisateur\User;

// connexion
$database = new Database();
$pdo = $database->connect();

// model
$userModel = new User($pdo);

// Récupérer les équipes pour le select
$equipes = $pdo->query("SELECT * FROM equipes")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Modifier Utilisateur";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style mémorisé - Blue Lock Holographic & Cyber Tech */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes energyPulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.4),
                            0 0 40px rgba(59, 130, 246, 0.2);
            }
            50% {
                box-shadow: 0 0 40px rgba(59, 130, 246, 0.6),
                            0 0 80px rgba(59, 130, 246, 0.4);
            }
        }

        @keyframes holographic {
            0% { transform: translateX(-100%); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateX(200%); opacity: 0; }
        }

        @keyframes tacticalGlow {
            0%, 100% { border-color: rgba(34, 197, 94, 0.2); }
            50% { border-color: rgba(34, 197, 94, 0.6); }
        }

        .float-ball { animation: float 6s ease-in-out infinite; }
        .energy-pulse { animation: energyPulse 3s ease-in-out infinite; }
        .tactical-border { animation: tacticalGlow 4s ease-in-out infinite; }

        .holographic-scan::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.15), transparent);
            animation: holographic 4s linear infinite;
            pointer-events: none;
        }

        .glass-effect {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .btn-glow-green {
            background: linear-gradient(135deg, #16a34a, #15803d, #166534);
            background-size: 200% 200%;
            transition: all 0.4s ease;
        }

        .btn-glow-green:hover {
            background-position: 100% 100%;
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.5);
            transform: translateY(-2px);
        }

        .btn-glow-slate {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.3);
            transition: all 0.3s ease;
        }

        .btn-glow-slate:hover {
            background: rgba(51, 65, 85, 0.9);
            border-color: rgba(59, 130, 246, 0.5);
            color: #f8fafc;
            transform: translateY(-2px);
        }

        .input-glow:focus {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.35);
        }
    </style>
</head>

<body class="min-h-screen text-slate-100 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 bg-fixed">

    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-20 z-0">
        <div class="absolute bottom-20 right-10 float-ball" style="animation-delay: 1s;">
            <div class="w-24 h-24 bg-gradient-to-br from-white to-gray-400 rounded-full flex items-center justify-center energy-pulse">
                <i class="fas fa-futbol text-5xl text-slate-900"></i>
            </div>
        </div>
        <div class="absolute top-20 left-10 w-64 h-64">
            <div class="w-full h-full border-2 border-dashed tactical-border rounded-full flex items-center justify-center">
                <div class="w-1/2 h-1/2 border border-blue-500 rounded-full tactical-border"></div>
            </div>
        </div>
    </div>

    <div class="flex min-h-screen relative z-10">

        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-blue-500/20 pb-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent flex items-center gap-3">
                            <i class="fas fa-user-edit text-blue-400"></i>
                            Modifier Utilisateur
                        </h1>
                        <p class="text-blue-300/60 mt-2 text-sm uppercase tracking-widest">Gestion des informations et critères du membre</p>
                    </div>
                    <a href="/page-admin" class="flex items-center gap-3 text-slate-300 px-6 py-3 rounded-xl font-bold btn-glow-slate shadow-md text-sm uppercase tracking-wider">
                        <i class="fas fa-arrow-left text-blue-400"></i>
                        Retour
                    </a>
                </div>

                <div class="glass-effect rounded-3xl shadow-2xl p-8 max-w-3xl mx-auto holographic-scan relative">
                    <form action="/admin-updateuser" method="POST" class="space-y-6">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-3">Nom & Prénom</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" value="<?= htmlspecialchars($user['nom']) ?>" class="border border-blue-500/20 rounded-xl px-5 py-4 bg-slate-900/40 font-semibold text-slate-400 cursor-not-allowed opacity-70" disabled>
                                <input type="text" value="<?= htmlspecialchars($user['prenom']) ?>" class="border border-blue-500/20 rounded-xl px-5 py-4 bg-slate-900/40 font-semibold text-slate-400 cursor-not-allowed opacity-70" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-3">Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border border-blue-500/20 rounded-xl px-5 py-4 bg-slate-900/40 font-semibold text-slate-400 cursor-not-allowed opacity-70" disabled>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-3">Rôle</label>
                                <select name="role" class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                    <option value="joueur" <?= $user['role'] === 'joueur' ? 'selected' : '' ?> class="bg-slate-950">Joueur</option>
                                    <option value="president" <?= $user['role'] === 'president' ? 'selected' : '' ?> class="bg-slate-950">Président</option>
                                    <option value="entraineur" <?= $user['role'] === 'entraineur' ? 'selected' : '' ?> class="bg-slate-950">Entraîneur</option>
                                    <option value="tresorier" <?= $user['role'] === 'tresorier' ? 'selected' : '' ?> class="bg-slate-950">Trésorier</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-3">Statut</label>
                                <select name="statut" class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                    <option value="en_attente" <?= $user['statut'] === 'en_attente' ? 'selected' : '' ?> class="bg-slate-950 text-yellow-400">En attente</option>
                                    <option value="valide" <?= $user['statut'] === 'valide' ? 'selected' : '' ?> class="bg-slate-950 text-green-400">Validé</option>
                                    <option value="refuse" <?= $user['statut'] === 'refuse' ? 'selected' : '' ?> class="bg-slate-950 text-red-400">Refusé</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-3">Équipe</label>
                            <select name="equipe_id" class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                <option value="" class="bg-slate-950">Aucune équipe</option>
                                <?php foreach ($equipes as $e): ?>
                                    <option value="<?= $e['id'] ?>" <?= $user['equipe_id'] == $e['id'] ? 'selected' : '' ?> class="bg-slate-950">
                                        <?= htmlspecialchars($e['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pt-6 flex flex-col md:flex-row justify-end gap-4">
                            <a href="/page-admin" class="flex-1 md:flex-none px-8 py-4 text-slate-300 rounded-xl font-bold btn-glow-slate text-center shadow-md uppercase tracking-wider text-sm">
                                Annuler
                            </a>
                            <button type="submit" class="flex-1 md:flex-none px-8 py-4 btn-glow-green text-white rounded-xl font-black shadow-lg uppercase tracking-widest text-sm">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

</body>

</html>