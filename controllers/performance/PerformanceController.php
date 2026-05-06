<?php

namespace Controllers\Performance;

use Config\Database;
use Models\Performance\Performance;
use Models\Activite_log\Activite;
use PDO;

class PerformanceController
{
    private Performance $performanceModel;
    private Activite $activiteModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->performanceModel = new Performance($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
    }

    /**
     * Enregistrer les performances d'une séance par lots.
     */
    public function enregistrerPerformances()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-match");
            exit;
        }

        $seance_id = intval($_POST['seance_id']);
        $perfs = $_POST['perfs'] ?? [];
        $admin_id = $_SESSION['user']['id'];

        foreach ($perfs as $joueur_id => $data) {
            $buts = intval($data['buts'] ?? 0);
            $passes = intval($data['passes'] ?? 0);

            if ($buts > 0 || $passes > 0) {
                $this->performanceModel->create([
                    'seance_id' => $seance_id,
                    'joueur_id' => $joueur_id,
                    'buts' => $buts,
                    'passes' => $passes
                ]);
            }
        }

        $this->activiteModel->log($admin_id, 'performance_enregistree', "Performances enregistrées pour la séance #$seance_id");

        header("Location: /page-match-detail?id=$seance_id&msg=perfs_enregistrees");
        exit;
    }
}
