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
<style>
    .cyber-bg {
        background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
        position: relative;
    }
    .cyber-bg::before {
        content: " ";
        display: block;
        position: fixed;
        top: 0; left: 0; bottom: 0; right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(6, 182, 212, 0.04), rgba(0, 0, 0, 0), rgba(168, 85, 247, 0.04));
        z-index: 99999;
        opacity: 0.25;
        pointer-events: none;
        background-size: 100% 4px, 6px 100%;
    }
</style>
<!-- HEADER -->
<header class="w-full h-20 bg-slate-900/90 backdrop-blur-xl border-b border-slate-800 flex items-center justify-between px-6 md:px-8 sticky top-0 z-30 shadow-2xl">
    
    <!-- LEFT: Titre & Burger -->
    <div class="flex items-center gap-6">
        <!-- BURGER (mobile only) -->
        <button onclick="toggleSidebar()" class="md:hidden bg-slate-800 border border-slate-700 text-cyan-400 p-3 rounded-xl transition-all active:scale-95">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- TITLE DYNAMIQUE -->
        <div>
            <h1 class="text-xl font-extrabold bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent leading-tight font-mono">
                <?= htmlspecialchars($titreAffiche) ?>
            </h1>
            <p class="text-xs text-cyan-500/70 mt-1 flex items-center gap-1 font-mono uppercase tracking-widest">
                <i class="fa-solid fa-calendar-days"></i>
                <?php
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                echo ucfirst($formatter->format(new DateTime()));
                ?>
            </p>
        </div>
    </div>

    <!-- RIGHT: Search, Notifs & Profil & Logout -->
    <div class="flex items-center gap-4 md:gap-6">

        <!-- SEARCH BAR -->
        <div class="hidden md:flex items-center bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 w-72 transition-all focus-within:border-cyan-500/50">
            <i class="fas fa-search text-slate-500 mr-3 text-sm"></i>
            <input type="text" placeholder="Rechercher (joueurs, matchs...)" class="bg-transparent border-0 focus:ring-0 text-slate-300 text-sm w-full placeholder:text-slate-600 font-mono">
        </div>

        <!-- NOTIFICATIONS -->
        <div class="relative" id="notificationsDropdown">
            <button onclick="toggleNotifications(event)" class="relative p-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-400 hover:text-cyan-400 transition-all active:scale-95">
                <i class="fas fa-bell text-base"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[9px] font-black rounded-full h-5 w-5 flex items-center justify-center border-2 border-slate-950 shadow-md animate-pulse">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </button>

            <!-- Dropdown Notifications -->
            <div id="notificationsList" class="absolute right-0 top-full mt-3 w-96 bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 hidden z-50 overflow-hidden">
                <div class="p-5 border-b border-slate-800 bg-slate-950">
                    <h3 class="font-bold text-slate-200 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-bell text-green-600"></i>
                        Notifications
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Vous avez <?= $unread_count ?> nouvelle(s) notification(s)</p>
                </div>
                <div class="max-h-[400px] overflow-y-auto">
                    <?php if (empty($notifications)): ?>
                        <div class="p-10 text-center text-slate-500">
                            <i class="fas fa-inbox text-5xl text-slate-700 mb-4"></i>
                            <p class="font-medium text-slate-400">Aucune notification pour le moment</p>
                            <p class="text-sm text-slate-600 mt-2">Vous serez averti des prochaines activités</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <a href="<?= htmlspecialchars($notif['lien'] ?? '#') ?>" class="block p-4 hover:bg-slate-800 border-b border-slate-800 transition-all">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold
                                        <?php
                                        $colors = [
                                            'cotisation' => 'bg-green-100 text-green-700',
                                            'reglement' => 'bg-blue-100 text-blue-700',
                                            'sanction' => 'bg-yellow-100 text-yellow-700',
                                            'default' => 'bg-slate-100 text-slate-700'
                                        ];
                                        echo $colors[$notif['type']] ?? $colors['default'];
                                        ?>">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-300 line-clamp-2"><?= htmlspecialchars($notif['message']) ?></p>
                                        <p class="text-xs text-slate-500 mt-1.5">
                                            <i class="far fa-clock mr-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($notif['date_creation'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="p-4 bg-slate-950 border-t border-slate-800">
                    <button class="w-full text-center text-sm font-bold text-cyan-400 hover:text-cyan-300 transition flex items-center justify-center gap-1">
                        <i class="fas fa-eye"></i>
                        Voir toutes les notifications
                    </button>
                </div>
            </div>
        </div>

        <!-- USER PROFILE -->
        <div class="flex items-center gap-4 pl-4 border-l border-slate-800">
            <!-- BADGE RÔLE -->
            <div class="hidden lg:flex flex-col items-end mr-2">
                <span class="text-sm font-bold text-slate-200">
                    <?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-circle text-cyan-500 text-[8px]"></i>
                    <?= htmlspecialchars($role) ?>
                </span>
            </div>

            <!-- AVATAR -->
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-700 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-cyan-500/30">
                    <?= strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1)) ?>
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-cyan-500 border-2 border-slate-950 rounded-full"></div>
            </div>

            <!-- LOGOUT -->
            <a href="/auth-logout" class="p-3 text-slate-500 hover:text-rose-500 transition-colors" title="Déconnexion">
                <i class="fas fa-power-off text-lg"></i>
            </a>
        </div>
    </div>
</header>


<!-- CONFIRMATION MODAL -->
<div id="confirmModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
    <div class="bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-800">
        <div class="p-8">
            <h3 class="text-2xl font-bold text-slate-200 mb-3" id="confirmModalTitle">Êtes-vous sûr ?</h3>
            <p class="text-slate-500" id="confirmModalMessage">Cette action est irréversible.</p>
        </div>
        <div class="flex gap-3 p-6 border-t bg-slate-950">
            <button onclick="closeConfirmModal()" class="flex-1 px-6 py-3 border border-slate-700 rounded-xl text-slate-400 font-bold hover:bg-slate-800 hover:border-slate-600 transition-all">
                Annuler
            </button>
            <button onclick="executeConfirmAction()" class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-600 to-orange-600 hover:from-rose-700 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all">
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

function toggleNotifications(event) {
    event.stopPropagation();
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
