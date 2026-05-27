<?php
namespace Models;
use PDO;

class ResultatMatch
{
    public PDO $conn;
    public string $table = "resultat_match";

    public int $id;
    public int $match_id;
    public int $buts_equipe_a;
    public int $buts_equipe_b;
    public int $saisie_par;
    // equipe_gagnante est GENERATED par MySQL — on ne la déclare pas ici

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // CREATE — saisir le résultat après le match
    public function create(array $data)
    {
        try {
            $this->match_id      = $data['match_id'];
            $this->buts_equipe_a = $data['buts_equipe_a'];
            $this->buts_equipe_b = $data['buts_equipe_b'];
            $this->saisie_par    = $data['saisie_par'];

            $sql = "INSERT INTO " . $this->table . "
                        (match_id, buts_equipe_a, buts_equipe_b, saisie_par)
                    VALUES
                        (:match_id, :buts_equipe_a, :buts_equipe_b, :saisie_par)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':match_id'      => $this->match_id,
                ':buts_equipe_a' => $this->buts_equipe_a,
                ':buts_equipe_b' => $this->buts_equipe_b,
                ':saisie_par'    => $this->saisie_par
            ]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la saisie du résultat : ' . $e->getMessage();
        }
    }

    // READ ONE — résultat d'un match précis (avec equipe_gagnante calculée par MySQL)
    public function read_by_match(int $match_id)
    {
        try {
            $sql = "SELECT rm.*, u.nom AS saisi_par_nom
                    FROM " . $this->table . " rm
                    JOIN users u ON u.id = rm.saisie_par
                    WHERE rm.match_id = :match_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':match_id' => $match_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la lecture du résultat : ' . $e->getMessage();
        }
    }

    // UPDATE — corriger un résultat mal saisi
    public function update(int $match_id, array $data)
    {
        try {
            $this->buts_equipe_a = $data['buts_equipe_a'];
            $this->buts_equipe_b = $data['buts_equipe_b'];

            $sql = "UPDATE " . $this->table . "
                    SET buts_equipe_a = :buts_equipe_a,
                        buts_equipe_b = :buts_equipe_b
                    WHERE match_id = :match_id";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':buts_equipe_a' => $this->buts_equipe_a,
                ':buts_equipe_b' => $this->buts_equipe_b,
                ':match_id'      => $match_id
            ]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la correction du résultat : ' . $e->getMessage();
        }
    }

    // DELETE
    public function delete_one(int $match_id)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE match_id = :match_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':match_id' => $match_id]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la suppression : ' . $e->getMessage();
        }
    }
}