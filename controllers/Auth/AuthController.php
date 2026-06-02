<?php

namespace Controllers\Auth;

use Config\Database;
use Models\Utilisateur\User;
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

        // validation nom/prenom (lettres seulement)
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $data['nom'])) {
            header("Location:/page-register-joueur?msg=Nom invalide");
            exit;
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $data['prenom'])) {
            header("Location:/page-register-joueur?msg=Prénom invalide");
            exit;
        }

        // validation téléphone (chiffres seulement)
        if (!empty($data['telephone']) && !preg_match("/^[0-9+\s]+$/", $data['telephone'])) {
            header("Location:/page-register-joueur?msg=Téléphone invalide");
            exit;
        }

        // validation numéro maillot
        if (!empty($data['numero_maillot'])) {
            $numeroMaillot = intval($data['numero_maillot']);
            if ($numeroMaillot < 1 || $numeroMaillot > 999) {
                header("Location:/page-register-joueur?msg=Numéro maillot invalide");
                exit;
            }
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
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        if (!$email) {
    header("Location:/page-register-joueur?msg=Email invalide");
    exit;
}

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
            'date_naissance' => $data['date_naissance'] ?? date('Y-m-d'),
            'poste' => $data['poste'] ?? null,
            'pied_dominant' => $data['pied_dominant'] ?? null,
            'numero_maillot' => !empty($data['numero_maillot']) ? intval($data['numero_maillot']) : null,

            // FORCÉS AUTOMATIQUEMENT
            'role' => 'joueur',
            'statut' => 'en_attente',
            'equipe_id' => null,

            'mot_de_passe' => $data['mot_de_passe']
        ];

        // ================= INSERTION =================
        $result = $this->userModel->create($userData, $file['photo_profil'] ?? null);

        if ($result) {
            header("Location:/page-redirect?msg=Inscription réussie, attente validation.");
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

        $email = trim($_POST['email']) ?? '';
        $mdp   = trim($_POST['mot_de_passe']) ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            header("Location:/page-login?msg=Utilisateur non trouvé");
            exit;
        }

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {

            if ($user['statut'] !== 'valide') {

                if ($user['statut'] === 'en_attente') {
                    header("Location:/page-redirect?msg=Votre compte est en cours de traitement..");
                }

                if ($user['statut'] === 'refuse') {
                    header("Location:/page-login?msg=Compte refusé");
                }

                exit;
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role'],
                'statut' => $user['statut'],
                'photo' => $user['photo_profil']
            ];

            if ($user['role'] === 'president') {
    header("Location:/page-admin");
} else {
    header("Location:/page-home");
}
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

        // Détruire le cookie de session sur le navigateur
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Détruire la session sur le serveur
        session_destroy();

        header("Location:/page-login?msg=Deconnexion avec success");
        exit;
    }

    // =========================================================
    // ADMIN CREATE USER
    // =========================================================
    public function createUserByAdmin(array $data)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require_once __DIR__ . '/../../middleware/Admin.php';

        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            header("Location:/page-admincreateuser");
            exit;
        }

        if (
            empty($data['nom']) ||
            empty($data['prenom']) ||
            empty($data['email']) ||
            empty($data['mot_de_passe']) ||
            empty($data['role'])
        ) {
            header("Location:/page-admincreateuser?msg=Champs requis");
            exit;
        }

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        if ($this->userModel->findByEmail($email)) {
            header("Location:/page-admincreateuser?msg=Email déjà utilisé");
            exit;
        }

        $userData = [
            'nom' => htmlspecialchars(trim($data['nom'])),
            'prenom' => htmlspecialchars(trim($data['prenom'])),
            'email' => $email,
            'telephone' => $data['telephone'] ?? null,
            'date_naissance' => $data['date_naissance'] ?? null,
            'poste' => $data['poste'] ?? null,
            'pied_dominant' => $data['pied_dominant'] ?? null,
            'numero_maillot' => $data['numero_maillot'] ?? null,

            'role' => $data['role'],
            'statut' => 'valide',
            'equipe_id' => null,

            'mot_de_passe' => $data['mot_de_passe'] // brut → hash dans model
        ];

        $result = $this->userModel->create($userData);

        if ($result) {
            header("Location:/page-admin?msg=Utilisateur créé");
        } else {
            header("Location:/page-admincreateuser?msg=Erreur");
        }

        exit;
    }
}
