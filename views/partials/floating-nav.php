<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/notifications/Notification.php';

use Config\Database;
use Models\Notifications\Notification;

$currentPage = $_SERVER['REQUEST_URI'];
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

$menuItems = [
    [
        'label' => 'Tableau de bord',
        'route' => '/',
        'icon' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'
    ],
    [
        'label' => 'Joueurs',
        'route' => '/page-admin',
        'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'
    ],
    [
        'label' => 'Équipes',
        'route' => '/admin-team',
        'icon' => 'M12 2a5 5 0 1 0 5 5a5 5 0 0 0-5-5zm0 2a3 3 0 1 1-3 3a3 3 0 0 1 3-3zm0 8c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z'
    ],
    [
        'label' => 'Matchs',
        'route' => '/page-match',
        'icon' => 'M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 15h-2v-2h2zm0-4h-2V7h2z'
    ],
    [
        'label' => 'Convocations',
        'route' => '/page-convocation',
        'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2zm0-4h-2V7h2z'
    ],
    [
        'label' => 'Présences',
        'route' => '/presence',
        'icon' => 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'
    ],
    [
        'label' => 'Sanctions',
        'route' => '/page-sanction',
        'icon' => 'M12 2L1 21h22L12 2zm0 3.83L19.13 19H4.87L12 5.83zM11 10h2v4h-2zm0 6h2v2h-2z'
    ],
    [
        'label' => 'Classement',
        'route' => '/page-classement',
        'icon' => 'M7.5 21H2V9h5.5v12zm14.25-14.74l-3.37-1.77a.997.997 0 0 0-1.26.65l-.76 2.29-2.29-.76a.997.997 0 0 0-.65 1.26l1.77 3.37-2.29.76c-.13.04-.24.13-.31.25a.997.997 0 0 0 .96 1.34l3.37-1.77 1.77 3.37c.22.41.81.57 1.26.35l.12-.06.06-.12 2.29-6.87a1 1 0 0 0-.65-1.26z'
    ],
    [
        'label' => 'Finances',
        'route' => '/page-finance',
        'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.83v-1.91a5.54 5.54 0 0 1-2.6-1.17l.99-.99a4.01 4.01 0 0 0 5.88 0l.99.99a5.54 5.54 0 0 1-2.43 1.17zM17 11h-4V7h-2v4H7v2h4v4h2v-4h4v-2z'
    ],
    [
        'label' => 'Galerie',
        'route' => '/page-galery',
        'icon' => 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'
    ]
];

$adminItems = [
    [
        'label' => 'Administration',
        'route' => '/page-admin',
        'icon' => 'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z'
    ]
];
?>

<style>
    :root {
        --nav-blue: #0052ff;
        --nav-cyan: #38bdf8;
        --nav-purple: #7c3aed;
        --nav-glass: rgba(15, 23, 42, 0.9);
        --nav-border: rgba(255, 255, 255, 0.15);
        --nav-text: #ffffff;
    }

    .top-mini-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 99;
        padding: 12px 24px;
        background: var(--nav-glass);
        backdrop-filter: blur(20px) saturate(180%);
        border-bottom: 1px solid var(--nav-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mini-bar-logo {
        font-size: 18px;
        font-weight: 900;
        color: var(--nav-text);
        text-transform: uppercase;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mini-bar-logo span {
        color: var(--nav-blue);
    }

    .mini-bar-user {
        display: flex;
        align-items: center;
        gap: 16px;
        color: var(--nav-text);
        font-size: 14px;
        font-weight: 600;
    }

    .mini-bar-user a {
        color: var(--nav-cyan);
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid rgba(56, 189, 248, 0.3);
        transition: all 0.2s;
    }

    .mini-bar-user a:hover {
        background: rgba(56, 189, 248, 0.15);
    }

    .notification-badge {
        background: var(--nav-cyan);
        color: #0f172a;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 10px;
    }

    /* Electron Nucleus Menu */
    .nucleus-menu {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 1000;
    }

    .nucleus-core {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--nav-blue) 0%, var(--nav-purple) 100%);
        border-radius: 50%;
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0, 82, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 1001;
        position: relative;
        outline: none;
    }

    .nucleus-core:hover {
        transform: scale(1.15);
        box-shadow: 0 16px 40px rgba(0, 82, 255, 0.55);
    }

    .nucleus-core.open {
        transform: rotate(135deg) scale(1.1);
    }

    .nucleus-core svg {
        width: 36px;
        height: 36px;
        color: white;
        transition: transform 0.3s;
    }

    .electron-orbit {
        position: absolute;
        bottom: 35px;
        right: 35px;
        width: 260px;
        height: 260px;
        pointer-events: none;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0;
    }

    .electron-orbit.open {
        pointer-events: auto;
        opacity: 1;
    }

    .electron {
        position: absolute;
        width: 56px;
        height: 56px;
        background: var(--nav-glass);
        backdrop-filter: blur(15px);
        border: 1px solid var(--nav-border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transform: translate(-50%, -50%) scale(0);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        outline: none;
    }

    .electron-orbit.open .electron {
        transform: translate(-50%, -50%) scale(1);
    }

    .electron:hover {
        background: rgba(0, 82, 255, 0.35);
        border-color: var(--nav-blue);
        transform: translate(-50%, -50%) scale(1.25) !important;
        box-shadow: 0 10px 28px rgba(0, 82, 255, 0.45);
    }

    .electron.active {
        background: rgba(0, 82, 255, 0.45);
        border-color: var(--nav-cyan);
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
    }

    .electron svg {
        width: 24px;
        height: 24px;
        color: var(--nav-text);
    }

    .electron-tooltip {
        position: absolute;
        right: 70px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        color: var(--nav-text);
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border: 1px solid var(--nav-border);
        opacity: 0;
        transform: translateX(10px);
        transition: all 0.2s;
        pointer-events: none;
    }

    .electron:hover .electron-tooltip {
        opacity: 1;
        transform: translateX(0);
    }

    /* Admin section */
    .admin-separator {
        position: absolute;
        bottom: 150%;
        right: 0;
        background: var(--nav-glass);
        backdrop-filter: blur(15px);
        border: 1px solid var(--nav-border);
        border-radius: 16px;
        padding: 8px 16px;
        opacity: 0;
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform: translateY(15px) scale(0.8);
    }

    .electron-orbit.open .admin-separator {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
        transition-delay: 0.25s;
    }

    .admin-separator a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--nav-text);
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        padding: 8px 12px;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .admin-separator a:hover {
        background: rgba(0, 82, 255, 0.3);
    }

    .admin-separator svg {
        width: 20px;
        height: 20px;
        color: var(--nav-cyan);
    }

    @media (max-width: 768px) {
        .nucleus-menu {
            bottom: 20px;
            right: 20px;
        }

        .electron-orbit {
            width: 220px;
            height: 220px;
        }

        .electron {
            width: 48px;
            height: 48px;
        }

        .electron svg {
            width: 20px;
            height: 20px;
        }

        .top-mini-bar {
            padding: 10px 16px;
        }

        .mini-bar-user span {
            display: none;
        }
    }
