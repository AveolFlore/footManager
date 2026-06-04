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

// récupérer les utilisateurs
$users = $userModel->readAll();

// compter les demandes en attente
$total_attente = $userModel->countPending();

// récupérer les demandes en attente
$demandes = $userModel->getPendingUsers();

$pageTitle = "Administration";
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

        .btn-glow-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.3s ease;
        }

        .btn-glow-blue:hover {
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
        }

        .btn-glow-purple {
            background: linear-gradient(135deg, #9333ea, #7e22ce);
            transition: all 0.3s ease;
        }

        .btn-glow-purple:hover {
            box-shadow: 0 0 20px rgba(147, 51, 234, 0.5);
            transform: translateY(-1px);
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
        }

        .tab-active {
            background: linear-gradient(90deg, rgba(22, 163, 74, 0.2), rgba(21, 128, 61, 0.05));
            border-bottom: 3px solid #22c55e;
            color: #ffffff;
        }
    </style>
</head>

<body class="min-h-screen text-slate-100 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 bg-fixed">

    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-10 z-0">
        <div class="absolute top-20 right-20 float-ball">
            <div class="w-32 h-32 border border-blue-500 rounded-full energy-pulse"></div>
        </div>
    </div>

    <div class="flex min-h-screen relative z-10">

        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-blue-500/20 pb-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent flex items-center gap-3">
                            <i class="fas fa-cog text-blue-400"></i>
                            Administration
                        </h1>
                        <p class="text-blue-300/60 mt-1 text-sm uppercase tracking-widest">Gestion des membres et du protocole de sélection</p>
                    </div>

                    <a href="/page-admincreateuser" class="flex items-center gap-3 btn-glow-green text-white px-6 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider shadow-lg">
                        <i class="fas fa-plus"></i>
                        Créer un utilisateur
                    </a>
                </div>

                <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden mb-8 relative holographic-scan">

                    <div class="flex border-b border-blue-500/20 bg-slate-950/40">
                        <button class="flex-1 px-8 py-5 tab-active flex items-center justify-center gap-3 font-black text-sm uppercase tracking-wider transition-all">
                            <i class="fas fa-check-double text-green-400"></i>
                            Validation Joueurs
                            <span class="bg-green-500 text-slate-950 rounded-full px-3 py-0.5 font-black text-xs shadow-md">
                                <?= $total_attente ?>
                            </span>
                        </button>

                        <button class="flex-1 px-8 py-5 text-slate-400 hover:text-slate-200 hover:bg-slate-900/30 font-bold text-sm uppercase tracking-wider transition-all">
                            <i class="fas fa-sliders-h mr-2 text-blue-400"></i>
                            Paramètres Club
                        </button>
                    </div>

                    <div class="p-6 md:p-8">
                        <h3 class="text-lg font-black text-white mb-6 flex items-center gap-2 uppercase tracking-wider">
                            <i class="fas fa-users text-blue-400"></i>
                            Demandes en attente
                        </h3>

                        <div class="space-y-4">
                            <?php foreach ($demandes as $d): ?>
                                <div class="border border-blue-500/20 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-900/50 backdrop-blur-sm relative overflow-hidden group hover:border-blue-500/40 transition-all duration-300">
                                    <div class="absolute left-0 top-0 h-full w-1 bg-amber-500"></div>

                                    <div class="flex-1">
                                        <p class="font-black text-xl text-white tracking-wide">
                                            <?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?>
                                        </p>
                                        <p class="text-sm text-blue-300/70 font-semibold mt-1"><?= htmlspecialchars($d['email']) ?></p>
                                        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                                            <i class="fas fa-clock text-amber-500"></i>
                                            En attente de traitement du dossier
                                        </p>

                                        <div class="mt-4 flex flex-wrap gap-3">
                                            <form action="/admin-validateuser" method="POST" class="inline">
                                                <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                                <input type="hidden" name="equipe_id" value="1">
                                                <button class="btn-glow-blue text-white px-5 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-md">
                                                    <i class="fas fa-users mr-2 opacity-80"></i>Équipe A
                                                </button>
                                            </form>

                                            <form action="/admin-validateuser" method="POST" class="inline">
                                                <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                                <input type="hidden" name="equipe_id" value="2">
                                                <button class="btn-glow-purple text-white px-5 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-md">
                                                    <i class="fas fa-users mr-2 opacity-80"></i>Équipe B
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end justify-between h-full min-w-[120px] gap-4 w-full md:w-auto">
                                        <span class="bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs px-3 py-1.5 rounded-lg font-bold tracking-wider uppercase flex items-center gap-1.5 self-start md:self-auto">
                                            <i class="fas fa-hourglass-half animate-spin-slow"></i>
                                            Attente
                                        </span>

                                        <form action="/admin-rejetuser" method="POST" class="w-full md:w-auto">
                                            <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                                            <button class="text-red-400 font-bold text-xs hover:text-red-300 transition-colors flex items-center justify-center gap-2 uppercase tracking-wider w-full md:w-auto px-3 py-1 border border-transparent hover:border-red-500/20 rounded-lg bg-red-500/5 md:bg-transparent">
                                                <i class="fas fa-times"></i>
                                                Refuser
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden relative">
                    <div class="p-6 md:p-8 border-b border-blue-500/20 bg-slate-950/20">
                        <h3 class="text-lg font-black text-white flex items-center gap-2 uppercase tracking-wider">
                            <i class="fas fa-list text-blue-400"></i>
                            Tous les utilisateurs
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-blue-500/20 bg-slate-900/60 text-blue-300 uppercase tracking-widest text-xs font-black">
                                    <th class="p-6">Nom</th>
                                    <th class="p-6">Email</th>
                                    <th class="p-6">Rôle</th>
                                    <th class="p-6">Statut</th>
                                    <th class="p-6 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-blue-500/10 bg-slate-950/10">
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-blue-500/5 transition-all duration-200">
                                        <td class="p-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-950 to-slate-900 border border-blue-500/30 flex items-center justify-center shadow-inner">
                                                    <i class="fas fa-user text-blue-400 text-md"></i>
                                                </div>
                                                <span class="font-bold text-white tracking-wide"><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></span>
                                            </div>
                                        </td>

                                        <td class="p-6 text-slate-300 font-semibold text-sm"><?= htmlspecialchars($user['email']) ?></td>

                                        <td class="p-6">
                                            <span class="bg-blue-500/10 text-blue-300 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wider border border-blue-500/20">
                                                <?= htmlspecialchars($user['role']) ?>
                                            </span>
                                        </td>

                                        <td class="p-6">
                                            <?php if ($user['statut'] === 'actif'): ?>
                                                <span class="bg-green-500/10 text-green-400 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wider border border-green-500/20">
                                                    <?= htmlspecialchars($user['statut']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="bg-yellow-500/10 text-yellow-400 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wider border border-yellow-500/20">
                                                    <?= htmlspecialchars($user['statut']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="p-6 text-right whitespace-nowrap">
                                            <div class="inline-flex gap-4 justify-end">
                                                <a href="/admin-edituser?id=<?= $user['id'] ?>" class="text-blue-400 font-bold text-xs uppercase tracking-wider hover:text-blue-300 transition-colors flex items-center gap-1.5 px-2 py-1 hover:bg-blue-500/10 rounded-md">
                                                    <i class="fas fa-edit"></i>
                                                    Modifier
                                                </a>
                                                <a href="/admin-deleteuser?id=<?= $user['id'] ?>" class="text-red-400 font-bold text-xs uppercase tracking-wider hover:text-red-300 transition-colors flex items-center gap-1.5 px-2 py-1 hover:bg-red-500/10 rounded-md" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                    <i class="fas fa-trash"></i>
                                                    Supprimer
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>