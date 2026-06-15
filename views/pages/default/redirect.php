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
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex flex-col justify-center items-center min-h-[80vh] px-4 py-12">

        <main class="w-full max-w-md text-center">

            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 bg-slate-100 text-slate-500 rounded-full border border-slate-200 shadow-sm">
                <i class="fa-solid fa-shield-halved text-3xl"></i>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">
                Compte en attente de validation
            </h1>

            <p class="text-sm text-slate-500 mb-8">
                Votre profil est en cours d'analyse par les administrateurs.<br>
                L'accès complet sera disponible après approbation.
            </p>

            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">

                <?php if ($msg): ?>
                    <div class="mb-6 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs font-medium text-left">
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="/page-login"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm rounded-lg shadow-sm transition-colors">
                        Me connecter
                    </a>
                    <a href="/auth-logout"
                       class="w-full sm:w-auto text-center px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-sm rounded-lg transition-colors">
                        Se déconnecter
                    </a>
                </div>
            </div>

        </main>
    </div>

</body>
</html>