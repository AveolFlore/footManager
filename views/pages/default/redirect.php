<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if($_SESSION['user']['statut'] !== 'en_attente') {
    header('Location: /');
    exit;
}
$msg = $_GET['msg'] ?? null; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation en cours | FC Green Lions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(0.98); }
        }
        .animate-pulse-soft { animation: pulse-soft 3s infinite ease-in-out; }
    </style>
</head>

<body class="bg-gradient-to-br from-green-800 via-green-900 to-black min-h-screen text-white font-sans selection:bg-green-500">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex flex-col justify-center items-center min-h-[90vh] px-4 py-12 relative overflow-hidden">
        
        <!-- Décoration de fond (Flou artistique) -->
        <div class="absolute top-1/4 -left-20 w-64 h-64 bg-green-600 rounded-full blur-[120px] opacity-20"></div>
        <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-green-400 rounded-full blur-[150px] opacity-10"></div>

        <main class="relative z-10 w-full max-w-2xl text-center">
            
            <!-- Icone Animée -->
            <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-green-600/20 rounded-full border border-green-500/30 backdrop-blur-sm animate-pulse-soft">
                <i class="fa-solid fa-shield-halved text-4xl text-green-400"></i>
            </div>

            <!-- Message Principal -->
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-green-300">
                Presque sur le terrain...
            </h1>
            
            <p class="text-lg text-green-100/70 mb-8 max-w-md mx-auto leading-relaxed">
                Bienvenue au <span class="text-green-400 font-semibold">FC Green Lions</span>. Votre profil est actuellement en cours d'examen par le bureau.
            </p>

            <!-- Card de Statut -->
            <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl relative">
                
                <?php if ($msg): ?>
                    <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg flex items-center gap-3 text-yellow-200 text-sm">
                        <i class="fa-solid fa-circle-info"></i>
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col items-center gap-6">
                    <!-- Barre de progression stylisée -->
                    <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden border border-white/5">
                        <div class="bg-gradient-to-r from-green-600 to-green-400 h-full w-[65%] rounded-full relative">
                            <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                        </div>
                    </div>

                    <div class="flex justify-between w-full text-xs font-medium uppercase tracking-widest text-green-300/50">
                        <span>Inscription</span>
                        <span class="text-green-400 animate-pulse">Vérification Bureau</span>
                        <span>Accès Club</span>
                    </div>
                </div>

                <hr class="my-8 border-white/10">

                <!-- Actions / Info -->
                <div class="space-y-4">
                    <p class="text-sm text-gray-400 italic">
                        "Un Lion ne court pas, il attend le bon moment."
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="/page-login" class="px-6 py-2 bg-green-600 hover:bg-green-500 rounded-full font-bold transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-green-900/20">
                            Me connecter
                        </a>
                        <a href="/auth-logout" class="px-6 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-full font-medium transition-all">
                            Se déconnecter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Aide / Contact -->
            <p class="mt-12 text-sm text-white/40">
                Besoin d'aide ? <a href="mailto:contact@greenlions.com" class="text-green-400 hover:underline">Contactez le secrétariat</a>
            </p>

        </main>
    </div>

</body>
</html>
