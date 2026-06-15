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
        'route' => '/'
    ],
    [
        'label' => 'Joueurs',
        'route' => '/page-admin'
    ],
    [
        'label' => 'Équipes',
        'route' => '/admin-team'
    ],
    [
        'label' => 'Matchs',
        'route' => '/page-match'
    ],
    [
        'label' => 'Convocations',
        'route' => '/page-convocation'
    ],
    [
        'label' => 'Présences',
        'route' => '/presence'
    ],
    [
        'label' => 'Sanctions',
        'route' => '/page-sanction'
    ],
    [
        'label' => 'Classement',
        'route' => '/page-classement'
    ],
    [
        'label' => 'Finances',
        'route' => '/page-finance'
    ],
    [
        'label' => 'Règlements',
        'route' => '/page-rule'
    ],
    [
        'label' => 'Galerie',
        'route' => '/page-galery'
    ]
];

$adminItems = [
    [
        'label' => 'Administration',
        'route' => '/page-admin'
    ]
];
?>
<aside class="sidebar">
    <h2>FC Blue Lock</h2>
    <nav>
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= $item['route'] ?>" class="<?= isActive($item['route'], $currentPage) ? 'active' : '' ?>">
                <?= $item['label'] ?>
            </a>
        <?php endforeach; ?>

        <?php if ($_SESSION['user']['role'] === 'president'): ?>
            <div style="margin-top: 24px; border-top: 1px solid #ddd; padding-top: 16px;">
                <strong>Administration</strong>
                <?php foreach ($adminItems as $item): ?>
                    <a href="<?= $item['route'] ?>" class="<?= isActive($item['route'], $currentPage) ? 'active' : '' ?>" style="display: block; margin-top: 8px;">
                        <?= $item['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>
</aside>
