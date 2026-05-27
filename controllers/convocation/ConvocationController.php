<?php

namespace Controllers\Convocation;

use Config\Database;
use Models\Convocation\EquipeType;
use Models\Convocation\Convocation;
use Models\MatchSeance\MatchSeance;

class ConvocationController
{
    #appelle du model de la convocation
    private Convocation $convocationModel;
    private ?MatchSeance $matchSeanceModel = null;

    private Database $database;
    private \PDO $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->database = new Database();
        $this->pdo = $this->database->connect();
        $this->convocationModel = new Convocation($this->pdo);
        $this->matchSeanceModel = new MatchSeance($this->pdo);
    }

    private function sanitize(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Affiche la page des convocations
     * Appelée par le router via l'action 'convocation'
     */
    public function convocationPage()
    {
        $filters = [
            'nom'       => $_GET['search_nom'] ?? '',
            'equipe'    => $_GET['filter_equipe'] ?? '',
            'score_min' => $_GET['filter_score'] ?? ''
        ];
        $qualifiedPlayers = $this->convocationModel->qualifyPlayer($filters);
        $summonedMap = $this->convocationModel->getConvocationsMap();
        $stmt = $this->pdo->query("SELECT id, date, lieu, description, type FROM match_seance WHERE statut IN ('publie', 'planifie') ORDER BY date DESC");
        $matches = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $matchesById = [];
        foreach ($matches as $m) {
            $matchesById[$m['id']] = $m;
        }
        require_once __DIR__ . '/../../views/pages/convocation/index.php';
    }

    /**
     * Reçoit les données du formulaire et exécute l'ajout en base de données
     */
    public function invokePlayer()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();

            if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) === 'joueur') {
                header("Location: /page-home?msg=AccesRefuse");
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $match_id = $_POST['match_id'] ?? null;
                $joueur_id = $_POST['joueur_id'] ?? null;
                $equipe_val = $_POST['equipe'] ?? null;

                if ($match_id && $joueur_id && $equipe_val) {
                    $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM convocation WHERE match_id = :m_id AND joueur_id = :j_id");
                    $checkStmt->execute([':m_id' => $match_id, ':j_id' => $joueur_id]);
                    if ($checkStmt->fetchColumn() > 0) {
                        header("Location: /Convocation-convocation?msg=already_summoned");
                        exit;
                    }

                    if ($this->convocationModel->hasOverlap((int)$joueur_id, (int)$match_id)) {
                        header("Location: /Convocation-convocation?msg=overlap");
                        exit;
                    }

                    $equipeEnum = EquipeType::from($equipe_val);

                    $this->convocationModel->addConvocation(
                        $match_id,
                        $joueur_id,
                        $equipeEnum
                    );
                }

                header("Location: /Convocation-convocation?msg=success");
                exit;
            }
        } catch (\ValueError $e) {
            echo "Équipe invalide : " . $e->getMessage();
        } catch (\PDOException $e) {
            echo "Erreur base de données : " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Erreur générale : " . $e->getMessage();
        }
    }

    // READ — convoqués d'un match
    public function index(int $match_id): array
    {
        return $this->convocationModel->read_by_match($match_id);
    }

    // SUGGESTION AUTO — plus-value ⑦
    public function get_suggestion(): array
    {
        return $this->convocationModel->get_suggestion();
    }

    // CREATE — enregistrer les convocations + publier le match
    public function store(array $data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $match_id = (int) $data['match_id'];
            if (!empty($data['joueurs'])) {
                foreach ($data['joueurs'] as $joueur_id => $info) {
                    if (isset($info['selectionne']) && $info['selectionne'] == '1') {
                        $this->convocationModel->create([
                            'match_id'       => $match_id,
                            'joueur_id'      => (int) $joueur_id,
                            'equipe_match'   => $this->sanitize($info['equipe']),
                            'est_capitaine'  => isset($info['capitaine']) ? 1 : 0,
                            'numero_maillot' => (int) $info['maillot']
                        ]);
                    }
                }

                $this->matchSeanceModel->publier($match_id);

                header('Location: /page-matchdetail?id=' . $match_id . '&msg=Convocations enregistrées');
                exit;
            } else {
                header('Location: /page-matchconvocations?id=' . $match_id . '&msg=Sélectionnez au moins un joueur');
                exit;
            }
        } else {
            header('Location: /page-match?msg=Méthode non autorisée');
            exit;
        }
    }

    // DELETE — retirer un joueur d'un match
    public function destroy(int $id)
    {
        $result = $this->convocationModel->delete_one($id);
        if ($result) {
            header('Location: /page-matchconvocations?msg=Joueur retiré');
            exit;
        } else {
            header('Location: /page-matchconvocations?msg=Erreur lors de la suppression');
            exit;
        }
    }
}
