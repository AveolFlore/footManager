<?php

namespace Core;

class Paginator
{
    private int $totalItems;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;
    private int $offset;

    public const DEFAULT_ITEMS_PER_PAGE = 5;
    public const MIN_ITEMS_FOR_PAGINATION = 5;

    /**
     * @param int $totalItems Nombre total d'éléments
     * @param int $itemsPerPage Nombre d'éléments par page
     * @param int|null $currentPage Page actuelle (si null, récupérée depuis $_GET)
     */
    public function __construct(int $totalItems, int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE, ?int $currentPage = null)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = max(1, $itemsPerPage);
        $this->currentPage = $this->sanitizePage($currentPage);
        $this->calculate();
    }

    /**
     * Nettoie et valide le numéro de page
     */
    private function sanitizePage(?int $page): int
    {
        if ($page === null) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        }
        return max(1, $page);
    }

    /**
     * Calcule le nombre total de pages et l'offset
     */
    private function calculate(): void
    {
        $this->totalPages = max(1, (int)ceil($this->totalItems / $this->itemsPerPage));
        $this->currentPage = min($this->currentPage, $this->totalPages);
        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;
    }

    // Getters
    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function needsPagination(): bool
    {
        return $this->totalItems > self::MIN_ITEMS_FOR_PAGINATION;
    }

    /**
     * Retourne un tableau des données pour la vue
     */
    public function toArray(): array
    {
        return [
            'page' => $this->currentPage,
            'totalPages' => $this->totalPages,
            'totalItems' => $this->totalItems,
            'offset' => $this->offset,
            'limit' => $this->itemsPerPage
        ];
    }

    /**
     * Exporte les variables pour être utilisées dans la vue
     */
    public function exportToView(): void
    {
        extract($this->toArray());
    }
}
