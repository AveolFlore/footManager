<?php

namespace Models\Equipe;

use PDO;

class Equipe
{
    private PDO $conn;
    private string $table = "equipe";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMembers($equipe_id)
    {
        $query = "SELECT * FROM users WHERE equipe_id = :equipe_id AND statut = 'valide'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':equipe_id' => $equipe_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
