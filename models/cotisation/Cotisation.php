<?php

namespace Models\Cotisation;

use PDO;

class Cotisation
{
    private PDO $conn;
    private string $table = "cotisation";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crée une nouvelle cotisation.
     */
    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (joueur_id, montant, mois, annee, statut) 
                  VALUES (:joueur_id, :montant, :mois, :annee, :statut)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':joueur_id' => $data['joueur_id'],
            ':montant' => $data['montant'],
            ':mois' => $data['mois'],
            ':annee' => $data['annee'],
            ':statut' => $data['statut'] ?? 'non_paye'
        ]);
    }

    /**
     * Récupère toutes les cotisations d'un joueur.
     */
    public function getByJoueur($joueur_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE joueur_id = ? ORDER BY annee DESC, mois DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$joueur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marquer une cotisation comme payée.
     */
    public function markAsPaid($id)
    {
        $query = "UPDATE {$this->table} SET statut = 'paye', date_paiement = CURDATE() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Récupère les cotisations impayées du mois en cours (avec pagination).
     */
    public function getUnpaidCurrentMonth($mois, $annee, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT c.*, u.nom, u.prenom 
                  FROM {$this->table} c
                  JOIN users u ON c.joueur_id = u.id
                  WHERE c.mois = ? AND c.annee = ? AND c.statut = 'non_paye'
                  LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$mois, $annee]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de cotisations impayées du mois en cours.
     */
    public function getTotalUnpaidCurrentMonth($mois, $annee)
    {
        $query = "SELECT COUNT(*) as total
                  FROM {$this->table} c
                  JOIN users u ON c.joueur_id = u.id
                  WHERE c.mois = ? AND c.annee = ? AND c.statut = 'non_paye'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$mois, $annee]);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
