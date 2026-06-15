<?php
/**
 * Composant de Pagination réutilisable Blue Lock
 * * @param int $page Page actuelle (par défaut 1)
 * @param int $totalPages Nombre total de pages (par défaut 1)
 * @param string $baseUrl URL de base (facultatif, par défaut la page actuelle)
 * @param array $params Paramètres GET supplémentaires à conserver (optionnel)
 */

$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '';
$params = $params ?? $_GET;

if ($totalPages <= 1) return;

// Sécurité : éviter la collision si le composant est chargé plusieurs fois
if (!function_exists('getPaginationUrl')) {
    function getPaginationUrl($pageNum, $params, $baseUrl = '')
    {
        $params['page'] = $pageNum;
        $queryString = http_build_query($params);
        return $baseUrl . '?' . $queryString;
    }
}

// Calculer les pages à afficher (éviter trop de pages)
$startPage = max(1, $page - 2);
$endPage = min($totalPages, $page + 2);

// S'assurer qu'on a au moins 5 pages visibles si possible
if ($endPage - $startPage < 4) {
    if ($startPage === 1) {
        $endPage = min(5, $totalPages);
    } elseif ($endPage === $totalPages) {
        $startPage = max(1, $totalPages - 4);
    }
}
?>
<nav class="flex items-center justify-center flex-wrap gap-2 mt-8 mb-4 font-mono select-none" role="navigation" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a href="<?= getPaginationUrl(1, $params, $baseUrl) ?>" class="px-3 py-2 border rounded-xl border-slate-200 bg-white text-slate-600 hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-500 dark:hover:border-cyan-500 dark:hover:text-cyan-400 transition-all active:scale-95" title="Première page">
            <i class="fas fa-angle-double-left"></i>
        </a>
    <?php endif; ?>

    <?php if ($page > 1): ?>
        <a href="<?= getPaginationUrl($page - 1, $params, $baseUrl) ?>" class="px-3 py-2 border rounded-xl border-slate-200 bg-white text-slate-600 hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-500 dark:hover:border-cyan-500 dark:hover:text-cyan-400 transition-all active:scale-95" title="Précédent">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>

    <?php if ($startPage > 1): ?>
        <span class="px-2 py-2 text-slate-400 dark:text-slate-600">...</span>
    <?php endif; ?>

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php if ($i == $page): ?>
            <span class="px-4 py-2 border rounded-xl border-cyan-500 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-black shadow-[0_0_15px_rgba(6,182,212,0.15)] dark:shadow-[0_0_15px_rgba(6,182,212,0.2)]" aria-current="page">
                <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
            </span>
        <?php else: ?>
            <a href="<?= getPaginationUrl($i, $params, $baseUrl) ?>" class="px-4 py-2 border rounded-xl border-slate-200 bg-white text-slate-500 hover:border-slate-400 hover:text-slate-800 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-500 dark:hover:border-slate-700 dark:hover:text-slate-200 transition-all" aria-label="Page <?= $i ?>">
                <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
            </a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($endPage < $totalPages): ?>
        <span class="px-2 py-2 text-slate-400 dark:text-slate-600">...</span>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= getPaginationUrl($page + 1, $params, $baseUrl) ?>" class="px-3 py-2 border rounded-xl border-slate-200 bg-white text-slate-600 hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-500 dark:hover:border-cyan-500 dark:hover:text-cyan-400 transition-all active:scale-95" title="Suivant">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= getPaginationUrl($totalPages, $params, $baseUrl) ?>" class="px-3 py-2 border rounded-xl border-slate-200 bg-white text-slate-600 hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-500 dark:hover:border-cyan-500 dark:hover:text-cyan-400 transition-all active:scale-95" title="Dernière page">
            <i class="fas fa-angle-double-right"></i>
        </a>
    <?php endif; ?>

    <span class="text-xs text-slate-400 dark:text-slate-500 ml-2 font-semibold whitespace-nowrap">
        Page <?= $page ?>/<?= $totalPages ?>
    </span>
</nav>