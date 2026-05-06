<?php

namespace Models\Recompense;

use PDO;

class Recompense
{
    private PDO $conn;
    private string $table = "recompense";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (joueur_id, type, periode, total_points, date_attribution) 
                  VALUES (:joueur_id, :type, :periode, :total_points, CURDATE())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':joueur_id' => $data['joueur_id'],
            ':type' => $data['type'],
            ':periode' => $data['periode'],
            ':total_points' => $data['total_points']
        ]);
    }

    public function getPalmares()
    {
        $query = "SELECT r.*, u.nom, u.prenom FROM {$this->table} r JOIN users u ON r.joueur_id = u.id ORDER BY r.date_attribution DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
