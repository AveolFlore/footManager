<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/notifications/Notification.php';

use Config\Database;
use Models\Notifications\Notification;

$titreAffiche = $pageTitle ?? "Gestion du Club";

$prenom = $_SESSION['user']['prenom'] ?? 'Invité';
$nom = $_SESSION['user']['nom'] ?? '';
$role = $_SESSION['user']['role'] ?? '';
$user_id = $_SESSION['user']['id'] ?? null;

$notifications = [];
$unread_count = 0;
if ($user_id) {
    $db = (new Database())->connect();
    $notificationModel = new Notification($db);
    $notifications = $notificationModel->getUnread($user_id);
    $unread_count = $notificationModel->countUnread($user_id);
}
?>
<header class="header">
    <h1><?= htmlspecialchars($titreAffiche) ?></h1>
    <div class="header-actions">
        <span>
            <?php if ($unread_count > 0): ?>
                <strong><?= $unread_count ?> notif(s)</strong>
            <?php endif; ?>
        </span>
        <span>
            <?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?> (<?= htmlspecialchars($role) ?>)
        </span>
        <a href="/auth-logout">Déconnexion</a>
    </div>
</header>
