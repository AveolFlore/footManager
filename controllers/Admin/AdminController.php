<?php

namespace Controllers\Admin;

use Config\Database;
use Models\Utilisateur\User;
use Models\Cotisation\Cotisation;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use Models\Historique_equipe\Historique;
use PDO;

require_once __DIR__ . '/../../middleware/Admin.php';

class AdminController
{
    private User $userModel;
    private Cotisation $cotisationModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
    private Historique $historiqueModel;
    private Database $database;
    private PDO $pdo;

    public function __construct()
    {
        $this->database  = new Database;
        $this->pdo       = $this->database->connect();
        $this->userModel = new User($this->pdo);
        $this->cotisationModel = new Cotisation($this->pdo);
        $this->notificationModel = new Notification($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
        $this->historiqueModel = new Historique($this->pdo);
    }

    // VALIDER + ASSIGNER EQUIPE (Logique complète selon cahier des charges)
    public function validateUser()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/page-admin");
            exit;
        }

        $userId   = intval($_POST['user_id']);
        $equipeId = intval($_POST['equipe_id']);
        $adminId  = $_SESSION['user']['id'];

        // 1. Changer le statut et affecter l'équipe
        $this->userModel->validateAndAssignTeam($userId, $equipeId);

        // 2. Générer la cotisation du mois en cours AUTOMATIQUEMENT
        $montant = 1000; // Montant par défaut (à paramétrer si besoin)
        $this->cotisationModel->create([
            'joueur_id' => $userId,
            'montant' => $montant,
            'mois' => date('m'),
            'annee' => date('Y'),
            'statut' => 'non_paye'
        ]);

        // 3. Enregistrer dans l'historique d'équipe
        $this->historiqueModel->addEntry($userId, $equipeId, $adminId);

        // 4. Notifier le joueur
        $this->notificationModel->create([
            'destinataire_id' => $userId,
            'type' => 'cotisation',
            'message' => "Félicitations ! Ton compte a été validé. Bienvenue dans le club. Une cotisation de $montant FCFA a été générée pour ce mois.",
            'lien' => '/page-finance'
        ]);

        // 5. Logger l'action
        $this->activiteModel->log($adminId, 'joueur_valide', "Joueur #$userId validé et affecté à l'équipe #$equipeId");

        header("Location:/page-admin?msg=joueur_valide");
        exit;
    }

    // REFUSER
    public function refuseUser()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/page-admin");
            exit;
        }

        $userId = intval($_POST['user_id']);

        $this->userModel->refuseUser($userId);

        header("Location:/page-admin?msg=joueur_refuse");
        exit;
    }

    // CHANGER D'EQUIPE
    public function changeTeam()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/page-team");
            exit;
        }

        $userId   = intval($_POST['user_id']);
        $equipeId = intval($_POST['equipe_id']);
        $motif    = $_POST['motif'] ?? "Rééquilibrage d'effectif";
        $adminId  = $_SESSION['user']['id'];

        // 1. Mettre à jour l'équipe de l'utilisateur
        $user = $this->userModel->getFindId($userId);
        $this->userModel->update($userId, [
            'role' => $user['role'],
            'statut' => $user['statut'] ?? 'valide', // Use current status
            'equipe_id' => $equipeId
        ]);

        // 2. Enregistrer dans l'historique
        $this->historiqueModel->addEntry($userId, $equipeId, $adminId, $motif);

        // 3. Notifier le joueur
        $this->notificationModel->create([
            'destinataire_id' => $userId,
            'type' => 'reglement', // Ou un type plus approprié si existant
            'message' => "Tu as été transféré vers une nouvelle équipe : $motif",
            'lien' => '/page-team'
        ]);

        // 4. Logger l'action
        $this->activiteModel->log($adminId, 'joueur_transfert', "Joueur #$userId transféré vers l'équipe #$equipeId. Motif: $motif");

        header("Location:/page-team?equipe_id=$equipeId&msg=transfert_success");
        exit;
    }
}