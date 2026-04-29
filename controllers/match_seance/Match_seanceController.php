<?php
namespace Controllers;
session_start();

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
                'createur_id' => (int) $_SESSION['user_id']
            ];

            if (!empty($data['type']) && !empty($data['date']) && !empty($data['lieu'])) {

                if (!empty($data['add_match']) && $data['add_match'] == 'Créer') {
                    $result = $this->matchSeanceModel->create($validate);

                    if ($result) {
                        header('Location: matches/list.php?msg=Séance créée avec succès');
                        exit;
                    } else {
                        header('Location: matches/create.php?msg=Erreur lors de la création');
                        exit;
                    }
                }

            } else {
                header('Location: matches/create.php?msg=Tous les champs sont requis');
                exit;
            }

        } else {
            header('Location: matches/create.php?msg=Méthode non autorisée');
            exit;
        }
    }

    // EDIT — charger en session pour pré-remplir le formulaire
    public function edit(int $id)
    {
        $result = $this->matchSeanceModel->read_one($id);

        $_SESSION['match_id']          = $result['id'];
        $_SESSION['match_type']        = $result['type'];
        $_SESSION['match_date']        = $result['date'];
        $_SESSION['match_lieu']        = $result['lieu'];
        $_SESSION['match_description'] = $result['description'];

        header('Location: matches/edit.php');
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

            if (!empty($data['date']) && !empty($data['lieu'])) {

                if (!empty($data['update_match']) && $data['update_match'] == 'Modifier') {
                    $id = (int) $data['match_id'];
                    $result = $this->matchSeanceModel->update($id, $validate);

                    if ($result) {
                        $this->initialize();
                        header('Location: matches/list.php?msg=Séance modifiée avec succès');
                        exit;
                    } else {
                        header('Location: matches/edit.php?msg=Erreur lors de la modification');
                        exit;
                    }

                } elseif (!empty($data['reset']) && $data['reset'] == 'Annuler') {
                    header('Location: matches/list.php');
                    exit;
                }

            } else {
                header('Location: matches/edit.php?msg=Tous les champs sont requis');
                exit;
            }

        } else {
            header('Location: matches/list.php?msg=Méthode non autorisée');
            exit;
        }
    }

    // PUBLIER
    public function publier(int $id)
    {
        $result = $this->matchSeanceModel->publier($id);

        if ($result) {
            header('Location: matches/detail.php?id=' . $id . '&msg=Match publié');
            exit;
        } else {
            header('Location: matches/detail.php?id=' . $id . '&msg=Erreur lors de la publication');
            exit;
        }
    }

    // TERMINER
    public function terminer(int $id)
    {
        $result = $this->matchSeanceModel->terminer($id);

        if ($result) {
            header('Location: matches/detail.php?id=' . $id . '&msg=Match clôturé');
            exit;
        } else {
            header('Location: matches/detail.php?id=' . $id . '&msg=Erreur lors de la clôture');
            exit;
        }
    }

    // DELETE
    public function destroy(int $id)
    {
        $result = $this->matchSeanceModel->delete_one($id);

        if ($result) {
            header('Location: matches/list.php?delete=success');
            exit;
        } else {
            header('Location: matches/list.php?delete=error');
            exit;
        }
    }
}