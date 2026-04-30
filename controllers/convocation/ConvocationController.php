<?php

namespace Controllers;

use Models\Convocation;
use Models\MatchSeance;
use Config\Database;

class ConvocationController
{
    private Convocation $convocationModel;
    private MatchSeance $matchSeanceModel;
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
        $pdo = $this->db->connect();
        $this->convocationModel = new Convocation($pdo);
        $this->matchSeanceModel = new MatchSeance($pdo);
    }

    private function sanitize(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
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
                    // enregistrer uniquement si checkbox cochée
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
