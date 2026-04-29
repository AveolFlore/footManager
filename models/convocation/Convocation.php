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
}