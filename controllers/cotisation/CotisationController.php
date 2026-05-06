<?php

namespace Controllers\Cotisation;

use Config\Database;
use Models\Cotisation\Cotisation;
use Models\Caisse\Caisse;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use Models\Utilisateur\User;
use PDO;

class CotisationController
{
    private Cotisation $cotisationModel;
    private Caisse $caisseModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
    private User $userModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->cotisationModel = new Cotisation($this->pdo);
        $this->caisseModel = new Caisse($this->pdo);
        $this->notificationModel = new Notification($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    /**
     * Marquer une cotisation comme payée et générer une entrée en caisse.
     */
    public function payerCotisation()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-finance");
            exit;
        }

        $cotisation_id = intval($_POST['cotisation_id']);
        $admin_id = $_SESSION['user']['id'];

        // 1. Marquer comme payé
        $this->cotisationModel->markAsPaid($cotisation_id);

        // Récupérer les infos de la cotisation
        $query = "SELECT c.*, u.nom, u.prenom FROM cotisation c JOIN users u ON c.joueur_id = u.id WHERE c.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$cotisation_id]);
        $cotisation = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Entrée automatique en caisse
        $this->caisseModel->addTransaction([
            'type' => 'entree',
            'libelle' => "Cotisation {$cotisation['mois']}/{$cotisation['annee']} - {$cotisation['nom']} {$cotisation['prenom']}",
            'montant' => $cotisation['montant'],
            'categorie' => 'cotisation',
            'reference_id' => $cotisation_id,
            'enregistre_par' => $admin_id
        ]);

        // 3. Notifier le joueur
        $this->notificationModel->create([
            'destinataire_id' => $cotisation['joueur_id'],
            'type' => 'cotisation',
            'message' => "Ton paiement de cotisation pour {$cotisation['mois']}/{$cotisation['annee']} a été validé.",
            'lien' => '/page-finance'
        ]);

        // 4. Log l'activité
        $this->activiteModel->log($admin_id, 'cotisation_payee', "Paiement cotisation validé pour {$cotisation['nom']} {$cotisation['prenom']}");

        header("Location: /page-finance?msg=paiement_valide");
        exit;
    }
}
