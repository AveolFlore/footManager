<?php
namespace Models;
use PDO;

class Convocation
{
    public PDO $conn;
    public string $table = "convocation";

    public int $id;
    public int $match_id;
    public int $joueur_id;
    public string $equipe_match;  // 'A' ou 'B'
    public int $est_capitaine;
    public int $numero_maillot;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // CREATE — ajouter un joueur à un match
    public function create(array $data)
    {
        try {
            $this->match_id      = $data['match_id'];
            $this->joueur_id     = $data['joueur_id'];
            $this->equipe_match  = $data['equipe_match'];
            $this->est_capitaine = $data['est_capitaine'];
            $this->numero_maillot = $data['numero_maillot'];

            $sql = "INSERT INTO " . $this->table . "
                        (match_id, joueur_id, equipe_match, est_capitaine, numero_maillot)
                    VALUES
                        (:match_id, :joueur_id, :equipe_match, :est_capitaine, :numero_maillot)
                    ON DUPLICATE KEY UPDATE
                        equipe_match = VALUES(equipe_match)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':match_id'       => $this->match_id,
                ':joueur_id'      => $this->joueur_id,
                ':equipe_match'   => $this->equipe_match,
                ':est_capitaine'  => $this->est_capitaine,
                ':numero_maillot' => $this->numero_maillot
            ]);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la convocation : ' . $e->getMessage();
        }
    }

    // READ ALL — tous les convoqués d'un match
    public function read_by_match(int $match_id)
    {
        try {
            $sql = "SELECT c.*, u.nom, u.prenom, u.poste, u.photo_profil
                    FROM " . $this->table . " c
                    JOIN users u ON u.id = c.joueur_id
                    WHERE c.match_id = :match_id
                    ORDER BY c.equipe_match, u.nom";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':match_id' => $match_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la lecture des convocations : ' . $e->getMessage();
        }
    }

    // READ ONE — une convocation précise
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

    // SUGGESTION AUTO — plus-value ⑦ — top 8 joueurs (score = perf×0.6 + présences×0.4)
    public function get_suggestion()
    {
        try {
            $sql = "SELECT u.id, u.nom, u.prenom, u.poste, e.nom AS equipe,
                        COALESCE(SUM(p.points_total), 0) AS pts_perf,
                        COUNT(CASE WHEN pr.type_presence = 'present' THEN 1 END) AS nb_present,
                        ROUND(
                            COALESCE(SUM(p.points_total), 0) * 0.6 +
                            COUNT(CASE WHEN pr.type_presence = 'present' THEN 1 END) * 0.4
                        , 2) AS score
                    FROM users u
                    LEFT JOIN equipe e ON e.id = u.equipe_id
                    LEFT JOIN performance p
                        ON p.joueur_id = u.id
                        AND MONTH(p.date_enregistrement) = MONTH(NOW())
                    LEFT JOIN presence pr
                        ON pr.joueur_id = u.id
                        AND MONTH(pr.date_marquage) = MONTH(NOW())
                    WHERE u.statut = 'valide' AND u.role = 'joueur'
                    GROUP BY u.id
                    ORDER BY score DESC
                    LIMIT 8";

            $result = $this->conn->query($sql);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Erreur lors de la suggestion : ' . $e->getMessage();
        }
    }

    // DELETE — retirer un joueur d'un match
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