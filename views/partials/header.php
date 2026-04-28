
<?php
// On définit un titre par défaut si la page n'en a pas fourni
$titreAffiche = $pageTitle ?? "Gestion du Club";

// On récupère les infos session en toute sécurité
$prenom = $_SESSION['user']['prenom'] ?? 'Invité';
$nom = $_SESSION['user']['nom'] ?? '';
$role = $_SESSION['user']['role'] ?? '';
?>

<!-- HEADER -->
<header class="w-full h-16 bg-white border-b flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
    
    <!-- LEFT: Titre & Burger -->
    <div class="flex items-center gap-4">
        <!-- BURGER (mobile only) -->
        <button onclick="toggleSidebar()" class="md:hidden bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition shadow-md">
            ☰
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
        <div class="relative group cursor-pointer">
            <button class="text-gray-400 hover:text-green-600 transition text-xl p-1">
                <i class="fa-regular fa-bell"></i> 🔔
            </button>
            <!-- Badge -->
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
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


<!-- SCRIPT MOBILE -->
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>