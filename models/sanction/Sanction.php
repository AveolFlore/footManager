<?php

namespace Models\Sanction;

use PDO;

class Sanction
{
    private PDO $conn;
    private string $table = "sanction";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (joueur_id, reglement_id, presence_id, applique_par, montant, motif, statut, date_sanction) 
                  VALUES (:joueur_id, :reglement_id, :presence_id, :applique_par, :montant, :motif, :statut, CURDATE())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':joueur_id' => $data['joueur_id'],
            ':reglement_id' => $data['reglement_id'],
            ':presence_id' => $data['presence_id'] ?? null,
            ':applique_par' => $data['applique_par'],
            ':montant' => $data['montant'],
            ':motif' => $data['motif'],
            ':statut' => $data['statut'] ?? 'en_attente'
        ]);
    }

    public function getByJoueur($joueur_id)
    {
        $query = "SELECT s.*, r.titre 
                  FROM {$this->table} s
                  JOIN reglement r ON s.reglement_id = r.id
                  WHERE s.joueur_id = ? ORDER BY date_sanction DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$joueur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsPaid($id)
    {
        $query = "UPDATE {$this->table} SET statut = 'payee' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getAllPending()
    {
        $query = "SELECT s.*, u.nom, u.prenom, r.titre 
                  FROM {$this->table} s
                  JOIN users u ON s.joueur_id = u.id
                  JOIN reglement r ON s.reglement_id = r.id
                  WHERE s.statut = 'en_attente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPendingPaginated(int $limit, int $offset): array
    {
        $query = "SELECT s.*, u.nom, u.prenom, r.titre 
                  FROM {$this->table} s
                  JOIN users u ON s.joueur_id = u.id
                  JOIN reglement r ON s.reglement_id = r.id
                  WHERE s.statut = 'en_attente'
                  ORDER BY s.date_sanction DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllPending(): int
    {
        $query = "SELECT COUNT(*) AS total FROM {$this->table} s WHERE s.statut = 'en_attente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
