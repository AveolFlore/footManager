<?php

namespace Controllers\Admin;

use Config\Database;
use Models\Utilisateur\User;
use PDO;

require_once __DIR__ . '/../../middleware/Role.php';

class AdminController
{
    private User $userModel;
    private Database $database;
    private PDO $pdo;

    public function __construct()
    {
        $this->database  = new Database;
        $this->pdo       = $this->database->connect();
        $this->userModel = new User($this->pdo);
    }

    // VALIDER + ASSIGNER EQUIPE
    public function validateUser()
    {
        requireRole('president');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/page-admin");
            exit;
        }

        $userId   = intval($_POST['user_id']);
        $equipeId = intval($_POST['equipe_id']);

        $this->userModel->validateAndAssignTeam($userId, $equipeId);

        header("Location:/page-admin?msg=joueur_valide");
        exit;
    }

    // REFUSER
    public function refuseUser()
    {
        requireRole('president');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/page-admin");
            exit;
        }

        $userId = intval($_POST['user_id']);

        $this->userModel->refuseUser($userId);

        header("Location:/page-admin?msg=joueur_refuse");
        exit;
    }
}