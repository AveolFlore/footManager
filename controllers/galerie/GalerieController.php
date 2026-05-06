<?php

namespace Controllers\Galerie;

use Config\Database;
use Models\Galerie\Galerie;
use Models\Activite_log\Activite;
use PDO;

class GalerieController
{
    private Galerie $galerieModel;
    private Activite $activiteModel;
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
        $this->galerieModel = new Galerie($this->pdo);
        $this->activiteModel = new Activite($this->pdo);
    }

    public function upload()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /page-galery");
            exit;
        }

        $titre = $_POST['titre'] ?? 'Sans titre';
        $description = $_POST['description'] ?? '';
        $seance_id = !empty($_POST['seance_id']) ? intval($_POST['seance_id']) : null;
        $admin_id = $_SESSION['user']['id'];

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newName = uniqid('img_', true) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/uploads/galerie/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newName)) {
                    $image_url = 'uploads/galerie/' . $newName;
                    
                    $this->galerieModel->create([
                        'titre' => $titre,
                        'description' => $description,
                        'seance_id' => $seance_id,
                        'image_url' => $image_url
                    ]);

                    $this->activiteModel->log($admin_id, 'galerie_upload', "Nouvelle photo ajoutée à la galerie : $titre");

                    header("Location: /page-galery?msg=upload_success");
                    exit;
                }
            }
        }

        header("Location: /page-galery?msg=upload_error");
        exit;
    }

    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $admin_id = $_SESSION['user']['id'];
            
            // On pourrait aussi supprimer le fichier physique ici
            $this->galerieModel->delete($id);
            $this->activiteModel->log($admin_id, 'galerie_delete', "Photo supprimée de la galerie #$id");

            header("Location: /page-galery?msg=delete_success");
            exit;
        }
        
        header("Location: /page-galery");
        exit;
    }
}
