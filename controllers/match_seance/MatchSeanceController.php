<?php

namespace Controllers;

use Models\MatchSeance;
use Config\Database;

class MatchSeanceController
{
    private MatchSeance $matchSeanceModel;
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->matchSeanceModel = new MatchSeance($this->db->connect());
    }

    private function sanitize(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    private function initialize()
    {
        unset($_SESSION['match_id']);
        unset($_SESSION['match_type']);
        unset($_SESSION['match_date']);
        unset($_SESSION['match_lieu']);
        unset($_SESSION['match_description']);
    }

    // READ ALL
    public function index(): array
    {
        return $this->matchSeanceModel->read();
    }

    // READ ONE
    public function read_one(int $id)
    {
        return $this->matchSeanceModel->read_one($id);
    }

    // CREATE
    public function store(array $data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validate = [
                'type'        => $this->sanitize($data['type']),
                'date'        => $this->sanitize($data['date']),
                'lieu'        => $this->sanitize($data['lieu']),
                'description' => $this->sanitize($data['description']),
                'createur_id' => (int) $_SESSION['user']['id']
            ];

            // Convert datetime-local to MySQL DATETIME format
            if (!empty($validate['date'])) {
                $validate['date'] = str_replace('T', ' ', $validate['date']) . ':00';
            }

            if (!empty($data['type']) && !empty($data['date']) && !empty($data['lieu'])) {

                if (!empty($data['add_match']) && $data['add_match'] == 'Créer') {
                    // vérifier que la date est dans le futur
                    if (strtotime($validate['date']) <= time()) {
                        header('Location: /page-matchcreate?msg=La date doit être dans le futur');
                        exit;
                    }
                    $result = $this->matchSeanceModel->create($validate);

                    if ($result) {
                        header('Location: /page-match?msg=Séance créée avec succès');
                        exit;
                    } else {
                        header('Location: /page-matchcreate?msg=Erreur lors de la création');
                        exit;
                    }
                }
            } else {
                header('Location: /page-matchcreate?msg=Tous les champs sont requis');
                exit;
            }
        } else {
            header('Location: /page-matchcreate?msg=Méthode non autorisée');
            exit;
        }
    }

    // EDIT
    public function edit(int $id)
    {
        $result = $this->matchSeanceModel->read_one($id);

        $_SESSION['match_id']          = $result['id'];
        $_SESSION['match_type']        = $result['type'];
        $_SESSION['match_date']        = $result['date'];
        $_SESSION['match_lieu']        = $result['lieu'];
        $_SESSION['match_description'] = $result['description'];

        header('Location: /page-matchedit');
        exit;
    }

    // UPDATE
    public function update(array $data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $validate = [
                'date'        => $this->sanitize($data['date']),
                'lieu'        => $this->sanitize($data['lieu']),
                'description' => $this->sanitize($data['description'])
            ];

            // Convert datetime-local to MySQL DATETIME format
            if (!empty($validate['date'])) {
                $validate['date'] = str_replace('T', ' ', $validate['date']) . ':00';
            }

            if (!empty($data['date']) && !empty($data['lieu'])) {

                if (!empty($data['update_match']) && $data['update_match'] == 'Modifier') {
                    $id = (int) $data['match_id'];
                    $result = $this->matchSeanceModel->update($id, $validate);

                    if ($result) {
                        $this->initialize();
                        header('Location: /page-match?msg=Séance modifiée avec succès');
                        exit;
                    } else {
                        header('Location: /page-matchedit?msg=Erreur lors de la modification');
                        exit;
                    }
                } elseif (!empty($data['reset']) && $data['reset'] == 'Annuler') {
                    header('Location: /page-match');
                    exit;
                }
            } else {
                header('Location: /page-matchedit?msg=Tous les champs sont requis');
                exit;
            }
        } else {
            header('Location: /page-match?msg=Méthode non autorisée');
            exit;
        }
    }

    // PUBLIER
    public function publier(int $id)
    {
        $result = $this->matchSeanceModel->publier($id);

        if ($result) {
            header('Location: /page-matchdetail?id=' . $id . '&msg=Match publié');
            exit;
        } else {
            header('Location: /page-matchdetail?id=' . $id . '&msg=Erreur lors de la publication');
            exit;
        }
    }

    // TERMINER
    public function terminer(int $id)
    {
        $match = $this->matchSeanceModel->read_one($id);
        $debut = strtotime($match['date']);
        $maintenant = time();

        if ($maintenant < $debut + (90 * 60)) {
            header('Location: /page-matchdetail?id=' . $id . '&msg=Le match ne peut pas être clôturé avant 1h30 après le début');
            exit;
        }

        $result = $this->matchSeanceModel->terminer($id);

        if ($result) {
            header('Location: /page-matchdetail?id=' . $id . '&msg=Match clôturé');
            exit;
        } else {
            header('Location: /page-matchdetail?id=' . $id . '&msg=Erreur lors de la clôture');
            exit;
        }
    }

    // DELETE
    public function destroy(int $id)
    {
        $result = $this->matchSeanceModel->delete_one($id);

        if ($result) {
            header('Location: /page-match?msg=Match supprimé avec succès');
            exit;
        } else {
            header('Location: /page-match?msg=Erreur lors de la suppression');
            exit;
        }
    }

    // DELETE (pour routeur)
    public function delete()
    {
        $id = (int) $_GET['id'];
        $result = $this->matchSeanceModel->delete_one($id);

        if ($result) {
            header('Location: /page-match?msg=Match supprimé avec succès');
            exit;
        } else {
            header('Location: /page-match?msg=Erreur lors de la suppression');
            exit;
        }
    }
}
