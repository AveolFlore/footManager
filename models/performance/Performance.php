<?php

namespace Models\Performance;

use PDO;

class Performance
{
    private PDO $conn;
    private string $table = "performance";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (seance_id, joueur_id, buts, passes, date_enregistrement) 
                  VALUES (:seance_id, :joueur_id, :buts, :passes, CURDATE())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':seance_id' => $data['seance_id'],
            ':joueur_id' => $data['joueur_id'],
            ':buts' => $data['buts'],
            ':passes' => $data['passes']
        ]);
    }

    public function getRanking($month = null, $year = null)
    {
        $query = "SELECT u.nom, u.prenom, u.photo_profil, SUM(p.buts) as total_buts, SUM(p.passes) as total_passes, SUM(p.points_total) as score 
                  FROM {$this->table} p
                  JOIN users u ON p.joueur_id = u.id
                  WHERE 1=1";
        
        $params = [];
        if ($month) {
            $query .= " AND MONTH(p.date_enregistrement) = ?";
            $params[] = $month;
        }
        if ($year) {
            $query .= " AND YEAR(p.date_enregistrement) = ?";
            $params[] = $year;
        }

        $query .= " GROUP BY p.joueur_id ORDER BY score DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
