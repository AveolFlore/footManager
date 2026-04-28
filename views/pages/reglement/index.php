<?php
require_once __DIR__ . '/../../../middleware/Role.php';

    requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Reglement</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 p-4 md:p-6">
        <h2>Page Reglement</h2>
        <!-- Ton contenu -->
    </main>
</div>
</body>
</html>