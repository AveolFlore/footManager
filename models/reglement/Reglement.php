<?php
namespace Models;

use PDO;

class Reglement {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Récupère les règlements selon leur statut (actif ou propose)
public function getByStatut($statut) {
    $query = "SELECT r.*, 
              (SELECT COUNT(*) FROM vote WHERE reglement_id = r.id AND choix = 'oui') as total_pour,
              (SELECT COUNT(*) FROM vote WHERE reglement_id = r.id AND choix = 'non') as total_contre
              FROM reglement r 
              WHERE r.statut = :statut";

    $stmt = $this->db->prepare($query);

    $stmt->execute([
        ':statut' => $statut
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Pour compter rapidement pour les badges des onglets
    public function countByStatut($statut) {
        $query = "SELECT COUNT(*) FROM reglement WHERE statut = :statut";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['statut' => $statut]);
        return $stmt->fetchColumn();
    }

    //Ajoute cette méthode pour enregistrer la proposition en base de données
  // Ajoute cette méthode pour enregistrer la proposition en base de données
public function createProposition($titre, $description, $propose_par, $type_infraction, $montant_amende) {
    // Utilisation du statut 'reflexion' conformément à ton ENUM SQL
    // NOW() est utilisé pour la date_creation si aucune date n'est fournie
    $query = "INSERT INTO reglement (titre, description, propose_par, statut, type_infraction, montant_amende, date_creation) 
              VALUES (:titre, :description, :propose_par, 'reflexion', :type_infraction, :montant_amende, NOW())";
    
    $stmt = $this->db->prepare($query);
    
    return $stmt->execute([
        'titre'           => $titre,
        'description'     => $description,
        'propose_par'     => $propose_par,
        'type_infraction' => $type_infraction, // Doit être 'retard', 'absence', 'comportement' ou 'autre'
        'montant_amende'  => $montant_amende
    ]);
}

public function verifierEtValiderRegle($id) {
    $query = "SELECT 
                (SELECT COUNT(*) FROM vote WHERE reglement_id = :id AND choix = 'oui') as pour,
                (SELECT COUNT(*) FROM vote WHERE reglement_id = :id AND choix = 'non') as contre";
    $stmt = $this->db->prepare($query);
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch();

    // Exemple : Si on a 10 votes "Pour", elle passe en vigueur
    if ($res['pour'] >= 10) {
        $upd = $this->db->prepare("UPDATE reglement SET statut = 'actif' WHERE id = :id");
        $upd->execute(['id' => $id]);
    }
}
}
?>
