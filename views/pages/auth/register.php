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
    <title>FC Blue Lock - Inscription</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
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

<body class="min-h-screen overflow-y-auto">
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
        </div>
    </div>

    <!-- Main register container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-6xl">
            <!-- Main card -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden holographic-scan flex flex-col md:flex-row">
                <!-- Left: Form -->
                <div class="w-full md:w-1/2 p-8">
                    <!-- Logo & Header -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center energy-pulse">
                            <i class="fas fa-shield-halved text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent">Blue Lock Japan</h1>
                            <p class="text-blue-300 text-xs tracking-widest uppercase">Rejoignez l'élite</p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-2">Inscription Joueur</h2>
                    <p class="text-slate-400 text-sm mb-6">Complétez votre profil pour rejoindre le club</p>

                    <?php if ($msg): ?>
                        <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl text-red-300 text-sm flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= htmlspecialchars($msg) ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="auth-signup" method="POST" enctype="multipart/form-data" class="space-y-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input type="text" name="nom" placeholder="Nom" required
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow"
                                    pattern="[a-zA-ZÀ-ÿ\s'-]+" title="Seulement des lettres">
                            </div>

                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <input type="text" name="prenom" placeholder="Prénom" required
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow"
                                    pattern="[a-zA-ZÀ-ÿ\s'-]+" title="Seulement des lettres">
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email" placeholder="Email professionnel" required
                                class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <input type="text" name="telephone" placeholder="Téléphone"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow"
                                    pattern="[0-9+\s]+" title="Seulement des chiffres">
                            </div>

                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input type="date" name="date_naissance" required
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-running"></i>
                                </div>
                                <select name="poste" required
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow appearance-none">
                                    <option value="" class="bg-slate-900">-- Poste --</option>
                                    <option value="gard" class="bg-slate-900">Gardien</option>
                                    <option value="def" class="bg-slate-900">Défenseur</option>
                                    <option value="mil" class="bg-slate-900">Milieu</option>
                                    <option value="att" class="bg-slate-900">Attaquant</option>
                                </select>
                            </div>

                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-shoe-prints"></i>
                                </div>
                                <select name="pied_dominant"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow appearance-none">
                                    <option value="" class="bg-slate-900">-- Pied dominant --</option>
                                    <option value="droit" class="bg-slate-900">Droit</option>
                                    <option value="gauche" class="bg-slate-900">Gauche</option>
                                    <option value="2" class="bg-slate-900">Les deux</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <input type="number" name="numero_maillot" placeholder="Numéro maillot"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow"
                                    min="1" max="999">
                            </div>

                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required minlength="6"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                                <i class="fas fa-lock-open"></i>
                            </div>
                            <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer mot de passe" required minlength="6"
                                class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-blue-500/30 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all duration-300 input-glow">
                        </div>

                        <div class="p-4 bg-slate-800/50 border border-blue-500/30 rounded-xl">
                            <label class="text-sm text-slate-300 mb-2 block flex items-center gap-2">
                                <i class="fas fa-camera text-blue-400"></i>
                                Photo de profil (optionnel)
                            </label>
                            <input type="file" name="photo_profil" accept="image/*"
                                class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:transition-all">
                        </div>

                        <button type="submit"
                            class="w-full btn-glow text-white font-bold py-4 rounded-xl uppercase tracking-widest shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Rejoindre le Club
                        </button>

                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-slate-400 text-sm mb-2">Déjà membre ?</p>
                        <a href="/page-login" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 font-semibold transition-colors text-sm">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter à l'espace
                        </a>
                    </div>
                </div>

                <!-- Right: GIF -->
                <div class="w-full md:w-1/2 bg-gradient-to-br from-blue-900/80 to-slate-900/80 flex items-center justify-center p-8 border-t md:border-t-0 md:border-l border-blue-500/30">
                    <div class="text-center">
                        <img src="/assets/images/blue-lock-10.gif" alt="Blue Lock" class="w-full max-w-md rounded-2xl shadow-2xl mb-6">
                        <h3 class="text-xl font-bold bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent mb-3">Devenir le meilleur</h3>
                        <p class="text-slate-400 text-sm">Rejoignez l'élite du football et faites partie de l'aventure Blue Lock</p>
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
