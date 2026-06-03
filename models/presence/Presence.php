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
        $query = "SELECT p.*, u.nom, u.prenom, u.numero_maillot, u.poste
                  FROM {$this->table} p
                  JOIN users u ON p.joueur_id = u.id
                  WHERE p.seance_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$seance_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsByJoueur($joueur_id, $mois = null, $annee = null)
    {
        $params = [$joueur_id];
        $query = "SELECT type_presence, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE joueur_id = ?";
        
        if ($mois) {
            $query .= " AND MONTH(date_marquage) = ?";
            $params[] = $mois;
        }
        if ($annee) {
            $query .= " AND YEAR(date_marquage) = ?";
            $params[] = $annee;
        }
        
        $query .= " GROUP BY type_presence";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les séances avec stats de présence
     */
    public function getAllSeancesWithPresence($limit = 20, $offset = 0)
    {
        $query = "SELECT m.*, 
                         COUNT(p.id) as total_presences,
                         SUM(CASE WHEN p.type_presence = 'present' THEN 1 ELSE 0 END) as nb_presents,
                         SUM(CASE WHEN p.type_presence = 'absent' THEN 1 ELSE 0 END) as nb_absents,
                         SUM(CASE WHEN p.type_presence = 'retard' THEN 1 ELSE 0 END) as nb_retards
                  FROM match_seance m
                  LEFT JOIN presence p ON m.id = p.seance_id
                  GROUP BY m.id
                  ORDER BY m.date DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de séances
     */
    public function countAllSeances()
    {
        $query = "SELECT COUNT(*) as total FROM match_seance";
        $stmt = $this->conn->query($query);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Récupère les joueurs avec leurs stats de présence
     */
    public function getJoueursWithStats($mois = null, $annee = null)
    {
        $whereDate = "";
        $params = [];
        if ($mois) {
            $whereDate .= " AND MONTH(p.date_marquage) = :mois";
            $params[':mois'] = $mois;
        }
        if ($annee) {
            $whereDate .= " AND YEAR(p.date_marquage) = :annee";
            $params[':annee'] = $annee;
        }

        $query = "SELECT u.id, u.nom, u.prenom, u.numero_maillot, u.poste,
                         COUNT(p.id) as total_seances,
                         SUM(CASE WHEN p.type_presence = 'present' THEN 1 ELSE 0 END) as nb_presents,
                         SUM(CASE WHEN p.type_presence = 'absent' THEN 1 ELSE 0 END) as nb_absents,
                         SUM(CASE WHEN p.type_presence = 'retard' THEN 1 ELSE 0 END) as nb_retards,
                         SUM(CASE WHEN p.type_presence = 'excuse' THEN 1 ELSE 0 END) as nb_excuses
                  FROM users u
                  LEFT JOIN presence p ON u.id = p.joueur_id $whereDate
                  WHERE u.role = 'joueur' AND u.statut = 'valide'
                  GROUP BY u.id
                  ORDER BY u.nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si une séance a déjà des présences marquées
     */
    public function seanceHasPresences($seance_id)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE seance_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$seance_id]);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    /**
     * Supprime les présences d'une séance (pour modification)
     */
    public function deleteBySeance($seance_id)
    {
        $query = "DELETE FROM {$this->table} WHERE seance_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$seance_id]);
    }
}
