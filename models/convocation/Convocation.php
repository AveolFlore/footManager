<?php

namespace Models\Convocation;

use PDO;

/**
 * Enum pour définir les catégories d'équipe
 */
enum EquipeType: string
{
    case A = 'A';
    case B = 'B';
}

class Convocation
{
    private PDO $conn;
    private string $table = "convocation";

    # id de la convocation
    public ?int $id = null;
    # liaisons avec l'id des matchs
    public ?int $match_id = null;
    # liaisons avec l'id des joueurs
    public ?int $joueur_id = null;
    # partie des enums pour les équipes A et B
    public ?EquipeType $équipe = null;
    # pour le capitaine gestion
    public bool $est_capitaine = false;
    # numero de maillot
    public ?int $numero_maillot = null;

    public function __construct($db)
    {
        $this->conn = $db;
    }

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

    /**
     * Récupère les joueurs valides disponibles
     */
    public function listPlayers()
    {
        try {
            $query = "SELECT * FROM users 
                      WHERE role = 'joueur' AND statut = 'valide' 
                      ORDER BY nom ASC";
            $result = $this->conn->prepare($query);
            $result->execute();
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'erreur recuperation:' . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère les performance et la présence des utilisateurs avec score (avec pagination)
     */
    public function qualifyPlayer($filters = [], $page = 1, $perPage = 5)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $query = "SELECT * FROM (
                        SELECT u.id, u.nom, u.equipe_id as equipe,
                               COALESCE(p.pts,0) AS pts,
                               COALESCE(pr.presences,0) AS presences,
                               COALESCE(cv.nb_conv, 0) AS nb_convocations,
                               (COALESCE(p.pts,0)*0.6 + COALESCE(pr.presences,0)*0.4) AS score
                        FROM users u
                        LEFT JOIN (
                            SELECT joueur_id, SUM(points_total) AS pts
                            FROM performance
                            GROUP BY joueur_id
                        ) p ON p.joueur_id = u.id
                        LEFT JOIN (
                            SELECT joueur_id,
                                   COUNT(CASE WHEN type_presence='present' THEN 1 END) AS presences
                            FROM presence
                            GROUP BY joueur_id
                        ) pr ON pr.joueur_id = u.id
                        LEFT JOIN (
                            SELECT joueur_id, COUNT(*) AS nb_conv
                            FROM convocation
                            GROUP BY joueur_id
                        ) cv ON cv.joueur_id = u.id
                        WHERE u.statut = 'valide' AND u.role = 'joueur'
                    ) AS ranked_players WHERE 1=1";

            $params = [];
            if (!empty($filters['nom'])) {
                $query .= " AND nom LIKE :nom";
                $params[':nom'] = '%' . $filters['nom'] . '%';
            }
            if (!empty($filters['equipe'])) {
                $query .= " AND equipe = :equipe";
                $params[':equipe'] = $filters['equipe'];
            }
            if (!empty($filters['score_min'])) {
                $query .= " AND score >= :score_min";
                $params[':score_min'] = $filters['score_min'];
            }

            $query .= " ORDER BY score DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

            $result = $this->conn->prepare($query);
            $result->execute($params);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'echec de la fonction:' . $e->getMessage();
            return [];
        }
    }

    /**
     * Compte le nombre total de joueurs éligibles avec filtres
     */
  public function countQualifiedPlayers($filters = [])
{
    try {
        $query = "SELECT COUNT(*) AS total FROM (
                    SELECT u.id,
                           u.nom,
                           u.equipe_id as equipe,
                           COALESCE(p.pts,0) AS pts,
                           COALESCE(pr.presences,0) AS presences,
                           COALESCE(cv.nb_conv,0) AS nb_convocations,
                           (COALESCE(p.pts,0)*0.6 + COALESCE(pr.presences,0)*0.4) AS score
                    FROM users u
                    LEFT JOIN (
                        SELECT joueur_id, SUM(points_total) AS pts
                        FROM performance
                        GROUP BY joueur_id
                    ) p ON p.joueur_id = u.id
                    LEFT JOIN (
                        SELECT joueur_id,
                               COUNT(CASE WHEN type_presence='present' THEN 1 END) AS presences
                        FROM presence
                        GROUP BY joueur_id
                    ) pr ON pr.joueur_id = u.id
                    LEFT JOIN (
                        SELECT joueur_id, COUNT(*) AS nb_conv
                        FROM convocation
                        GROUP BY joueur_id
                    ) cv ON cv.joueur_id = u.id
                    WHERE u.statut = 'valide'
                    AND u.role = 'joueur'
                ) AS ranked_players
                WHERE 1=1";

        $params = [];

        if (!empty($filters['nom'])) {
            $query .= " AND nom LIKE :nom";
            $params[':nom'] = '%' . $filters['nom'] . '%';
        }

        if (!empty($filters['equipe'])) {
            $query .= " AND equipe = :equipe";
            $params[':equipe'] = $filters['equipe'];
        }

        if (!empty($filters['score_min'])) {
            $query .= " AND score >= :score_min";
            $params[':score_min'] = $filters['score_min'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    } catch (PDOException $e) {
        echo 'echec de la fonction: ' . $e->getMessage();
        return 0;
    }
}

    /**
     * Ajoute une convocation pour un joueur à un match avec équipe spécifiée
     */
    public function addConvocation($match_id, $joueur_id, EquipeType $equipe)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (match_id, joueur_id, equipe_match)
                      VALUES (:match_id, :joueur_id, :equipe)";

            $stmt = $this->conn->prepare($query);

            return $stmt->execute([
                ':match_id' => $match_id,
                ':joueur_id' => $joueur_id,
                ':equipe' => $equipe->value
            ]);
        } catch (PDOException $e) {
            echo 'Erreur insertion: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère la liste simplifiée des convocations existantes
     * pour vérifier les doublons côté client.
     */
    public function getConvocationsMap()
    {
        $query = "SELECT joueur_id, match_id FROM " . $this->table;
        $stmt = $this->conn->query($query);
        $convocations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On organise par joueur_id pour faciliter la recherche en PHP
        // Format : [joueur_id => [match_id1, match_id2, ...]]
        $map = [];
        foreach ($convocations as $row) {
            $map[$row['joueur_id']][] = $row['match_id'];
        }
        return $map;
    }

    /**
     * Vérifie si le joueur est déjà convoqué pour un match qui se chevauche dans le temps.
     * On considère par défaut qu'un match/séance dure 2 heures.
     */
    public function hasOverlap(int $joueur_id, int $match_id): bool
    {
        $stmtMatch = $this->conn->prepare("SELECT date FROM match_seance WHERE id = :id");
        $stmtMatch->execute([':id' => $match_id]);
        $newMatch = $stmtMatch->fetch(PDO::FETCH_ASSOC);

        if (!$newMatch) return false;

        // Logique de chevauchement :
        // (Start_Nouveau < End_Existant) AND (End_Nouveau > Start_Existant)
        // On utilise DATE_ADD pour simuler une durée de 2 heures.
        $query = "SELECT COUNT(*) 
                  FROM convocation c
                  JOIN match_seance ms ON c.match_id = ms.id
                  WHERE c.joueur_id = :joueur_id
                    AND :new_start < DATE_ADD(ms.date, INTERVAL 2 HOUR)
                    AND DATE_ADD(:new_start_alt, INTERVAL 2 HOUR) > ms.date";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':joueur_id' => $joueur_id,
            ':new_start' => $newMatch['date'],
            ':new_start_alt' => $newMatch['date']
        ]);

        return (int)$stmt->fetchColumn() > 0;
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
