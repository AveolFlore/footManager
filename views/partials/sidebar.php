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
        'label' => 'Tableau de bord',
        'route' => '/',
        'icon' => 'fa-solid fa-gauge-high'
    ],
    [
        'label' => 'Joueurs',
        'route' => '/page-admin',
        'icon' => 'fa-solid fa-users'
    ],
    [
        'label' => 'Équipes',
        'route' => '/admin-team',
        'icon' => 'fa-solid fa-people-group'
    ],
    [
        'label' => 'Matchs',
        'route' => '/page-match',
        'icon' => 'fa-solid fa-futbol'
    ],
    [
        'label' => 'Convocations',
        'route' => '/page-convocation',
        'icon' => 'fa-solid fa-clipboard-list'
    ],
    [
        'label' => 'Présences',
        'route' => '/presence',
        'icon' => 'fa-solid fa-check-to-slot'
    ],
    [
        'label' => 'Sanctions',
        'route' => '/page-sanction',
        'icon' => 'fa-solid fa-scale-balanced'
    ],
    [
        'label' => 'Classement',
        'route' => '/page-classement',
        'icon' => 'fa-solid fa-chart-column'
    ],
    [
        'label' => 'Finances',
        'route' => '/page-finance',
        'icon' => 'fa-solid fa-wallet'
    ],
    [
        'label' => 'Règlements',
        'route' => '/page-rule',
        'icon' => 'fa-solid fa-file-contract'
    ],
    [
        'label' => 'Galerie',
        'route' => '/page-galery',
        'icon' => 'fa-solid fa-images'
    ],
    [
        'label' => 'Paramètres',
        'route' => '#',
        'icon' => 'fa-solid fa-gear'
    ]
];

$adminItems = [
    [
        'label' => 'Administration',
        'route' => '/page-admin',
        'icon' => 'fa-solid fa-shield-halved'
    ]
];
?>

<!-- OVERLAY (mobile) -->
<div id="overlay" onclick="toggleSidebar()"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
    class="fixed md:static top-0 left-0 z-50 w-72 min-h-screen bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white shadow-2xl shadow-slate-900/50 -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col">

    <!-- HEADER WITH BLUE LOCK LOGO -->
    <div class="px-7 py-8 border-b border-slate-800 flex-shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center shadow-lg shadow-green-900/30 overflow-hidden">
                <img src="/images/blue_lock_logo.png" alt="Blue Lock Logo" class="w-10 h-10 object-contain">
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-extrabold tracking-wide">
                    <span class="text-green-400">FC</span> Blue Lock
                </h2>
                <p class="text-xs text-slate-400 mt-1 font-medium uppercase tracking-widest">Management System</p>
            </div>
        </div>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-5 py-6 space-y-1 overflow-y-auto">
        <div class="mb-4 px-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
            Général
        </div>

        <?php foreach ($menuItems as $item): ?>
            <a href="<?= $item['route'] ?>"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-200 group
               <?= isActive($item['route'], $currentPage)
                    ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-900/30'
                    : 'hover:bg-slate-800 text-slate-300 hover:text-white' ?>">
                <span class="text-xl w-6 text-center transition-all <?= isActive($item['route'], $currentPage) ? 'text-white' : 'text-slate-400 group-hover:text-green-400' ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                </span>
                <span class="font-semibold"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>

        <?php if ($_SESSION['user']['role'] === 'president'): ?>
            <div class="mt-8 pt-4 border-t border-slate-800">
                <div class="mb-4 px-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Administration
                </div>
                <?php foreach ($adminItems as $item): ?>
                    <a href="<?= $item['route'] ?>"
                        class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-200 group
                       <?= isActive($item['route'], $currentPage)
                            ? 'bg-gradient-to-r from-yellow-600 to-orange-600 text-white shadow-lg shadow-orange-900/30'
                            : 'hover:bg-slate-800 text-slate-300 hover:text-white' ?>">
                        <span class="text-xl w-6 text-center transition-all <?= isActive($item['route'], $currentPage) ? 'text-white' : 'text-slate-400 group-hover:text-yellow-400' ?>">
                            <i class="<?= $item['icon'] ?>"></i>
                        </span>
                        <span class="font-semibold"><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>

    <!-- USER PROFILE & LOGOUT -->
    <div class="px-5 py-6 border-t border-slate-800 flex-shrink-0">
        <div class="bg-slate-800/70 rounded-2xl p-5 mb-4 border border-slate-700">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-xl font-extrabold shadow-lg shadow-green-900/30">
                    <?= strtoupper(substr($_SESSION['user']['nom'], 0, 1) . substr($_SESSION['user']['prenom'], 0, 1)) ?>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-white">
                        <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                        <?= htmlspecialchars($_SESSION['user']['prenom']) ?>
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium uppercase">
                        <?= strtoupper($_SESSION['user']['role']) ?>
                    </p>
                </div>
            </div>
            <div class="bg-slate-700/70 rounded-xl p-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Last login</span>
                    <span class="text-green-400 font-semibold">Today</span>
                </div>
            </div>
        </div>

        <!-- LOGOUT -->
        <a href="/auth-logout"
            class="flex items-center justify-center gap-3 w-full px-4 py-3.5 rounded-2xl bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-600 hover:to-slate-700 transition-all duration-200 text-sm font-semibold shadow-lg border border-slate-700">
            <span class="text-lg"><i class="fas fa-right-from-bracket"></i></span>
            Déconnexion
        </a>
    </div>

</aside>