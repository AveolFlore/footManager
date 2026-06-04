<?php

namespace Controllers\Equipe;

use Config\Database; // Import de la classe du chef de projet
use Models\Equipe\Equipe;

// On vérifie si une session est déjà active avant de la lancer
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class EquipeController
{
    private $pdo;
    private $equipeModel;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->equipeModel = new Equipe($this->pdo);
    }

    public function store()
    {
        // 1. SÉCURITÉ : On vérifie si l'utilisateur est connecté et s'il est ADMIN
        // (Adapté selon votre variable de session : ex: $_SESSION['role'])
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            die("Accès refusé : réservé à l'administrateur.");
        }

        // 2. Vérification si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyage des données entrantes (XSS protection)
            $nom = htmlspecialchars($_POST['nom']);
            $type = htmlspecialchars($_POST['type']); // 'A' ou 'B'
            $categorie = htmlspecialchars($_POST['categorie']);

            // 3. Appel au modèle pour enregistrer
            if ($this->equipeModel->createEquipe($nom, $type, $categorie)) {
                header('Location: /liste-equipes?success=1'); // Redirection si succès
            } else {
                echo "Erreur lors de la création.";
            }
        }
    }

    //  affiche la page du formulaire

    public function create()
    {
        // Sécurité : Vérifier si l'admin est connecté avant d'afficher
        // if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
        //     header('Location: /login'); // Redirige si pas admin
        //     exit();
        // }

        // Appel de la vue
        require_once __DIR__ . '/../../views/pages/admin/creer_equipe.php';
    }
    // Affiche la liste (Historique)
    public function teamPage()
    {
        if (function_exists('\requireRole')) {
            \requireRole('president');
        }

        // Vérifie s'il y a un terme de recherche dans la requête GET
        $searchTerm = $_GET['search'] ?? '';

        // Si un terme de recherche est présent, on utilise la méthode searchByName
        if (!empty($searchTerm)) {
            $equipes = $this->equipeModel->searchByName($searchTerm);
        } else {
            // Sinon, on récupère toutes les équipes
            $equipes = $this->equipeModel->getAll();
        }

        require_once __DIR__ . '/../../views/pages/historique_equipe/index.php';
    }

    // Affiche le formulaire

    public function createEquipePage()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Correction du chemin vers la session : $_SESSION['user']['role']
        $userRole = $_SESSION['user']['role'] ?? null;

        if (!$userRole || $userRole !== 'president') {
            header('Location: /page-login?msg=Acces_interdit_President_uniquement');
            exit();
        }

        require_once __DIR__ . '/../../views/pages/creation_equipe/creer_equipe.php';
    }

    // Traite l'enregistrement
    // Dans Controllers/Admin/AdminController.php

    public function storeEquipe($data)
    {
        if (function_exists('\requireRole')) {
            \requireRole('president');
        }

        // Utilisation correcte de $this->equipeModel initialisé dans le constructeur
        if ($this->equipeModel->createEquipe($data['nom'], $data['couleur'], $data['categorie'], $data['date_creation'])) {
            header('Location: /admin-team?msg=success');
            exit();
        }
    }

    // Affiche le formulaire de modification avec les données existantes
    public function editEquipePage($id)
    {
        // Vérifie si une session est active
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Récupère le rôle de l'utilisateur
        $userRole = $_SESSION['user']['role'] ?? null;

        // Vérifie si l'utilisateur est président
        if (!$userRole || $userRole !== 'president') {
            header('Location: /page-login?msg=Acces_interdit_President_uniquement');
            exit();
        }

        // Récupère les données de l'équipe à modifier via le modèle
        $equipe = $this->equipeModel->findById($id);

        // Inclut la vue en passant les données de l'équipe
        require_once __DIR__ . '/../../views/pages/creation_equipe/creer_equipe.php';
    }

    // Traite la mise à jour de l'équipe
    public function updateEquipe($data, $id)
    {
        // Vérifie le rôle de l'utilisateur
        if (function_exists('\requireRole')) {
            \requireRole('president');
        }

        // Appelle la méthode update du modèle avec les données nettoyées
        if ($this->equipeModel->update(
            $id,
            htmlspecialchars($data['nom']),
            htmlspecialchars($data['couleur']),
            htmlspecialchars($data['categorie']),
            htmlspecialchars($data['date_creation'])
        )) {
            // Redirige vers la liste des équipes avec un message de succès
            header('Location: /admin-team?msg=updated');
            exit();
        }
    }

    // Traite la suppression de l'équipe
    public function deleteEquipe($id)
    {
        // Vérifie le rôle de l'utilisateur
        if (function_exists('\requireRole')) {
            \requireRole('president');
        }

        // Appelle la méthode delete du modèle
        if ($this->equipeModel->delete($id)) {
            // Redirige vers la liste des équipes avec un message de succès
            header('Location: /admin-team?msg=deleted');
            exit();
        }
    }

    // Traite la recherche AJAX et renvoie du JSON
    public function searchAJAX()
    {
        // Vérifie le rôle de l'utilisateur
        if (function_exists('\requireRole')) {
            \requireRole('president');
        }

        // Récupère le terme de recherche
        $searchTerm = $_GET['search'] ?? '';

        // Récupère les équipes
        if (!empty($searchTerm)) {
            $equipes = $this->equipeModel->searchByName($searchTerm);
        } else {
            $equipes = $this->equipeModel->getAll();
        }

        // Définit l'en-tête pour renvoyer du JSON
        header('Content-Type: application/json');
        // Encode les équipes en JSON et les affiche
        echo json_encode($equipes);
        exit();
    }

    // Affiche les détails d'une équipe
    public function showDetails($id)
    {
        if (!$id) {
            header('Location: /admin-team');
            exit;
        }

        //  On récupère les infos de l'équipe pour le titre de la page
        $equipe = $this->equipeModel->findById($id);
        if (!$equipe) {
            header('Location: /admin-team?msg=equipe_non_trouvee');
            exit;
        }

        // On récupère UNIQUEMENT les joueurs liés à cet ID d'équipe
        $query = "SELECT nom, prenom, email, date_naissance, poste, pied_dominant 
              FROM users 
              WHERE equipe_id = :equipe_id 
              AND role = 'joueur'";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['equipe_id' => $id]);
        $joueurs = $stmt->fetchAll();

        // On charge la vue en lui passant les données
        require_once __DIR__ . '/../../views/pages/historique_equipe/details_equipe.php';
    }
}
