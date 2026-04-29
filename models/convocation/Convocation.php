<?php

namespace Models\Convocation;

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
#lliaisons avec l'id des matchs
    public int $match_id;
#liaisons avec l'id des joueures
    public int $joueur_id;
#partie des enums pour les equipes A et B
    public EquipeType $equipe;

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

    #ont recupere les users via la table users qui remplissent certaines conditions
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

public function  qualifyPlayer(){
 try {
        $query = " SELECT u.id, u.nom,
        COALESCE(SUM(p.points_total),0) AS pts,
        COUNT(CASE WHEN pr.type_presence='present' THEN 1 END) AS presences,
        (COALESCE(SUM(p.points_total),0)*0.6 +
         COUNT(CASE WHEN pr.type_presence='present' THEN 1 END)*0.4) AS score
        FROM users u
        -- left join pour recuperer tout les users meme ceux n'ayant aucun score en base de données
        LEFT JOIN performance p ON p.joueur_id = u.id
        LEFT JOIN presence pr ON pr.joueur_id = u.id
        WHERE u.statut='valide'
        GROUP BY u.id
        ORDER BY score DESC
        LIMIT 8";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result->fetchAll();
 }  catch (\PDOException $e) {
        echo 'echec de la fonction:' . $e->getMessage();
      }
}
}