<?php

namespace Controllers\Presence;

use Config\Database;
use Models\Presence\Presence;
use Models\Reglement\Reglement;
use Models\Sanction\Sanction;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use Models\Utilisateur\User;
use Models\MatchSeance;
use PDO;

class PresenceController
{
    private Presence $presenceModel;
    private Reglement $reglementModel;
    private Sanction $sanctionModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
    private User $userModel;
    private MatchSeance $matchSeanceModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->presenceModel = new Presence($this->pdo);
        $this->reglementModel = new Reglement($this->pdo);
        $this->sanctionModel = new Sanction($this->pdo);
        $this->notificationModel = new Notification($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
        $this->userModel = new User($this->pdo);
        $this->matchSeanceModel = new MatchSeance($this->pdo);
    }

    /**
     * Affiche la page principale des présences
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $mois = isset($_GET['mois']) ? (int)$_GET['mois'] : date('m');
        $annee = isset($_GET['annee']) ? (int)$_GET['annee'] : date('Y');

        $seances = $this->presenceModel->getAllSeancesWithPresence($perPage, $offset);
        $totalSeances = $this->presenceModel->countAllSeances();
        $totalPages = ceil($totalSeances / $perPage);
        $joueursStats = $this->presenceModel->getJoueursWithStats($mois, $annee);

        require_once __DIR__ . '/../../views/pages/presence/index.php';
    }

    /**
     * Affiche le formulaire de marquage de présence pour une séance
     */
    public function formulaireMarquage()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $seance_id = isset($_GET['seance_id']) ? (int)$_GET['seance_id'] : 0;
        if (!$seance_id) {
            header("Location: /presence?msg=seance_invalide");
            exit;
        }

        $seance = $this->matchSeanceModel->getById($seance_id);
        if (!$seance) {
            header("Location: /presence?msg=seance_introuvable");
            exit;
        }

        $joueurs = $this->userModel->getAllJoueursValides();
        $presencesExistantes = $this->presenceModel->seanceHasPresences($seance_id)
            ? $this->presenceModel->getBySeance($seance_id)
            : [];

        // Organiser les présences existantes par joueur_id
        $presencesMap = [];
        foreach ($presencesExistantes as $p) {
            $presencesMap[$p['joueur_id']] = $p;
        }

        require_once __DIR__ . '/../../views/pages/presence/marquer.php';
    }

    /**
     * Marque la présence d'un joueur à une séance et applique les sanctions automatiques.
     */
    public function marquerPresence()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['presences'])) {
            header("Location: /presence");
            exit;
        }

        $seance_id = intval($_POST['seance_id']);
        $marqueur_id = $_SESSION['user']['id'];

        // Supprimer les anciennes présences si elles existent
        $this->presenceModel->deleteBySeance($seance_id);

        // On boucle sur chaque joueur envoyé par le formulaire
        foreach ($_POST['presences'] as $joueur_id => $data) {
            $type_presence = $data['type']; // 'present', 'retard', 'absent' ou 'excuse'
            $note = $data['note'] ?? '';

            // 1. Créer l'entrée en base de données
            $presence_id = $this->presenceModel->create([
                'seance_id' => $seance_id,
                'joueur_id' => $joueur_id,
                'type_presence' => $type_presence,
                'marque_par' => $marqueur_id,
                'note' => $note
            ]);

            // 2. Déterminer le type d'infraction
            $type_infraction = null;
            $motif_sanction = '';

            if ($type_presence === 'retard') {
                $type_infraction = 'retard';
                $motif_sanction = "Retard à la séance";
            } elseif ($type_presence === 'absent') {
                $type_infraction = 'absence';
                $motif_sanction = "Absence à la séance";
            }

            // 3. Gestion automatique des sanctions si infraction
            if ($type_infraction) {
                $reglement = $this->reglementModel->getActiveByType($type_infraction);
                if ($reglement) {
                    $this->sanctionModel->create([
                        'joueur_id' => $joueur_id,
                        'reglement_id' => $reglement['id'],
                        'presence_id' => $presence_id,
                        'applique_par' => $marqueur_id,
                        'montant' => $reglement['montant_amende'],
                        'motif' => $motif_sanction,
                        'statut' => 'en_attente'
                    ]);

                    // Notifier le joueur
                    $this->notificationModel->create([
                        'destinataire_id' => $joueur_id,
                        'type' => 'sanction',
                        'message' => "Amende de {$reglement['montant_amende']} FCFA pour $motif_sanction.",
                        'lien' => '/page-sanction'
                    ]);

                    // Log l'application de la sanction
                    $joueur = $this->userModel->getFindId($joueur_id);
                    $nom_joueur = $joueur ? htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']) : 'Joueur';
                    $this->activiteModel->log($marqueur_id, 'sanction_appliquee', "Sanction pour $type_infraction appliquée à $nom_joueur pour {$reglement['montant_amende']} FCFA.");
                }
            }
        }

        // Log unique pour l'action
        $this->activiteModel->log($marqueur_id, 'presence_marque', "Feuille de présence validée pour la séance #$seance_id");

        header("Location: /presence?msg=presences_enregistrees");
        exit;
    }
}
