<?php
// Titre par défaut si non défini par la page
$titreAffiche = $pageTitle ?? "Gestion du Club";

// Récupération sécurisée des infos session
$prenom = $_SESSION['user']['prenom'] ?? 'Invité';
$nom = $_SESSION['user']['nom'] ?? '';
$role = $_SESSION['user']['role'] ?? 'Utilisateur';
?>

<!-- HEADER -->
<header class="w-full h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
    
    <!-- LEFT: Titre & Welcome Message -->
    <div class="flex flex-col">
        <h1 class="text-[#1e293b] text-lg font-extrabold leading-tight tracking-tight">
            <?= htmlspecialchars($titreAffiche) ?>
        </h1>
        <p class="text-[11px] text-gray-400 font-medium">
            Welcome, <span class="text-[#22c55e] font-bold"><?= htmlspecialchars($prenom . ' ' . $nom) ?></span>
        </p>
    </div>

    <!-- RIGHT: Notifications & Badge Rôle -->
    <div class="flex items-center gap-6">

        <!-- NOTIFICATIONS -->
        <button class="relative p-2 text-[#fbbf24] hover:scale-110 transition-transform">
            <span class="text-2xl">🔔</span>
            <!-- Badge Notification -->
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white shadow-sm"></span>
        </button>

        <!-- BADGE RÔLE -->
        <div class="flex items-center bg-[#f1f5f9] px-3 py-1.5 rounded-full border border-gray-100 shadow-sm">
            <div class="w-2 h-2 bg-[#22c55e] rounded-full mr-2 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-[#475569]">
                <?= htmlspecialchars($role) ?>
            </span>
        </div>

    </div>
</header>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
}
</script>
