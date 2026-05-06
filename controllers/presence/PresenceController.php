<?php

namespace Controllers\Presence;

use Config\Database;
use Models\Presence\Presence;
use Models\Reglement\Reglement;
use Models\Sanction\Sanction;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use PDO;

class PresenceController
{
    private Presence $presenceModel;
    private Reglement $reglementModel;
    private Sanction $sanctionModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
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
    }

    /**
     * Marque la présence d'un joueur à une séance.
     */
public function marquerPresence()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['presences'])) {
        header("Location: /page-match");
        exit;
    }

    $seance_id = intval($_POST['seance_id']);
    $marqueur_id = $_SESSION['user']['id'];

    // On boucle sur chaque joueur envoyé par le formulaire
    foreach ($_POST['presences'] as $joueur_id => $data) {
        $type_presence = $data['type']; // 'present', 'retard' ou 'absent'

        // 1. Créer l'entrée en base de données
        $presence_id = $this->presenceModel->create([
            'seance_id' => $seance_id,
            'joueur_id' => $joueur_id,
            'type_presence' => $type_presence,
            'marque_par' => $marqueur_id,
            'note' => ''
        ]);

        // 2. Gestion automatique des sanctions pour les retards
        if ($type_presence === 'retard') {
            $reglement = $this->reglementModel->getActiveByType('retard');
            if ($reglement) {
                $this->sanctionModel->create([
                    'joueur_id' => $joueur_id,
                    'reglement_id' => $reglement['id'],
                    'presence_id' => $presence_id,
                    'applique_par' => $marqueur_id,
                    'montant' => $reglement['montant_amende'],
                    'motif' => "Retard automatique",
                    'statut' => 'en_attente'
                ]);

                $this->notificationModel->create([
                    'destinataire_id' => $joueur_id,
                    'type' => 'sanction',
                    'message' => "Amende de {$reglement['montant_amende']} FCFA pour retard.",
                    'lien' => '/page-sanction'
                ]);
            }
        }
    }

    // 3. Log unique pour l'action
    $this->activiteModel->log($marqueur_id, 'presence_marque', "Feuille de présence validée pour la séance #$seance_id");

    header("Location: /page-match-detail?id=$seance_id&msg=presences_enregistrees");
    exit;
}

}
