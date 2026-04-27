<?php
$msg = $_GET['msg'] ?? null;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page De Redirection</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        
        <main class="flex-1 p-4 md:p-6">
        <h2>Page Redirection</h2>
        <!-- Ton contenu -->

        <?php if ($msg): ?>
                <p class="bg-yellow-100 text-yellow-800 text-sm text-center p-2 rounded mb-4">
                    <?= htmlspecialchars($msg) ?>
                </p>
            <?php endif; ?>
    </main>
</div>
</body>
</html>