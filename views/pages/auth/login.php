<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user'])) {
    header("Location:/page-home");
    exit;
}

$msg = $_GET['msg'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FC Blue Lock - Connexion</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom keyframes */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes energyPulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5),
                    0 0 40px rgba(59, 130, 246, 0.3),
                    0 0 60px rgba(59, 130, 246, 0.2);
            }

            50% {
                box-shadow: 0 0 40px rgba(59, 130, 246, 0.7),
                    0 0 80px rgba(59, 130, 246, 0.5),
                    0 0 120px rgba(59, 130, 246, 0.3);
            }
        }

        @keyframes holographic {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateX(200%);
                opacity: 0;
            }
        }

        @keyframes scoreboardBlink {

            0%,
            100% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes tacticalGlow {

            0%,
            100% {
                border-color: rgba(34, 197, 94, 0.3);
            }

            50% {
                border-color: rgba(34, 197, 94, 0.8);
            }
        }

        .float-ball {
            animation: float 6s ease-in-out infinite;
        }

        .energy-pulse {
            animation: energyPulse 3s ease-in-out infinite;
        }

        .scoreboard-blink {
            animation: scoreboardBlink 1.5s ease-in-out infinite;
        }

        .tactical-border {
            animation: tacticalGlow 4s ease-in-out infinite;
        }

        .holographic-scan::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.2), transparent);
            animation: holographic 3s linear infinite;
            pointer-events: none;
        }

        .glass-effect {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .btn-glow {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8, #1e3a8a);
            background-size: 200% 200%;
            transition: all 0.4s ease;
        }

        .btn-glow:hover {
            background-position: 100% 100%;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.6),
                0 0 60px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        .input-glow:focus {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4),
                inset 0 0 10px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>

<body class="min-h-screen overflow-hidden">
    <!-- Background with gradient & particles -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Floating football 1 -->
            <div class="absolute top-10 left-10 float-ball opacity-20" style="animation-delay: 0s;">
                <div class="w-24 h-24 bg-gradient-to-br from-white to-gray-300 rounded-full shadow-2xl flex items-center justify-center energy-pulse">
                    <i class="fas fa-futbol text-5xl text-slate-800"></i>
                </div>
            </div>

            <!-- Floating football 2 -->
            <div class="absolute bottom-20 right-20 float-ball opacity-20" style="animation-delay: 2s;">
                <div class="w-20 h-20 bg-gradient-to-br from-white to-gray-300 rounded-full shadow-2xl flex items-center justify-center energy-pulse">
                    <i class="fas fa-futbol text-4xl text-slate-800"></i>
                </div>
            </div>

            <!-- Floating football 3 -->
            <div class="absolute top-1/2 right-10 float-ball opacity-15" style="animation-delay: 4s;">
                <div class="w-16 h-16 bg-gradient-to-br from-white to-gray-300 rounded-full shadow-2xl flex items-center justify-center energy-pulse">
                    <i class="fas fa-futbol text-3xl text-slate-800"></i>
                </div>
            </div>

            <!-- Holographic scoreboard top-left -->
            <div class="absolute top-20 left-20 w-48 glass-effect rounded-xl p-4 opacity-40">
                <div class="text-green-400 text-xs uppercase tracking-widest mb-2 scoreboard-blink">Live Score</div>
                <div class="flex items-center justify-between text-white font-bold">
                    <div class="text-center">
                        <div class="text-xs text-blue-300">Blue Lock</div>
                        <div class="text-2xl">2</div>
                    </div>
                    <div class="text-yellow-400 text-lg">VS</div>
                    <div class="text-center">
                        <div class="text-xs text-purple-300">United</div>
                        <div class="text-2xl">1</div>
                    </div>
                </div>
                <div class="mt-2 text-xs text-slate-400 text-center scoreboard-blink">85'</div>
            </div>

            <!-- Holographic scoreboard bottom-right -->
            <div class="absolute bottom-20 left-10 w-40 glass-effect rounded-xl p-4 opacity-30">
                <div class="text-yellow-400 text-xs uppercase tracking-widest mb-2">Possession</div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex-1">
                        <div class="h-1 bg-gradient-to-r from-green-500 to-green-300 rounded-full" style="width: 65%"></div>
                        <div class="text-green-400 text-xs mt-1">65%</div>
                    </div>
                    <div class="flex-1 text-right">
                        <div class="h-1 bg-gradient-to-r from-purple-300 to-purple-500 rounded-full ml-auto" style="width: 35%"></div>
                        <div class="text-purple-400 text-xs mt-1">35%</div>
                    </div>
                </div>
            </div>

            <!-- Tactical diagram decoration -->
            <div class="absolute bottom-10 right-10 w-64 h-64 opacity-20">
                <div class="w-full h-full border-2 border-dashed tactical-border rounded-full flex items-center justify-center">
                    <div class="w-1/2 h-1/2 border border-blue-500 rounded-full tactical-border"></div>
                </div>
                <div class="absolute top-1/2 left-0 w-4 h-4 bg-blue-500 rounded-full shadow-lg shadow-blue-500"></div>
                <div class="absolute top-1/2 right-0 w-4 h-4 bg-green-500 rounded-full shadow-lg shadow-green-500"></div>
            </div>
        </div>
    </div>

    <!-- Main login container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-lg">
            <!-- Main card -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden holographic-scan">
                <!-- Header -->
                <div class="relative p-8 bg-gradient-to-r from-blue-900/80 to-slate-900/80 border-b border-blue-500/30">
                    <!-- Logo -->
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center energy-pulse">
                            <i class="fas fa-shield-halved text-3xl text-white"></i>
                        </div>
                        <div class="text-center">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent">Blue Lock Japan</h1>
                            <p class="text-blue-300 text-sm tracking-widest uppercase">Elite Football Management</p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                </div>

                <!-- Body -->
                <div class="p-8">
                    <h2 class="text-center text-2xl font-bold text-white mb-2">Accès Protégé</h2>
                    <p class="text-center text-slate-400 text-sm mb-8">Entrez vos identifiants pour accéder à la plateforme</p>

                    <?php if ($msg): ?>
                        <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl text-red-300 text-sm flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= htmlspecialchars($msg) ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="auth-signin" method="POST" class="space-y-5">

                        <!-- Email -->
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                <i class="fas fa-envelope text-lg"></i>
                            </div>
                            <input
                                type="email"
                                name="email"
                                placeholder="Email professionnel"
                                required
                                class="w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                <i class="fas fa-lock text-lg"></i>
                            </div>
                            <input
                                type="password"
                                name="mot_de_passe"
                                placeholder="Mot de passe sécurisé"
                                required
                                class="w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full btn-glow text-white font-bold py-4 rounded-xl uppercase tracking-widest shadow-lg">
                            <i class="fas fa-fingerprint mr-2"></i>
                            Accéder à l'Espace
                        </button>

                    </form>
                </div>

                <!-- Footer -->
                <div class="p-6 bg-slate-900/50 border-t border-blue-500/30">
                    <div class="text-center">
                        <p class="text-slate-400 text-sm mb-2">Pas encore membre ?</p>
                        <a href="/page-register" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 font-semibold transition-colors text-sm">
                            <i class="fas fa-user-plus"></i>
                            Créer un compte professionnel
                        </a>
                    </div>

                    <!-- Stats footer -->
                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div class="p-3 rounded-xl bg-slate-800/50 border border-blue-500/20">
                            <div class="text-2xl font-bold text-green-400">247</div>
                            <div class="text-xs text-slate-500 uppercase">Joueurs</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/50 border border-blue-500/20">
                            <div class="text-2xl font-bold text-blue-400">85</div>
                            <div class="text-xs text-slate-500 uppercase">Matchs</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/50 border border-blue-500/20">
                            <div class="text-2xl font-bold text-yellow-400">12</div>
                            <div class="text-xs text-slate-500 uppercase">Trophées</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-8 text-slate-500 text-xs">
                <p>&copy; 2026 Blue Lock Japan. Tous droits réservés. Système de gestion footballistique professionnel.</p>
            </div>
        </div>
    </div>
</body>

</html>