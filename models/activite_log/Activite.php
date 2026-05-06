<?php

namespace Models\Activite_log;

use PDO;

class Activite
{
    private PDO $conn;
    private string $table = "activite_log";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Enregistre une activité dans le log.
     */
    public function log($auteur_id, $type_action, $description, $lien = null)
    {
        $query = "INSERT INTO {$this->table} (auteur_id, type_action, description, lien, date_action) 
                  VALUES (:auteur_id, :type_action, :description, :lien, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':auteur_id' => $auteur_id,
            ':type_action' => $type_action,
            ':description' => $description,
            ':lien' => $lien
        ]);
    }

    /**
     * Récupère les dernières activités.
     */
    public function getLatest($limit = 10)
    {
        $query = "SELECT l.*, u.nom, u.prenom 
                  FROM {$this->table} l
                  LEFT JOIN users u ON l.auteur_id = u.id
                  ORDER BY l.date_action DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
