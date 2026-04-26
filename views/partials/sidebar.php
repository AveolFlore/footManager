<!-- OVERLAY (mobile) -->
<div id="overlay" onclick="toggleSidebar()" 
     class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
class="fixed md:static top-0 left-0 z-50 w-64 h-full bg-green-800 text-white
transform -translate-x-full md:translate-x-0 transition duration-300 flex flex-col">

    <!-- HEADER CLUB -->
    <div class="p-5 border-b border-green-700">
        <div class="flex items-center gap-3">
            <div class="text-2xl">🦁</div>
            <div>
                <h2 class="font-semibold">FC Green Lions</h2>
                <p class="text-sm text-green-200">Unis pour la victoire</p>
            </div>
        </div>
    </div>

     <!-- <a href="/">Home</a>
    <a href="/page-team">Team</a>
    <a href="/page-match">Match</a>
    <a href="page-login">Login</a>
    <a href="page-register">Register</a>
    <a href="auth-logout">Logout</a> -->

    <!-- MENU -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">

        <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-green-700">
            Accueil
        </a>

        <a href="page-team" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Équipe
        </a>

        <a href="page-traning" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Entraînements
        </a>

        <a href="page-match" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Matchs
        </a>

        <a href="page-rule" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Règlement
        </a>

        <a href="page-finance" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Cotisations
        </a>

        <a href="page-galery" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Galerie
        </a>

        <a href="page-classement" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Classement
        </a>
        <a href="page-login" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            login
        </a>
        <a href="page-register" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Register
        </a>
        <a href="page-admin" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700">
            Admin
        </a>

    </nav>

    <!-- USER -->
    <div class="p-4 border-t border-green-700">
        <div class="flex items-center gap-3 bg-green-700 p-3 rounded-lg mb-3">
            <div>👤</div>
            <div>
                <p class="text-sm font-medium">Jean Dupont</p>
                <p class="text-xs text-green-200">Président</p>
            </div>
        </div>

        <a href="auth-logout" class="text-sm text-green-200 hover:text-white flex items-center gap-2">
            Déconnexion
        </a>
    </div>

</aside>