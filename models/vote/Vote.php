<?php

namespace Models\Vote;

use PDO;

class Vote
{
    private PDO $conn;
    private string $table = "vote";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (reglement_id, joueur_id, choix, date_vote) 
                  VALUES (:reglement_id, :joueur_id, :choix, CURDATE())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':reglement_id' => $data['reglement_id'],
            ':joueur_id' => $data['joueur_id'],
            ':choix' => $data['choix']
        ]);
    }

    public function getResults($reglement_id)
    {
        $query = "SELECT choix, COUNT(*) as total FROM {$this->table} WHERE reglement_id = ? GROUP BY choix";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reglement_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
