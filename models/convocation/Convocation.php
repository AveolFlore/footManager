<?php

namespace Models\Convocation;

use PDO;

class Convocation
{
    private PDO $conn;
    private string $table = "convocation";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (match_id, joueur_id, equipe_match, est_capitaine, numero_maillot) 
                  VALUES (:match_id, :joueur_id, :equipe_match, :est_capitaine, :numero_maillot)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':match_id' => $data['match_id'],
            ':joueur_id' => $data['joueur_id'],
            ':equipe_match' => $data['equipe_match'],
            ':est_capitaine' => $data['est_capitaine'] ?? 0,
            ':numero_maillot' => $data['numero_maillot'] ?? null
        ]);
    }

    public function getByMatch($match_id)
    {
        $query = "SELECT c.*, u.nom, u.prenom 
                  FROM {$this->table} c
                  JOIN users u ON c.joueur_id = u.id
                  WHERE c.match_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$match_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Suggestion des meilleurs joueurs selon score (Perf 60% + Présence 40%)
     */
    public function getSuggestions($limit = 8)
    {
        $query = "SELECT u.id, u.nom, u.prenom,
                  COALESCE(SUM(p.points_total), 0) * 0.6 + 
                  COUNT(CASE WHEN pr.type_presence = 'present' THEN 1 END) * 0.4 AS score
                  FROM users u
                  LEFT JOIN performance p ON p.joueur_id = u.id AND MONTH(p.date_enregistrement) = MONTH(NOW())
                  LEFT JOIN presence pr ON pr.joueur_id = u.id AND MONTH(pr.date_marquage) = MONTH(NOW())
                  WHERE u.statut = 'valide' AND u.role = 'joueur'
                  GROUP BY u.id
                  ORDER BY score DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
