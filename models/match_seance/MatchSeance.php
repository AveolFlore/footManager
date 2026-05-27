<?php
namespace Models;
use PDO;

class MatchSeance
{
    public PDO $conn;
    public string $table = "match_seance";

    // Propriétés qui correspondent aux colonnes de la table
    public int $id;
    public string $type;       // 'match' ou 'entr'
    public string $date;
    public string $lieu;
    public string $description;
    public string $statut;     // 'planifie' | 'publie' | 'termine'
    public int $createur_id;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // CREATE
    public function create(array $data)
    {
        try {
            $this->type        = $data['type'];
            $this->date        = $data['date'];
            $this->lieu        = $data['lieu'];
            $this->description = $data['description'];
            $this->createur_id = $data['createur_id'];

            $sql = "INSERT INTO " . $this->table . " (type, date, lieu, description, statut, createur_id)
                    VALUES (:type, :date, :lieu, :description, 'planifie', :createur_id)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':type'        => $this->type,
                ':date'        => $this->date,
                ':lieu'        => $this->lieu,
                ':description' => $this->description,
                ':createur_id' => $this->createur_id
            ]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la création : ' . $e->getMessage();
        }
    }

    // READ ALL
    public function read()
    {
        try {
            $sql = "SELECT ms.*, u.nom AS createur_nom
                    FROM " . $this->table . " ms
                    JOIN users u ON u.id = ms.createur_id
                    ORDER BY ms.date DESC";

            $result = $this->conn->query($sql);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la lecture : ' . $e->getMessage();
        }
    }

    // READ ONE
    public function read_one(int $id)
    {
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la lecture : ' . $e->getMessage();
        }
    }

    // UPDATE (modifier lieu, date, description)
    public function update(int $id, array $data)
    {
        try {
            $this->id          = $id;
            $this->date        = $data['date'];
            $this->lieu        = $data['lieu'];
            $this->description = $data['description'];

            $sql = "UPDATE " . $this->table . "
                    SET date = :date, lieu = :lieu, description = :description
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':date'        => $this->date,
                ':lieu'        => $this->lieu,
                ':description' => $this->description,
                ':id'          => $this->id
            ]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la modification : ' . $e->getMessage();
        }
    }

    // PUBLIER (statut → publie)
    public function publier(int $id)
    {
        try {
            $sql = "UPDATE " . $this->table . " SET statut = 'publie' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la publication : ' . $e->getMessage();
        }
    }

    // TERMINER (statut → termine, après saisie résultat)
    public function terminer(int $id)
    {
        try {
            $sql = "UPDATE " . $this->table . " SET statut = 'termine' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la clôture : ' . $e->getMessage();
        }
    }

    // DELETE
    public function delete_one(int $id)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la suppression : ' . $e->getMessage();
        }
    }
}