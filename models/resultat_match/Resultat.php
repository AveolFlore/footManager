<?php

namespace Models\Resultat_match;

use PDO;

class Resultat
{
    private PDO $conn;
    private string $table = "resultat_match";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (match_id, buts_equipe_a, buts_equipe_b, saisie_par) 
                  VALUES (:match_id, :buts_equipe_a, :buts_equipe_b, :saisie_par)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':match_id' => $data['match_id'],
            ':buts_equipe_a' => $data['buts_equipe_a'],
            ':buts_equipe_b' => $data['buts_equipe_b'],
            ':saisie_par' => $data['saisie_par']
        ]);
    }

    public function getByMatch($match_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE match_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$match_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
