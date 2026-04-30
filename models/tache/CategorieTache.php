<?php

namespace Models\Tache;

use PDO;

class CategorieTache
{
    private PDO $conn;
    private string $table = "categorie_tache";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // =========================================================
    // TOUTES LES CATÉGORIES ACTIVES
    // =========================================================
    public function readAll(): array
    {
        $sql  = "SELECT * FROM {$this->table} WHERE actif = 1 ORDER BY nom ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // FIND BY ID
    // =========================================================
    public function findById(int $id): array|false
    {
        $sql  = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CRÉER UNE CATÉGORIE
    // =========================================================
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                    (nom, icone, description, couleur, actif)
                VALUES
                    (:nom, :icone, :description, :couleur, 1)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nom'         => htmlspecialchars(trim($data['nom'])),
            ':icone'       => $data['icone']       ?? null,
            ':description' => $data['description'] ?? null,
            ':couleur'     => $data['couleur']      ?? '#0d9488',
        ]);
    }

    // =========================================================
    // MODIFIER UNE CATÉGORIE
    // =========================================================
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nom         = :nom,
                    icone       = :icone,
                    description = :description,
                    couleur     = :couleur
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nom'         => htmlspecialchars(trim($data['nom'])),
            ':icone'       => $data['icone']       ?? null,
            ':description' => $data['description'] ?? null,
            ':couleur'     => $data['couleur']      ?? '#0d9488',
            ':id'          => $id,
        ]);
    }

    // =========================================================
    // DÉSACTIVER (soft delete)
    // =========================================================
    public function desactiver(int $id): bool
    {
        $sql  = "UPDATE {$this->table} SET actif = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}