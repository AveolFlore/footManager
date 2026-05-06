<?php

namespace Models;

/**
 * Enum pour définir les catégories d'équipe
 */
enum EquipeType: string {
    case A = 'A';
    case B = 'B';
}

class Convocation {
    private  \PDO $conn;

    #nom de la table
    private $table = 'convocation';

    # id de la convocation
    public  int  $id;
#liaisons avec l'id des matchs
    public int $match_id;
#liaisons avec l'id des joueurs
    public int $joueur_id;
#partie des enums pour les équipes A et B
    public EquipeType $équipe;

    #pour le capitaine gestion
    public bool $est_capitaine = false ;

    #numero de maillot
    public ?int $numero_maillot = null;
    /**
     * Constructeur pour initialiser la connexion
     */
    public function __construct(\PDO $db)
    {
        $this->conn = $db;
    }

    #ont récupère les users via la table users qui remplissent certaines conditions
    public function  listPlayers(){
      try {
        $query = "SELECT * FROM users 
                      WHERE role = 'joueur' AND statut = 'valide' 
                      ORDER BY nom ASC";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result->fetchAll(\PDO::FETCH_ASSOC);

      } catch (\PDOException $e) {
        echo 'erreur recuperation:' . $e->getMessage();
      }
}
#ont recupere les performance et la presence des utilisateur et ont fait la jointure

// -- left join pour recupere tout les joueur y compris ceux n'ayant aucune performance ou points
public function qualifyPlayer($filters = []){
 try {
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

        $query .= " ORDER BY score DESC";

        $result = $this->conn->prepare($query);
        $result->execute($params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
 }  catch (\PDOException $e) {
        echo 'echec de la fonction:' . $e->getMessage();
        return [];
      }
}
#addconvocation s'occupe ici d'ajouter dans la table convocation les donnees les joueures declarer comme convoquer
public function addConvocation($match_id, $joueur_id, EquipeType $equipe) {
    try {
        $query = "INSERT INTO " . $this->table . " (match_id, joueur_id, equipe_match)
                  VALUES (:match_id, :joueur_id, :equipe)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':match_id' => $match_id,
            ':joueur_id' => $joueur_id,
            ':equipe' => $equipe->value
        ]);

    } catch (\PDOException $e) {
        echo 'Erreur insertion: ' . $e->getMessage();
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
        $convocations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
        $newMatch = $stmtMatch->fetch(\PDO::FETCH_ASSOC);

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
}