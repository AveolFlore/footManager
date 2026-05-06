<?php

namespace Models\Historique_equipe;

use PDO;

class Historique
{
    private PDO $conn;
    private string $table = "historique_equipe";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Ajoute une entrée dans l'historique d'équipe.
     */
    public function addEntry($joueur_id, $equipe_id, $modifie_par, $motif = "Validation initiale")
    {
        // On ferme l'ancienne entrée si elle existe
        $queryClose = "UPDATE {$this->table} SET date_sortie = CURDATE() WHERE joueur_id = ? AND date_sortie IS NULL";
        $stmtClose = $this->conn->prepare($queryClose);
        $stmtClose->execute([$joueur_id]);

        // On crée la nouvelle entrée
        $query = "INSERT INTO {$this->table} (joueur_id, equipe_id, date_entree, modifie_par, motif) 
                  VALUES (:joueur_id, :equipe_id, CURDATE(), :modifie_par, :motif)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':joueur_id' => $joueur_id,
            ':equipe_id' => $equipe_id,
            ':modifie_par' => $modifie_par,
            ':motif' => $motif
        ]);
    }
}
