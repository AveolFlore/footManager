<?php

namespace Controllers\Reglement;

use Config\Database;
use Models\Reglement\Reglement;
use Models\Vote\Vote;
use Models\Notifications\Notification;
use Models\Activite_log\Activite;
use PDO;

class ReglementController
{
    private Reglement $reglementModel;
    private Vote $voteModel;
    private Notification $notificationModel;
    private Activite $activiteModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->reglementModel = new Reglement($this->pdo);
        $this->voteModel = new Vote($this->pdo);
        $this->notificationModel = new Notification($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
    }

    /**
     * Proposer un nouveau règlement.
     */
    public function proposerReglement()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-reglement");
            exit;
        }

        $data = [
            'titre' => htmlspecialchars($_POST['titre']),
            'description' => htmlspecialchars($_POST['description']),
            'montant_amende' => intval($_POST['montant_amende']),
            'type_infraction' => $_POST['type_infraction'],
            'statut' => 'reflexion',
            'propose_par' => $_SESSION['user']['id']
        ];

        $this->reglementModel->create($data);

        // Notifier tout le monde
        // (En pratique on bouclerait sur les users validés, ici on simplifie)
        $this->activiteModel->log($_SESSION['user']['id'], 'reglement_propose', "Nouveau règlement proposé : {$data['titre']}");

        header("Location: /page-rule?msg=proposition_envoyee");
        exit;
    }

    /**
     * Voter pour un règlement.
     */
    public function voter()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-rule");
            exit;
        }

        $reglement_id = intval($_POST['reglement_id']);
        $choix = $_POST['choix'];
        $joueur_id = $_SESSION['user']['id'];

        try {
            $this->voteModel->create([
                'reglement_id' => $reglement_id,
                'joueur_id' => $joueur_id,
                'choix' => $choix
            ]);

            // Vérifier si majorité (simplifié : si + de 5 votes OUI, on active)
            $results = $this->voteModel->getResults($reglement_id);
            $oui = 0;
            foreach ($results as $res) {
                if ($res['choix'] === 'oui') $oui = $res['total'];
            }

            if ($oui >= 5) {
                $this->reglementModel->updateStatut($reglement_id, 'actif');
                $this->activiteModel->log($joueur_id, 'reglement_actif', "Règlement #$reglement_id passé au statut ACTIF après vote.");
            }

            header("Location: /page-rule?msg=vote_enregistre");
        } catch (\Exception $e) {
            header("Location: /page-rule?msg=deja_vote");
        }
        exit;
    }
}
