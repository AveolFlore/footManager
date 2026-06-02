<?php

namespace Models\Reglement;

use PDO;

class Reglement
{
    private PDO $conn;
    private string $table = "reglement";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (titre, description, montant_amende, type_infraction, statut, propose_par, date_creation, date_debut_vote) 
                  VALUES (:titre, :description, :montant_amende, :type_infraction, :statut, :propose_par, CURDATE(), NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':montant_amende' => $data['montant_amende'],
            ':type_infraction' => $data['type_infraction'],
            ':statut' => $data['statut'] ?? 'reflexion',
            ':propose_par' => $data['propose_par']
        ]);
    }

    public function getActiveByType($type)
    {
        $query = "SELECT * FROM {$this->table} WHERE type_infraction = ? AND statut = 'actif' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$type]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $query = "SELECT r.*, u.nom, u.prenom FROM {$this->table} r LEFT JOIN users u ON r.propose_par = u.id ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatut($id, $statut)
    {
        $query = "UPDATE {$this->table} SET statut = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$statut, $id]);
    }

    /**
     * Vérifie et met à jour le statut des réglements dont le délai de vote est terminé
     */
    public function checkAndUpdateExpiredVotes()
    {
        // Récupérer tous les réglements en reflexion dont le délai est dépassé
        $query = "SELECT * FROM {$this->table} WHERE statut = 'reflexion' AND date_debut_vote <= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $reglements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($reglements as $r) {
            // Calculer les votes
            $voteQuery = "SELECT choix, COUNT(*) as total FROM vote WHERE reglement_id = ? GROUP BY choix";
            $voteStmt = $this->conn->prepare($voteQuery);
            $voteStmt->execute([$r['id']]);
            $votes = $voteStmt->fetchAll(PDO::FETCH_ASSOC);

            $oui = 0;
            $non = 0;
            foreach ($votes as $v) {
                if ($v['choix'] === 'oui') $oui = $v['total'];
                if ($v['choix'] === 'non') $non = $v['total'];
            }

            // Déterminer le nouveau statut
            if ($oui > $non) {
                $this->updateStatut($r['id'], 'actif');
            } else {
                $this->updateStatut($r['id'], 'rejete');
            }
        }

        return true;
    }
}
