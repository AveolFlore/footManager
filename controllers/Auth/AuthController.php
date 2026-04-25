<?php

namespace Controllers\Auth;

use Config\Database;
use Models\User;
use PDO;

class AuthController
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

    // =========================================================
    // INSCRIPTION JOUEUR
    // =========================================================
    public function registerJoueur(array $data, array $file)
    {
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            header("Location:/page-register-joueur?msg=echec");
            exit;
        }

        // ================= VALIDATION =================
        if (
            empty($data['nom']) ||
            empty($data['prenom']) ||
            empty($data['email']) ||
            empty($data['mot_de_passe']) ||
            empty($data['confirm_mot_de_passe']) ||
            empty($data['date_naissance'])
        ) {
            header("Location:/page-register-joueur?msg=Tous les champs obligatoires");
            exit;
        }

        // confirmation mot de passe
        if ($data['mot_de_passe'] !== $data['confirm_mot_de_passe']) {
            header("Location:/page-register-joueur?msg=Les mots de passe ne correspondent pas");
            exit;
        }

        // longueur mot de passe
        if (strlen($data['mot_de_passe']) < 6) {
            header("Location:/page-register-joueur?msg=Mot de passe trop court");
            exit;
        }

        // email sanitize
        $email = htmlspecialchars(trim($data['email']));

        // vérification email existant
        if ($this->userModel->findByEmail($email)) {
            header("Location:/page-register-joueur?msg=Email déjà utilisé");
            exit;
        }

        // ================= DONNÉES USER =================
        $userData = [
            'nom' => htmlspecialchars(trim($data['nom'])),
            'prenom' => htmlspecialchars(trim($data['prenom'])),
            'email' => $email,
            'telephone' => $data['telephone'] ?? null,
            'date_naissance' => $data['date_naissance'],
            'poste' => $data['poste'] ?? null,
            'pied_dominant' => $data['pied_dominant'] ?? null,
            'numero_maillot' => $data['numero_maillot'] ?? null,

            // FORCÉS AUTOMATIQUEMENT
            'role' => 'joueur',
            'statut' => 'en_attente',
            'equipe_id' => null,

            'mot_de_passe' => $data['mot_de_passe']
        ];

        // ================= INSERTION =================
        $result = $this->userModel->create($userData, $file['photo_profil'] ?? null);

        if ($result) {
            header("Location:/page-login?msg=Inscription réussie");
        } else {
            header("Location:/page-register-joueur?msg=Erreur lors de l'inscription");
        }
        exit;
    }

    // =========================================================
    // LOGIN (inchangé mais propre table utilisateur)
    // =========================================================
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            header("Location:/page-login");
            exit;
        }

        $email = $_POST['email'] ?? '';
        $mdp   = $_POST['mdp'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            header("Location:/page-login?msg=Utilisateur non trouvé");
            exit;
        }

        if (password_verify($mdp, $user['mot_de_passe'])) {

            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role'],
                'photo' => $user['photo_profil']
            ];

            header("Location:/page-home");
            exit;
        }

        header("Location:/page-login?msg=Mot de passe incorrect");
        exit;
    }

    // =========================================================
    // LOGOUT
    // =========================================================
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location:/page-login");
        exit;
    }
}