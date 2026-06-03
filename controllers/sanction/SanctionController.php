<?php

namespace Controllers\Sanction;

use Config\Database;
use Models\Sanction\Sanction;
use Models\Caisse\Caisse;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use Models\Utilisateur\User;
use PDO;

class SanctionController
{
    private Sanction $sanctionModel;
    private Caisse $caisseModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
    private User $userModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->sanctionModel = new Sanction($this->pdo);
        $this->caisseModel = new Caisse($this->pdo);
        $this->notificationModel = new Notification($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    /**
     * Marque une sanction comme payée et ajoute la transaction en caisse.
     */
    public function marquerPayee()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Vérifier accès (seul bureau/admin peut faire ça)
        $allowed_roles = ['president', 'organisateur', 'tresorier']; // A adapter selon vos rôles
        if (!isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], $allowed_roles)) {
            header("Location: /?msg=acces_refuse");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['sanction_id'])) {
            header("Location: /page-sanction?msg=requete_invalide");
            exit;
        }

        $sanction_id = intval($_POST['sanction_id']);
        $user_id = $_SESSION['user']['id'];

        // Récupérer la sanction pour les détails
        $query = "SELECT s.*, u.nom, u.prenom FROM sanction s 
                  JOIN users u ON s.joueur_id = u.id WHERE s.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$sanction_id]);
        $sanction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sanction) {
            header("Location: /page-sanction?msg=sanction_introuvable");
            exit;
        }

        if ($sanction['statut'] === 'payee') {
            header("Location: /page-sanction?msg=deja_payee");
            exit;
        }

        // Marquer comme payée
        $this->sanctionModel->markAsPaid($sanction_id);

        // Ajouter la transaction en caisse
        $this->caisseModel->addTransaction([
            'type' => 'entree',
            'libelle' => "Amende payée par {$sanction['nom']} {$sanction['prenom']}",
            'montant' => $sanction['montant'],
            'categorie' => 'sanction',
            'reference_id' => $sanction_id,
            'enregistre_par' => $user_id
        ]);

        // Notifier le joueur que son amende a été payée
        $this->notificationModel->create([
            'destinataire_id' => $sanction['joueur_id'],
            'type' => 'sanction',
            'message' => "Votre amende de {$sanction['montant']} FCFA a été enregistrée comme payée.",
            'lien' => '/page-sanction'
        ]);

        // Log l'action
        $this->activiteModel->log($user_id, 'sanction_payee', "Amende de {$sanction['montant']} FCFA pour {$sanction['nom']} {$sanction['prenom']} marquée payée.");

        header("Location: /page-sanction?msg=sanction_payee");
        exit;
    }

    /**
     * Liste toutes les sanctions (pour la page de gestion)
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $sanctions = $this->sanctionModel->getAllPending();
        require_once __DIR__ . '/../../views/pages/sanction/index.php';
    }
}
