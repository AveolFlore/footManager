<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// if($_SESSION['user']['statut'] !== 'en_attente') {
//     header('Location: /');
//     exit;
// }
$msg = $_GET['msg'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation en cours | FC Blue Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');

        .cyber-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        }

        .holo-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(34, 211, 238, 0.25);
            box-shadow: 0 0 40px rgba(34, 211, 238, 0.15);
        }

        .neon-text {
            text-shadow: 0 0 12px rgb(34 211 238),
                        0 0 25px rgb(34 211 238);
        }

        .scan-line {
            position: relative;
        }

        .scan-line::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -100%;
            width: 50%;
            height: 200%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.12),
                transparent
            );
            animation: scan 5s linear infinite;
        }

        @keyframes scan {
            0% { transform: translateX(-150%); }
            100% { transform: translateX(400%); }
        }

        .pulse-glow {
            animation: pulse-glow 3s infinite ease-in-out;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.75; transform: scale(0.95); }
        }
    </style>
</head>
<body class="cyber-bg text-slate-200 min-h-screen font-sans">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex flex-col justify-center items-center min-h-[90vh] px-4 py-12 relative overflow-hidden">

        <!-- Background Holographic Effects -->
        <div class="absolute top-1/4 -left-32 w-80 h-80 bg-cyan-500 rounded-full blur-[140px] opacity-10"></div>
        <div class="absolute bottom-1/3 -right-28 w-96 h-96 bg-blue-600 rounded-full blur-[160px] opacity-10"></div>

        <main class="relative z-10 w-full max-w-2xl text-center">

            <!-- Animated Shield Icon -->
            <div class="mb-10 inline-flex items-center justify-center w-28 h-28 bg-slate-900 border border-cyan-400/30 rounded-full pulse-glow">
                <i class="fa-solid fa-shield-halved text-5xl text-cyan-400"></i>
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text mb-4 font-['Orbitron']">
                SYSTÈME EN ATTENTE
            </h1>

            <p class="text-xl text-slate-400 mb-12 max-w-md mx-auto">
                Votre profil est en cours d'analyse par le <span class="text-cyan-400">Bureau Central</span>.<br>
                Accès au terrain bientôt autorisé.
            </p>

            <!-- Status Card -->
            <div class="holo-card rounded-3xl p-10 scan-line">

                <?php if ($msg): ?>
                    <div class="mb-8 p-4 bg-yellow-500/10 border border-yellow-400/30 rounded-2xl flex items-center gap-3 text-yellow-300 text-sm">
                        <i class="fa-solid fa-circle-info"></i>
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-8">
                    <!-- Progress Bar -->
                    <div>
                        <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden border border-slate-700">
                            <div class="bg-gradient-to-r from-cyan-400 to-blue-500 h-full w-[65%] rounded-full relative">
                                <div class="absolute inset-0 bg-white/30 animate-[shimmer_2.5s_infinite]"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs uppercase tracking-widest mt-4 text-slate-400">
                            <span>Inscription</span>
                            <span class="text-cyan-400 font-medium">Vérification en cours</span>
                            <span>Accès complet</span>
                        </div>
                    </div>

                    <hr class="border-slate-700">

                    <!-- Quote -->
                    <div class="italic text-slate-400 text-sm">
                        "Un lion ne court pas après sa proie...<br>Il attend le bon moment."
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        <a href="/page-login"
                           class="btn-glow px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-2xl hover:brightness-110 transition-all">
                            Me connecter
                        </a>
                        <a href="/auth-logout"
class="px-8 py-4 border border-slate-600 hover:border-red-400 text-slate-300 hover:text-red-400 font-medium rounded-2xl transition-all">
                            Se déconnecter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <p class="mt-12 text-sm text-slate-500">
                Besoin d'assistance ?
                <a href="mailto:contact@bluelock.com" class="text-cyan-400 hover:text-cyan-300 hover:underline">
                    Contacter le Secrétariat
                </a>
            </p>

        </main>
    </div>

</body>
</html>