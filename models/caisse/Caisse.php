<?php

namespace Models\Caisse;

use PDO;

class Caisse
{
    private PDO $conn;
    private string $table = "caisse";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addTransaction(array $data)
    {
        $query = "INSERT INTO {$this->table} (type, libelle, montant, categorie, reference_id, enregistre_par, date_transaction) 
                  VALUES (:type, :libelle, :montant, :categorie, :reference_id, :enregistre_par, CURDATE())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':type' => $data['type'],
            ':libelle' => $data['libelle'],
            ':montant' => $data['montant'],
            ':categorie' => $data['categorie'],
            ':reference_id' => $data['reference_id'] ?? null,
            ':enregistre_par' => $data['enregistre_par']
        ]);
    }

    public function getSolde()
    {
        $query = "SELECT 
                    SUM(CASE WHEN type = 'entree' THEN montant ELSE 0 END) - 
                    SUM(CASE WHEN type = 'sortie' THEN montant ELSE 0 END) as solde 
                  FROM {$this->table}";
        $stmt = $this->conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['solde'] ?? 0;
    }

    public function getAllTransactions()
    {
        $query = "SELECT c.*, u.nom, u.prenom 
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.enregistre_par = u.id
                  ORDER BY date_transaction DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
