<?php
/**
 * Composant de Pagination Blue Lock
 * @var int $page Page actuelle
 * @var int $totalPages Nombre total de pages
 */
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;

if ($totalPages <= 1) return;

// Fonction pour reconstruire l'URL avec les filtres existants
function getPagiUrl($pageNum) {
    $params = $_GET;
    $params['page'] = $pageNum;
    return '?' . http_build_query($params);
}
?>
<nav class="flex items-center justify-center gap-2 mt-10 mb-6 font-mono">
    <?php if ($page > 1): ?>
        <a href="<?= getPagiUrl($page - 1) ?>" class="px-3 py-2 border border-slate-800 bg-slate-950/40 text-slate-500 hover:border-cyan-500 hover:text-cyan-400 transition-all active:scale-95" title="Précédent">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <span class="px-4 py-2 border border-cyan-500 bg-cyan-500/10 text-cyan-400 font-black shadow-[0_0_15px_rgba(6,182,212,0.2)]">
                <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
            </span>
        <?php else: ?>
            <a href="<?= getPagiUrl($i) ?>" class="px-4 py-2 border border-slate-800 bg-slate-950/40 text-slate-500 hover:border-slate-700 hover:text-slate-200 transition-all">
                <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
            </a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= getPagiUrl($page + 1) ?>" class="px-3 py-2 border border-slate-800 bg-slate-950/40 text-slate-500 hover:border-cyan-500 hover:text-cyan-400 transition-all active:scale-95" title="Suivant">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</nav>