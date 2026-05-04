<?php

namespace Controllers\Tache;

use Config\Database;
use Models\Tache\Tache;
use Models\Tache\CategorieTache;
use PDO;

require_once __DIR__ . '/../../middleware/Role.php';

class TacheController
{
    private Tache $tacheModel;
    private CategorieTache $categorieModel;
    private PDO $pdo;

    public function __construct()
    {
        $db                   = new Database();
        $this->pdo            = $db->connect();
        $this->tacheModel     = new Tache($this->pdo);
        $this->categorieModel = new CategorieTache($this->pdo);

        // Sync automatique des retards à chaque chargement du module
        $this->tacheModel->syncRetards();
    }

    // =========================================================
    // INDEX — liste toutes les tâches
    // =========================================================
    public function index()
    {
        requireLogin();

        $filtres = [
            'statut'       => $_GET['statut']       ?? null,
            'categorie_id' => $_GET['categorie_id'] ?? null,
            'assigne_a'    => $_GET['assigne_a']     ?? null,
            'priorite'     => $_GET['priorite']      ?? null,
        ];

        $taches     = $this->tacheModel->readAll($filtres);
        $categories = $this->categorieModel->readAll();

        require_once '../views/pages/tache/index.php';
    }

    // =========================================================
    // MES TÂCHES — vue personnelle du joueur
    // =========================================================
    public function mesTaches()
    {
        requireLogin();

        $joueurId = $_SESSION['user']['id'];
        $taches   = $this->tacheModel->readByJoueur($joueurId);

        require_once '../views/pages/tache/mes_taches.php';
    }

    // =========================================================
    // DETAIL — fiche complète d'une tâche
    // =========================================================
    public function detail()
    {
        requireLogin();

        $id    = intval($_GET['id'] ?? 0);
        $tache = $this->tacheModel->findById($id);

        if (!$tache) {
            header("Location:/tache-index?msg=introuvable");
            exit;
        }

        $commentaires = $this->tacheModel->getCommentaires($id);

        require_once '../views/pages/tache/detail.php';
    }

