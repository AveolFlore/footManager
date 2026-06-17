<?php
if (session_status() === PHP_SESSION_NONE) session_start();
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
        .bg-impact {
            background-image: url('/assets/images/Impact.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-impact min-h-screen text-slate-800">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex flex-col justify-center items-center min-h-[80vh] px-4 py-12">
        <main class="w-full max-w-md text-center bg-white/90 backdrop-blur-sm p-8 rounded-3xl shadow-2xl border border-white/50">

            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 bg-slate-100 text-slate-500 rounded-full border border-slate-200 shadow-sm">
                <i class="fa-solid fa-shield-halved text-3xl"></i>
            </div>

            <h1 class="text-2xl font-black text-slate-900 mb-2">
                Compte en attente de validation
            </h1>

            <p class="text-sm font-bold text-slate-500 mb-8">
                Votre profil est en cours d'analyse par les administrateurs.<br>
                L'accès complet sera disponible après approbation.
            </p>

            <?php if ($msg): ?>
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs font-black text-left shadow-inner">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-center gap-3 pt-4 border-t border-slate-100">
                <a href="/page-login"
                   class="w-full sm:w-auto text-center px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-black text-sm rounded-xl shadow-lg transition-all">
                    Me connecter
                </a>
                <a href="/auth-logout"
                   class="w-full sm:w-auto text-center px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-black text-sm rounded-xl transition-all">
                    Se déconnecter
                </a>
            </div>

        </main>
    </div>

</body>
</html>