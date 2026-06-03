<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');
$msg = $_GET['msg'] ?? null;

$pageTitle = "Créer Utilisateur";
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
        
        /* Personnalisation des flèches et du calendrier natifs sur fond sombre */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.7;
        }
    </style>
</head>

<body class="min-h-screen text-slate-100 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 bg-fixed">

    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-20 z-0">
        <div class="absolute top-10 right-10 float-ball" style="animation-delay: 0s;">
            <div class="w-24 h-24 bg-gradient-to-br from-white to-gray-400 rounded-full flex items-center justify-center energy-pulse">
                <i class="fas fa-futbol text-5xl text-slate-900"></i>
            </div>
        </div>
        <div class="absolute bottom-10 left-10 w-64 h-64">
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
                            <i class="fas fa-user-plus text-blue-400"></i>
                            Créer un utilisateur
                        </h1>
                        <p class="text-blue-300/60 text-xs tracking-widest uppercase mt-1">Espace d'administration / Recrutement Elite</p>
                    </div>
                    <a href="/page-admin" class="flex items-center gap-3 text-slate-300 px-6 py-3 rounded-xl font-bold btn-glow-slate shadow-md text-sm uppercase tracking-wider">
                        <i class="fas fa-arrow-left text-blue-400"></i>
                        Retour
                    </a>
                </div>

                <?php if ($msg): ?>
                    <div class="mb-8 px-6 py-4 rounded-xl bg-red-500/10 text-red-300 font-semibold border border-red-500/30 shadow-lg backdrop-blur-md flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-red-400 animate-pulse"></i>
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="glass-effect rounded-3xl shadow-2xl p-8 max-w-3xl mx-auto holographic-scan relative">
                    <form action="/auth-adminstoreuser" method="POST" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Nom</label>
                                <input type="text" name="nom" placeholder="Nom" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Prénom</label>
                                <input type="text" name="prenom" placeholder="Prénom" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Email</label>
                            <input type="email" name="email" placeholder="Email professionnel" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Téléphone</label>
                            <input type="text" name="telephone" placeholder="Téléphone" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Date de naissance</label>
                            <input type="date" name="date_naissance" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Poste</label>
                                <select name="poste" class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                    <option value="" class="bg-slate-950">-- Poste --</option>
                                    <option value="gard" class="bg-slate-950">Gardien</option>
                                    <option value="def" class="bg-slate-950">Défenseur</option>
                                    <option value="mil" class="bg-slate-950">Milieu</option>
                                    <option value="att" class="bg-slate-950">Attaquant</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Pied dominant</label>
                                <select name="pied_dominant" class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                    <option value="" class="bg-slate-950">-- Pied dominant --</option>
                                    <option value="droit" class="bg-slate-950">Droit</option>
                                    <option value="gauche" class="bg-slate-950">Gauche</option>
                                    <option value="2" class="bg-slate-950">Les deux</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Numéro de maillot</label>
                            <input type="number" name="numero_maillot" placeholder="Numéro maillot" min="1" max="99" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Mot de passe</label>
                            <input type="password" name="mot_de_passe" placeholder="Mot de passe confidentiel" class="w-full bg-slate-900/60 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white placeholder-slate-500 font-semibold" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Rôle système</label>
                            <select name="role" required class="w-full bg-slate-900/80 border border-blue-500/30 rounded-xl px-5 py-4 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow text-white font-semibold appearance-none cursor-pointer">
                                <option value="" class="bg-slate-950">-- Rôle --</option>
                                <option value="joueur" class="bg-slate-950">Joueur</option>
                                <option value="medecin" class="bg-slate-950">Médecin</option>
                                <option value="censeur" class="bg-slate-950">Censeur</option>
                                <option value="entraineur" class="bg-slate-950">Entraîneur</option>
                                <option value="organisateur" class="bg-slate-950">Organisateur</option>
                                <option value="president" class="bg-slate-950">Président</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full btn-glow-green text-white py-4 rounded-xl font-black text-sm uppercase tracking-widest shadow-xl mt-4">
                            <i class="fas fa-plus mr-2"></i>
                            Créer l'utilisateur interne
                        </button>

                    </form>
                </div>

            </div>
        </main>
    </div>

</body>
</html>