    // =========================================================
    // CREATE — formulaire (bureau uniquement)
    // =========================================================
    public function create()
    {
        requireLogin();

        $categories = $this->categorieModel->readAll();

        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom FROM users
             WHERE statut = 'valide'
             ORDER BY nom ASC"
        );
        $stmt->execute();
        $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $this->pdo->prepare(
            "SELECT id, type, date, lieu FROM match_seance
             WHERE date >= CURDATE()
             ORDER BY date ASC
             LIMIT 20"
        );
        $stmt2->execute();
        $seances = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        require_once '../views/pages/tache/create.php';
    }

    // =========================================================
    // STORE — enregistrer une nouvelle tâche (POST)
    // =========================================================
    public function store()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/tache-create");
            exit;
        }

        $data = [
            'titre'            => $_POST['titre']            ?? '',
            'description'      => $_POST['description']      ?? null,
            'categorie_id'     => $_POST['categorie_id']     ?? 0,
            'seance_id'        => !empty($_POST['seance_id']) ? $_POST['seance_id'] : null,
            'assigne_a'        => $_POST['assigne_a']        ?? 0,
            'assigne_par'      => $_SESSION['user']['id'],
            'priorite'         => $_POST['priorite']         ?? 'moyenne',
            'deadline'         => $_POST['deadline']         ?? '',
            'recurrente'       => $_POST['recurrente']       ?? 0,
            'intervalle_jours' => !empty($_POST['intervalle_jours']) ? $_POST['intervalle_jours'] : null,
        ];

        if (empty($data['titre']) || empty($data['categorie_id'])
            || empty($data['assigne_a']) || empty($data['deadline'])) {
            header("Location:/tache-create?msg=champs_manquants");
            exit;
        }

        $ok = $this->tacheModel->create($data);

        if ($ok) {
            $this->creerNotification(
                intval($data['assigne_a']),
                'tache',
                "Nouvelle tâche assignée : {$data['titre']}",
                '/tache-mesTaches'
            );
            $this->logActivite(
                $_SESSION['user']['id'],
                'tache_creee',
                "Tâche « {$data['titre']} » créée"
            );
        }

        header("Location:/tache-index?msg=" . ($ok ? 'tache_creee' : 'erreur'));
        exit;
    }

    // =========================================================
    // EDIT — formulaire modification
    // =========================================================
    public function edit()
    {
        requireLogin();

        $id    = intval($_GET['id'] ?? 0);
        $tache = $this->tacheModel->findById($id);

        if (!$tache) {
            header("Location:/tache-index?msg=introuvable");
            exit;
        }

        $categories = $this->categorieModel->readAll();

        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom FROM users
             WHERE statut = 'valide'
             ORDER BY nom ASC"
        );
        $stmt->execute();
        $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once '../views/pages/tache/edit.php';
    }

    // =========================================================
    // UPDATE — enregistrer la modification (POST)
    // =========================================================
    public function update()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/tache-index");
            exit;
        }

        $id   = intval($_POST['id'] ?? 0);
        $data = [
            'titre'            => $_POST['titre']            ?? '',
            'description'      => $_POST['description']      ?? null,
            'categorie_id'     => $_POST['categorie_id']     ?? 0,
            'assigne_a'        => $_POST['assigne_a']        ?? 0,
            'priorite'         => $_POST['priorite']         ?? 'moyenne',
            'deadline'         => $_POST['deadline']         ?? '',
            'recurrente'       => $_POST['recurrente']       ?? 0,
            'intervalle_jours' => !empty($_POST['intervalle_jours']) ? $_POST['intervalle_jours'] : null,
        ];

        $ok = $this->tacheModel->update($id, $data);

        $this->logActivite(
            $_SESSION['user']['id'],
            'tache_modifiee',
            "Tâche #$id modifiée"
        );

        header("Location:/tache-detail?id=$id&msg=" . ($ok ? 'tache_modifiee' : 'erreur'));
        exit;
    }

    // =========================================================
    // CLÔTURER — joueur marque sa tâche terminée (POST)
    // =========================================================
    public function cloturer()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/tache-mesTaches");
            exit;
        }

        $id          = intval($_POST['id'] ?? 0);
        $joueurId    = $_SESSION['user']['id'];
        $commentaire = $_POST['commentaire_cloture'] ?? null;

        $ok = $this->tacheModel->cloturer($id, $joueurId, $commentaire);

        if ($ok) {
            $this->tacheModel->creerOccurrenceSuivante($id);
            $this->logActivite($joueurId, 'tache_terminee', "Tâche #$id terminée");
        }

        header("Location:/tache-mesTaches?msg=" . ($ok ? 'tache_terminee' : 'erreur'));
        exit;
    }

    // =========================================================
    // COMMENTER — ajouter un commentaire (POST)
    // =========================================================
    public function commenter()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/tache-index");
            exit;
        }

        $tacheId  = intval($_POST['tache_id'] ?? 0);
        $auteurId = $_SESSION['user']['id'];
        $message  = $_POST['message'] ?? '';

        if (empty($message)) {
            header("Location:/tache-detail?id=$tacheId&msg=message_vide");
            exit;
        }

        $this->tacheModel->ajouterCommentaire($tacheId, $auteurId, $message);

        header("Location:/tache-detail?id=$tacheId");
        exit;
    }

    // =========================================================
    // KANBAN — vue visuelle par statut
    // =========================================================
    public function kanban()
    {
        requireLogin();

        $colonnes = $this->tacheModel->readGroupedByStatut();

        require_once '../views/pages/tache/kanban.php';
    }

    // =========================================================
    // STATS — score d'implication par joueur
    // =========================================================
    public function stats()
    {
        requireLogin();

        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, photo_profil FROM users
             WHERE statut = 'valide'
             ORDER BY nom ASC"
        );
        $stmt->execute();
        $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($joueurs as &$j) {
            $j['stats'] = $this->tacheModel->getStatsByJoueur($j['id']);
        }

        require_once '../views/pages/tache/stats.php';
    }

    // =========================================================
    // SUPPRIMER — président uniquement (POST)
    // =========================================================
    public function supprimer()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/tache-index");
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $ok = $this->tacheModel->delete($id);

        $this->logActivite(
            $_SESSION['user']['id'],
            'tache_supprimee',
            "Tâche #$id supprimée"
        );

        header("Location:/tache-index?msg=" . ($ok ? 'tache_supprimee' : 'erreur'));
        exit;
    }

    // =========================================================
    // API JSON — widget dashboard
    // =========================================================
    public function api()
    {
        requireLogin();

        $limite = intval($_GET['limit'] ?? 3);
        $taches = $this->tacheModel->getTachesDuJour($limite);

        header('Content-Type: application/json');
        echo json_encode($taches);
        exit;
    }

    // =========================================================
    // HELPERS PRIVÉS
    // =========================================================
    private function creerNotification(int $destinataireId, string $type, string $message, string $lien = ''): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notifications (destinataire_id, type, message, lien)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$destinataireId, $type, $message, $lien]);
    }

    private function logActivite(int $auteurId, string $typeAction, string $description): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO activite_log (auteur_id, type_action, description)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$auteurId, $typeAction, $description]);
    }
}