</style>

<div class="top-mini-bar">
    <div class="mini-bar-logo">
        FC <span>Blue Lock</span>
    </div>
    <div class="mini-bar-user">
        <?php if ($unread_count > 0): ?>
            <span class="notification-badge"><?= $unread_count ?> notif(s)</span>
        <?php endif; ?>
        <span><?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?> (<?= htmlspecialchars($role) ?>)</span>
        <a href="/auth-logout">Déconnexion</a>
    </div>
</div>

<div class="nucleus-menu">
    <button class="nucleus-core" id="nucleusToggle" aria-expanded="false" aria-haspopup="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
    </button>
    <div class="electron-orbit" id="electronOrbit" aria-hidden="true">
        <?php 
        $totalItems = count($menuItems);
        foreach ($menuItems as $index => $item): 
            // Calculate position on perfect circle (as percentage)
            $angle = ($index / $totalItems) * 2 * M_PI - M_PI / 2; // Start from top (12 o'clock)
            $orbitRadiusPercent = 40; // Radius in % of orbit container size (container is 260px wide, so ~40% radius = 104px, which is good
            $x = 50 + $orbitRadiusPercent * cos($angle);
            $y = 50 + $orbitRadiusPercent * sin($angle);
        ?>
            <a href="<?= $item['route'] ?>" 
               class="electron <?= strpos($currentPage, $item['route']) !== false ? 'active' : '' ?>"
               style="left: <?= $x ?>%; top: <?= $y ?>%; transition-delay: <?= $index * 0.03 ?>s;"
               aria-label="<?= htmlspecialchars($item['label']) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="<?= $item['icon'] ?>"></path>
                </svg>
                <span class="electron-tooltip"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'president'): ?>
            <div class="admin-separator">
                <?php foreach ($adminItems as $item): ?>
                    <a href="<?= $item['route'] ?>" class="<?= strpos($currentPage, $item['route']) !== false ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="<?= $item['icon'] ?>"></path>
                        </svg>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const toggle = document.getElementById('nucleusToggle');
    const orbit = document.getElementById('electronOrbit');

    toggle.addEventListener('click', () => {
        const isOpen = toggle.classList.toggle('open');
        orbit.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen);
        orbit.setAttribute('aria-hidden', !isOpen);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nucleus-menu')) {
            toggle.classList.remove('open');
            orbit.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            orbit.setAttribute('aria-hidden', 'true');
        }
    });

    // Close with Escape key for accessibility
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggle.classList.contains('open')) {
            toggle.classList.remove('open');
            orbit.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            orbit.setAttribute('aria-hidden', 'true');
            toggle.focus();
        }
    });
</script>
