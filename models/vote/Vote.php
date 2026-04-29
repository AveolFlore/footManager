<?php
class Vote {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Enregistre ou met à jour un vote
    public function voter($reglement_id, $joueur_id, $choix) {
        // On utilise ON DUPLICATE KEY UPDATE si tu as une contrainte unique sur (reglement_id, joueur_id)
        // Sinon, on fait un simple INSERT
        $query = "INSERT INTO vote (reglement_id, joueur_id, choix, date_vote) 
                  VALUES (:reg_id, :joueur_id, :choix, CURDATE())";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'reg_id'    => $reglement_id,
            'joueur_id' => $joueur_id,
            'choix'     => $choix // 'oui' ou 'non'
        ]);
    }

    // Vérifie si le joueur a déjà voté pour cette règle
    public function aDejaVote($reglement_id, $joueur_id) {
        $query = "SELECT COUNT(*) FROM vote WHERE reglement_id = :reg_id AND joueur_id = :joueur_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['reg_id' => $reglement_id, 'joueur_id' => $joueur_id]);
        return $stmt->fetchColumn() > 0;
    }
}