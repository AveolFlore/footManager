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

<style>
    .sidebar-nav-link {
        display: block;
        padding: 12px 16px;
        color: #cbd5e1 !important; /* Gris clair text-muted */
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .sidebar-nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff !important;
        transform: translateX(4px);
    }

    .sidebar-nav-link.active {
        background: rgba(0, 82, 255, 0.25) !important; /* Bleu Blue lock transparent */
        color: #38bdf8 !important; /* Cyan éclatant */
        font-weight: 700;
        border-left: 4px solid #0052ff;
        padding-left: 12px; /* Compense la bordure */
    }
</style>

<aside style="
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: rgba(15, 23, 42, 0.8); /* Fond sombre assorti aux panels */
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    padding: 30px 24px;
    overflow-y: auto;
    z-index: 100;
">
    <h2 style="
        margin-bottom: 35px; 
        font-size: 22px; 
        font-weight: 900; 
        color: #ffffff; 
        text-transform: uppercase; 
        letter-spacing: -0.5px;
    ">
        FC <span style="color: #0052ff;">Blue Lock</span>
    </h2>
    
    <nav style="display: flex; flex-direction: column; gap: 6px;">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= $item['route'] ?>" 
               class="sidebar-nav-link <?= isActive($item['route'], $currentPage) ? 'active' : '' ?>">
                <?= $item['label'] ?>
            </a>
        <?php endforeach; ?>

        <?php if ($_SESSION['user']['role'] === 'president'): ?>
            <div style="margin-top: 28px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                <strong style="
                    display: block; 
                    margin-bottom: 12px; 
                    font-size: 11px; 
                    text-transform: uppercase; 
                    letter-spacing: 1px; 
                    color: #64748b;
                    padding-left: 12px;
                ">
                    Gestion
                </strong>
                <?php foreach ($adminItems as $item): ?>
                    <a href="<?= $item['route'] ?>" 
                       class="sidebar-nav-link <?= isActive($item['route'], $currentPage) ? 'active' : '' ?>">
                        <?= $item['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>
</aside>