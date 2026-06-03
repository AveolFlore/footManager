<?php
require_once __DIR__ . '/../../middleware/Role.php';
requireLogin();

$currentPage = $_SERVER['REQUEST_URI'];

function isActive($route, $currentPage)
{
    if ($route === '/') {
        return $currentPage === '/';
    }
    return strpos($currentPage, $route) !== false;
}

$menuItems = [
    [
        'label' => 'Accueil',
        'route' => '/',
        'icon' => '🏠'
    ],
    [
        'label' => 'Équipes',
        'route' => '/admin-team',
        'icon' => '👥'
    ],
    [
        'label' => 'Entraînements',
        'route' => '/page-traning',
        'icon' => '⚽'
    ],
    [
        'label' => 'Matchs',
        'route' => '/page-match',
        'icon' => '🏆'
    ],
    [
        'label' => 'Présences',
        'route' => '/presence',
        'icon' => '✅'
    ],
    [
        'label' => 'Sanctions',
        'route' => '/page-sanction',
        'icon' => '⚖️'
    ],
    [
        'label' => 'Convocations',
        'route' => '/page-convocation',
        'icon' => '📋'
    ],
    [
        'label' => 'Règlements',
        'route' => '/page-rule',
        'icon' => '📜'
    ],
    [
        'label' => 'Finances',
        'route' => '/page-finance',
        'icon' => '💰'
    ],
    [
        'label' => 'Classement',
        'route' => '/page-classement',
        'icon' => '📊'
    ],
    [
        'label' => 'Galerie',
        'route' => '/page-galery',
        'icon' => '📷'
    ]
];

$adminItems = [
    [
        'label' => 'Administration',
        'route' => '/page-admin',
        'icon' => '⚙️'
    ]
];
?>

<!-- OVERLAY (mobile) -->
<div id="overlay" onclick="toggleSidebar()"
    class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
    class="fixed md:static top-0 left-0 z-50 w-64 min-h-screen bg-gradient-to-b from-green-900 to-green-950 text-white shadow-2xl -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col">

    <!-- HEADER -->
    <div class="px-6 py-6 border-b border-green-800/50 flex-shrink-0">
        <h2 class="text-xl font-bold tracking-wide">
            <span class="text-green-400">FC</span> Green Lions
        </h2>
        <p class="text-sm text-green-300/80 mt-1">Gestion du club</p>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-sm">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= $item['route'] ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200
               <?= isActive($item['route'], $currentPage)
                    ? 'bg-green-700/70 text-white shadow-inner'
                    : 'hover:bg-green-800/60 text-green-100' ?>">
                <span class="text-xl"><?= $item['icon'] ?></span>
                <span class="font-medium"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>

        <?php if ($_SESSION['user']['role'] === 'president'): ?>
            <div class="mt-6 pt-4 border-t border-green-800/50">
                <?php foreach ($adminItems as $item): ?>
                    <a href="<?= $item['route'] ?>"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200
                       <?= isActive($item['route'], $currentPage)
                            ? 'bg-yellow-600/80 text-white shadow-inner'
                            : 'hover:bg-yellow-700/60 text-yellow-100' ?>">
                        <span class="text-xl"><?= $item['icon'] ?></span>
                        <span class="font-medium"><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>

    <!-- USER -->
    <div class="px-4 py-5 border-t border-green-800/50 flex-shrink-0">
        <div class="bg-green-800/50 rounded-2xl p-4 mb-4 backdrop-blur-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-lg font-bold shadow-lg">
                    <?= strtoupper(substr($_SESSION['user']['nom'], 0, 1) . substr($_SESSION['user']['prenom'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-semibold">
                        <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                        <?= htmlspecialchars($_SESSION['user']['prenom']) ?>
                    </p>
                    <p class="text-xs text-green-300/80">
                        <?= strtoupper($_SESSION['user']['role']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- LOGOUT -->
        <a href="/auth-logout"
            class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 transition duration-200 text-sm font-semibold shadow-lg hover:shadow-xl">
            <span>🚪</span>
            Déconnexion
        </a>
    </div>

</aside>