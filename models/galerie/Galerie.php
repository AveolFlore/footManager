<?php

namespace Models\Galerie;

use PDO;

class Galerie
{
    private PDO $conn;
    private string $table = "galerie";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT g.*, s.lieu, s.date as seance_date 
                  FROM {$this->table} g
                  LEFT JOIN match_seance s ON g.seance_id = s.id
                  ORDER BY g.date_upload DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (seance_id, titre, image_url, description) 
                  VALUES (:seance_id, :titre, :image_url, :description)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':seance_id' => $data['seance_id'] ?? null,
            ':titre' => $data['titre'],
            ':image_url' => $data['image_url'],
            ':description' => $data['description'] ?? ''
        ]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
