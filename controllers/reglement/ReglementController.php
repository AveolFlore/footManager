<?php

namespace App\Controllers;

use App\Models\Reglement;
use PDO;
class ReglementController {
    private $reglementModel;
    private $db;

    public function __construct($database) {
        $this->db = $database;
        $this->reglementModel = new \Models\Reglement($this->db);
    }

    /**
     * Affiche la page principale des règlements
     */
    public function index() {
        // 1. Récupération des données (On utilise 'reflexion' car c'est ton ENUM SQL)
        $regles_actives = $this->reglementModel->getByStatut('actif');
        $regles_en_reflexion = $this->reglementModel->getByStatut('reflexion');

        // 2. Comptage pour les badges
        $count_actives = $this->reglementModel->countByStatut('actif');
        $count_reflexion = $this->reglementModel->countByStatut('reflexion');

        // 3. Chargement de la vue
        // Les variables ci-dessus seront accessibles dans le fichier inclus
        include __DIR__ . '/../../views/pages/reglement/index.php';
    }

    public function storeRule($data) {
    // 1. On instancie le Model proprement nettoyé
    $reglementModel = new \Models\Reglement($this->db);

    // 2. On prépare les données (nettoyage simple)
    $titre = strip_tags($data['titre'] ?? 'Sans titre');
    $description = strip_tags($data['description'] ?? '');
    $type = $data['type_infraction'] ?? 'autre';
    $montant = (int)($data['montant_amende'] ?? 0);
    
    // On récupère l'ID de l'utilisateur en session
    $user_id = $_SESSION['user_id'] ?? 2; 

    // 3. Validation de sécurité
    if (empty($description)) {
        header("Location: /page-rule?error=empty_desc");
        exit();
    }

    // 4. Appel au Model pour l'insertion
    $success = $reglementModel->createProposition($titre, $description, $user_id, $type, $montant);

    // 5. Redirection finale
    if ($success) {
        header("Location: /page-rule?success=1");
    } else {
        header("Location: /page-rule?error=db_error");
    }
    exit();
}

    /**
     * Traite la soumission d'une nouvelle proposition
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_proposition'])) {
            $titre = $_POST['titre'] ?? "Nouvelle Proposition";
            $description = $_POST['description'];
            $type_infraction = $_POST['type_infraction'] ?? 'autre';
            $montant = $_POST['montant_amende'] ?? 0;
            
            // On récupère l'ID de l'utilisateur connecté (fictif pour le test)
            $user_id = $_SESSION['user_id'] ?? 2; 

            if (!empty($description)) {
                $this->reglementModel->createProposition(
                    $titre, 
                    $description, 
                    $user_id, 
                    $type_infraction, 
                    $montant
                );

                // Redirection
                header("Location: index.php?page=reglement&success=1");
                exit();
            }
        }
    }
}