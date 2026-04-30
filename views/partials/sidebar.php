<?php
require_once __DIR__ . '/../../middleware/Role.php';

requireLogin();

// récupérer la route actuelle
$currentPage = $_SERVER['REQUEST_URI'];

function isActive($route, $currentPage)
{
    if ($route === '/') {
    return $currentPage === '/';
}
    return strpos($currentPage, $route) !== false;
}
?>

<!-- OVERLAY (mobile) -->
<div id="overlay" onclick="toggleSidebar()" 
     class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
class="fixed md:static top-0 left-0 z-50 w-64 min-h-screen bg-green-900 text-white">

    <!-- HEADER -->
    <div class="px-6 py-5 border-b border-green-800">
        <h2 class="text-lg font-semibold tracking-wide">FC Green Lions</h2>
        <p class="text-sm text-green-300 mt-1">Gestion du club</p>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto text-sm">

        <a href="/"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('/', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Accueil
        </a>

        <a href="/page-team"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-team', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Équipe
        </a>

        <a href="/page-traning"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-traning', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Entraînements
        </a>

        <a href="/page-match"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-match', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Matchs
        </a>

        <a href="/page-rule"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-rule', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Règlement
        </a>

        <a href="/page-finance"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-finance', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Cotisations
        </a>

        <a href="/page-galery"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-galery', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Galerie
        </a>

        <a href="/page-classement"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-classement', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Classement
        </a>
        <a href="/page-tache"
           class="block px-4 py-2 rounded-lg transition 
           <?= isActive('page-tache', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
            Tâches
        </a>
<!-- Ajout des tâches dans le sidebar par Florence pour pouvoir afficher son travail -->
        <?php if ($_SESSION['user']['role'] === 'president') : ?>
            <a href="/page-admin"
               class="block px-4 py-2 rounded-lg transition 
               <?= isActive('page-admin', $currentPage) ? 'bg-green-700' : 'hover:bg-green-800' ?>">
                Administration
            </a>
        <?php endif; ?>

    </nav>

    <!-- USER -->
    <div class="px-4 py-4 border-t border-green-800">

        <div class="bg-green-800 rounded-lg p-3 mb-3">
            <p class="text-sm font-medium">
                <?= $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom'] ?>
            </p>
            <p class="text-xs text-green-300 mt-1">
                <?= strtoupper($_SESSION['user']['role']) ?>
            </p>
        </div>

        <!-- LOGOUT -->
        <a href="/auth-logout"
           class="block w-full text-center px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 transition text-sm font-medium">
            Déconnexion
        </a>

    </div>

</aside>