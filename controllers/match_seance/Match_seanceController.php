<?php

namespace Controllers\Match_seance;

use Config\Database;
use Models\Activite_log\Activite;
use Models\Match_seance\MatchEntity;
use PDO;

class Match_seanceController
{
    private MatchEntity $matchModel;
    private Activite $activiteModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->matchModel = new MatchEntity($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
    }

    /**
     * Créer une nouvelle séance (match ou entraînement).
     */
    public function creerMatch()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-match");
            exit;
        }

        $data = [
            'type' => $_POST['type'],
            'date' => $_POST['date'],
            'lieu' => htmlspecialchars($_POST['lieu']),
            'description' => htmlspecialchars($_POST['description'] ?? ''),
            'statut' => 'planifie',
            'createur_id' => $_SESSION['user']['id']
        ];

        $this->matchModel->create($data);

        $this->activiteModel->log($_SESSION['user']['id'], 'match_cree', "Nouvel événement créé : {$data['type']} le {$data['date']} à {$data['lieu']}");

        header("Location: /page-match?msg=evenement_cree");
        exit;
    }
}
