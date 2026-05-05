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
}