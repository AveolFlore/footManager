<?php

namespace Models\Presence;

use PDO;

class Presence
{
    private PDO $conn;
    private string $table = "presence";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (seance_id, joueur_id, type_presence, marque_par, date_marquage, note) 
                  VALUES (:seance_id, :joueur_id, :type_presence, :marque_par, CURDATE(), :note)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':seance_id' => $data['seance_id'],
            ':joueur_id' => $data['joueur_id'],
            ':type_presence' => $data['type_presence'],
            ':marque_par' => $data['marque_par'],
            ':note' => $data['note'] ?? null
        ]);
        return $this->conn->lastInsertId();
    }

    public function getBySeance($seance_id)
    {
        $query = "SELECT p.*, u.nom, u.prenom 
                  FROM {$this->table} p
                  JOIN users u ON p.joueur_id = u.id
                  WHERE p.seance_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$seance_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsByJoueur($joueur_id)
    {
        $query = "SELECT type_presence, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE joueur_id = ? 
                  GROUP BY type_presence";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$joueur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
