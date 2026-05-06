<?php

namespace Models\Match_seance;

use PDO;

class MatchEntity
{
    private PDO $conn;
    private string $table = "match_seance";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (type, date, lieu, description, statut, createur_id) 
                  VALUES (:type, :date, :lieu, :description, :statut, :createur_id)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':type' => $data['type'],
            ':date' => $data['date'],
            ':lieu' => $data['lieu'],
            ':description' => $data['description'] ?? null,
            ':statut' => $data['statut'] ?? 'planifie',
            ':createur_id' => $data['createur_id']
        ]);
    }

    public function readAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFindId($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatut($id, $statut)
    {
        $query = "UPDATE {$this->table} SET statut = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$statut, $id]);
    }
}
