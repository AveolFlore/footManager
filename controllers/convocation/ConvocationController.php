<?php 

namespace Controllers\Convocation;

use Config\Database;
use Models\EquipeType;
use Models\Utilisateur\User;
use Models\Convocation;

class ConvocationController {
#appelle du model de la convocation
private  Convocation $convocationModel;

private Database $database;

private \PDO $pdo;

public function __construct(){
    // si la session est nul ont la démarre
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $this->database = new Database();
    $this->pdo = $this->database->connect();
    $this->convocationModel = new Convocation($this->pdo);
}

/**
 * Affiche la page des convocations
 * Appelée par le router via l'action 'convocation'
 */
public function convocationPage(){
    // On récupère les données pour la vue
    $filters = [
        'nom'       => $_GET['search_nom'] ?? '',
        'equipe'    => $_GET['filter_equipe'] ?? '',
        'score_min' => $_GET['filter_score'] ?? ''
    ];
// fonction récupérer dans le model  pour l'affichage des joueurs qualifiers
    $qualifiedPlayers = $this->convocationModel->qualifyPlayer($filters);
    
    // Récupération des matchs disponibles (publiés et à venir uniquement)
    $stmt = $this->pdo->query("SELECT id, date, lieu, description FROM match_seance WHERE type = 'match' AND statut = 'publie' AND date >= CURDATE() ORDER BY date ASC");
    $matches = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // $pageTitle = "Gestion des Convocations";

    // Chargement de la vue
    require_once __DIR__ . '/../../views/pages/convocation/index.php';
}

/**
 * Reçoit les données du formulaire et exécute l'ajout en base de données
 */
public function invokePlayer()
{
    try {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Sécurité : Seuls les rôles non-joueurs peuvent convoquer
        if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) === 'joueur') {
            header("Location: /page-home?msg=AccesRefuse");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $match_id = $_POST['match_id'] ?? null;
            $joueur_id = $_POST['joueur_id'] ?? null;
            $equipe_val = $_POST['equipe'] ?? null; // 'A' ou 'B'

            if ($match_id && $joueur_id && $equipe_val) {

                // Vérification anti-doublon : le joueur est-il déjà convoqué pour ce match ?
                $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM convocation WHERE match_id = :m_id AND joueur_id = :j_id");
                $checkStmt->execute([':m_id' => $match_id, ':j_id' => $joueur_id]);
                if ($checkStmt->fetchColumn() > 0) {
                    header("Location: /page-home?msg=already_summoned");
                    exit;
                }

                // Conversion vers l'enum
                $equipeEnum = EquipeType::from($equipe_val);

                // Appel du modèle
                $this->convocationModel->addConvocation(
                    $match_id,
                    $joueur_id,
                    $equipeEnum
                );
            }

            // Redirection après succès
            header("Location: /convocation-convocation?msg=success");
            exit;
        }
    } catch (\ValueError $e) {
        // erreur possible si EquipeType::from reçoit une mauvaise valeur
        echo "Équipe invalide : " . $e->getMessage();
    } catch (\PDOException $e) {
        echo "Erreur base de données : " . $e->getMessage();
    } catch (\Exception $e) {
        echo "Erreur générale : " . $e->getMessage();
    }
}
}