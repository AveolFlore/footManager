<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/notifications/Notification.php';

use Config\Database;
use Models\Notifications\Notification;

// On définit un titre par défaut si la page n'en a pas fourni
$titreAffiche = $pageTitle ?? "Gestion du Club";

// On récupère les infos session en toute sécurité
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

<!-- HEADER -->
<header class="w-full h-16 bg-white border-b flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
    
    <!-- LEFT: Titre & Burger -->
    <div class="flex items-center gap-4">
        <!-- BURGER (mobile only) -->
        <button onclick="toggleSidebar()" class="md:hidden bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition shadow-md">
            <i class="fas fa-bars"></i>
        </button>

        <!-- TITLE DYNAMIQUE -->
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">
                <?= htmlspecialchars($titreAffiche) ?>
            </h1>
            <p class="text-xs text-gray-500 hidden sm:block">
                Welcome, <span class="text-green-600 font-medium"><?= htmlspecialchars($prenom . ' ' . $nom) ?></span>
            </p>
        </div>
    </div>

    <!-- RIGHT: Notifs & Profil -->
    <div class="flex items-center gap-3 md:gap-5">

        <!-- NOTIFICATIONS -->
        <div class="relative group cursor-pointer" id="notificationsDropdown">
            <button onclick="toggleNotifications()" class="text-gray-400 hover:text-green-600 transition text-xl p-1 relative">
                <i class="fas fa-bell"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-white">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </button>

            <!-- Dropdown Notifications -->
            <div id="notificationsList" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 hidden group-hover:block z-50">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Notifications</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <?php if (empty($notifications)): ?>
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Aucune notification</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <a href="<?= htmlspecialchars($notif['lien'] ?? '#') ?>" class="block p-4 hover:bg-gray-50 border-b border-gray-50">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        <?php
                                        $colors = [
                                            'cotisation' => 'bg-green-100 text-green-700',
                                            'reglement' => 'bg-blue-100 text-blue-700',
                                            'default' => 'bg-gray-100 text-gray-700'
                                        ];
                                        echo $colors[$notif['type']] ?? $colors['default'];
                                        ?>">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 line-clamp-2"><?= htmlspecialchars($notif['message']) ?></p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <?= date('d/m/Y H:i', strtotime($notif['date_creation'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- BADGE RÔLE (Visuel moderne) -->
        <div class="hidden lg:flex items-center bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-600">
                <?= htmlspecialchars($role) ?>
            </span>
        </div>

    </div>
</header>


<!-- CONFIRMATION MODAL -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-2" id="confirmModalTitle">Êtes-vous sûr ?</h3>
            <p class="text-gray-600 mb-6" id="confirmModalMessage">Cette action est irréversible.</p>
        </div>
        <div class="flex gap-3 p-4 border-t bg-gray-50">
            <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition">
                Annuler
            </button>
            <button onclick="executeConfirmAction()" class="flex-1 px-4 py-2 bg-red-600 rounded-lg text-white font-medium hover:bg-red-700 transition">
                Confirmer
            </button>
        </div>
    </div>
</div>

<!-- SCRIPT MOBILE & NOTIFICATIONS & CONFIRM MODAL -->
<script>
let confirmCallback = null;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function toggleNotifications() {
    const list = document.getElementById('notificationsList');
    list.classList.toggle('hidden');
}

function openConfirmModal(title, message, callback) {
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalMessage').textContent = message;
    confirmCallback = callback;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    confirmCallback = null;
}

function executeConfirmAction() {
    if (confirmCallback) {
        confirmCallback();
    }
    closeConfirmModal();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationsDropdown');
    const list = document.getElementById('notificationsList');
    if (!dropdown.contains(event.target)) {
        list.classList.add('hidden');
    }

    // Close confirm modal if clicking outside
    const confirmModal = document.getElementById('confirmModal');
    if (confirmModal && event.target === confirmModal) {
        closeConfirmModal();
    }
});
</script>
