<?php
namespace Controllers;
session_start();

use Models\ResultatMatch;
use Models\MatchSeance;
use Config\Database;

class ResultatMatchController
{
    private ResultatMatch $resultatMatchModel;
    private MatchSeance $matchSeanceModel;
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
        $pdo = $this->db->connect();
        $this->resultatMatchModel = new ResultatMatch($pdo);
        $this->matchSeanceModel    = new MatchSeance($pdo);
    }

    private function sanitize(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // READ — résultat d'un match
    public function index(int $match_id)
    {
        return $this->resultatMatchModel->read_by_match($match_id);
    }

    // CREATE — saisir le score après le match
    public function store(array $data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $validate = [
                'match_id'      => (int) $data['match_id'],
                'buts_equipe_a' => (int) $this->sanitize($data['buts_equipe_a']),
                'buts_equipe_b' => (int) $this->sanitize($data['buts_equipe_b']),
                'saisie_par'    => (int) $_SESSION['user_id']
            ];

            if (isset($data['buts_equipe_a']) && isset($data['buts_equipe_b'])) {

                if (!empty($data['save_resultat']) && $data['save_resultat'] == 'Enregistrer') {
                    $result = $this->resultatMatchModel->create($validate);

                    if ($result) {
                        $this->matchSeanceModel->terminer($validate['match_id']);
                        header('Location: matches/detail.php?id=' . $validate['match_id'] . '&msg=Résultat enregistré');
                        exit;
                    } else {
                        header('Location: matches/detail.php?id=' . $validate['match_id'] . '&msg=Erreur lors de la saisie');
                        exit;
                    }
                }

            } else {
                header('Location: matches/detail.php?id=' . $data['match_id'] . '&msg=Les scores sont requis');
                exit;
            }

        } else {
            header('Location: matches/list.php?msg=Méthode non autorisée');
            exit;
        }
    }

    // UPDATE — corriger un résultat mal saisi
    public function update(array $data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $match_id = (int) $data['match_id'];

            $validate = [
                'buts_equipe_a' => (int) $this->sanitize($data['buts_equipe_a']),
                'buts_equipe_b' => (int) $this->sanitize($data['buts_equipe_b'])
            ];

            if (isset($data['buts_equipe_a']) && isset($data['buts_equipe_b'])) {

                if (!empty($data['update_resultat']) && $data['update_resultat'] == 'Corriger') {
                    $result = $this->resultatMatchModel->update($match_id, $validate);

                    if ($result) {
                        header('Location: matches/detail.php?id=' . $match_id . '&msg=Résultat corrigé');
                        exit;
                    } else {
                        header('Location: matches/detail.php?id=' . $match_id . '&msg=Erreur lors de la correction');
                        exit;
                    }
                }

            } else {
                header('Location: matches/detail.php?id=' . $match_id . '&msg=Les scores sont requis');
                exit;
            }

        } else {
            header('Location: matches/list.php?msg=Méthode non autorisée');
            exit;
        }
    }
}