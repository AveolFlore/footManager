<?php

namespace Controllers\Performance;

use Config\Database;
use Models\Performance\Performance;
use Models\Utilisateur\User;
use PDO;

class PerformanceController
{
    private Performance $perfModel;
    private User $userModel;
    private Database $database;
    private PDO $pdo;

    public function __construct()
    {
        $this->database  = new Database;
        $this->pdo       = $this->database->connect();
        $this->perfModel = new Performance($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    /**
     * Affiche la page des performances
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Sécurité : Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header("Location:/auth-login");
            exit;
        }

        $currentUser = $_SESSION['user'];
        
        // Déterminer quel joueur on regarde (soi-même ou un joueur spécifique si admin)
        $joueurId = $_GET['id'] ?? $currentUser['id'];

        // Récupérer les informations du joueur sélectionné avec son équipe
        $selectedJoueur = $this->userModel->getFindIdWithTeam($joueurId);
        
        // Récupérer la liste de tous les joueurs avec leur équipe
        $allJoueurs = $this->userModel->readAllWithTeam();

        // Récupérer les statistiques
        $globalStats = $this->perfModel->getGlobalStats($joueurId);
        $evolutionData = $this->perfModel->getEvolution($joueurId);
        $teamAverages = $this->perfModel->getTeamAverages();
        $matchPerformances = $this->perfModel->getPerformanceByMatch($joueurId);

        // Préparer les données pour Chart.js
        $chartLabels = [];
        $chartNotes = [];
        $chartButs = [];
        $chartPasses = [];

        foreach ($evolutionData as $data) {
            $chartLabels[] = date('d M', strtotime($data['date']));
            $chartNotes[] = $data['note'];
            $chartButs[] = $data['buts'];
            $chartPasses[] = $data['passes'];
        }

        // Charger la vue
        require_once "../views/pages/performance/index.php";
    }
}
