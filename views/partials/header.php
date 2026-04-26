<!-- HEADER -->
<header class="w-full h-16 bg-white border-b flex items-center justify-between px-4 md:px-6">

    <!-- LEFT -->
    <div class="flex items-center gap-4">

        <!-- BURGER (mobile only) -->
        <button onclick="toggleSidebar()" class="md:hidden bg-green-600 text-white p-2 rounded-lg">
            ☰
        </button>

        <!-- TITLE -->
        <div>
            <h1 class="text-lg font-semibold text-gray-800">Accueil</h1>
            <p class="text-sm text-gray-500 hidden sm:block">
                Bienvenue, Jean Dupont
            </p>
        </div>

    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-4">

        <!-- NOTIF -->
        <div class="relative">
            <button class="text-gray-600 text-xl">🔔</button>
            <span class="absolute -top-1 -right-1 w-2 h-2 bg-orange-500 rounded-full"></span>
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