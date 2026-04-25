<?php

namespace Models;

use PDO;

class User
{
    private PDO $conn;
    private string $table = "users";

    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $mot_de_passe;
    public string $telephone;
    public string $photo_profil;
    public string $date_naissance;
    public string $poste;
    public string $pied_dominant;
    public ?int $numero_maillot;
    public string $role;
    public ?int $equipe_id;
    public string $statut;
    public string $date_inscription;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================================================
    // LISTE USERS
    // =========================================================
    public function readAll()
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // FIND BY EMAIL
    // =========================================================
    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CREATE USER
    // =========================================================
    public function create($data, $file = null)
    {
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->email = $data['email'];
        $this->telephone = $data['telephone'] ?? null;
        $this->date_naissance = $data['date_naissance'];
        $this->poste = $data['poste'] ?? null;
        $this->pied_dominant = $data['pied_dominant'] ?? null;
        $this->numero_maillot = $data['numero_maillot'] ?? null;
        $this->role = $data['role'] ?? 'joueur';
        $this->equipe_id = $data['equipe_id'] ?? null;
        $this->statut = $data['statut'] ?? 'en_attente';
        $this->date_inscription = date('Y-m-d');

        $passwordHash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        // Upload photo
        $photoPath = null;
        if ($file && $file['error'] === 0) {
            $photoPath = $this->uploadPhoto($file);
        }

        $sql = "INSERT INTO {$this->table}
        (nom, prenom, email, mot_de_passe, telephone, photo_profil,
        date_naissance, poste, pied_dominant, numero_maillot,
        role, equipe_id, statut, date_inscription)
        VALUES
        (:nom, :prenom, :email, :mot_de_passe, :telephone, :photo_profil,
        :date_naissance, :poste, :pied_dominant, :numero_maillot,
        :role, :equipe_id, :statut, :date_inscription)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":nom" => $this->nom,
            ":prenom" => $this->prenom,
            ":email" => $this->email,
            ":mot_de_passe" => $passwordHash,
            ":telephone" => $this->telephone,
            ":photo_profil" => $photoPath,
            ":date_naissance" => $this->date_naissance,
            ":poste" => $this->poste,
            ":pied_dominant" => $this->pied_dominant,
            ":numero_maillot" => $this->numero_maillot,
            ":role" => $this->role,
            ":equipe_id" => $this->equipe_id,
            ":statut" => $this->statut,
            ":date_inscription" => $this->date_inscription
        ]);
    }

    // =========================================================
    // FIND BY ID
    // =========================================================
    public function getFindId(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // DELETE USER
    // =========================================================
    public function delete(int $id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // =========================================================
    // UPDATE ROLE / STATUT / EQUIPE
    // =========================================================
    public function update(int $id, array $data)
    {
        $sql = "UPDATE {$this->table}
                SET role = :role,
                    statut = :statut,
                    equipe_id = :equipe_id
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":role" => $data['role'],
            ":statut" => $data['statut'],
            ":equipe_id" => $data['equipe_id'] ?? null,
            ":id" => $id
        ]);
    }

    // =========================================================
    // UPDATE PROFIL (nom, email, téléphone)
    // =========================================================
    public function updateProfile(int $id, array $data, $file = null)
    {
        $photoPath = null;

        if ($file && $file['error'] === 0) {
            $photoPath = $this->uploadPhoto($file);

            $query = "UPDATE {$this->table}
                      SET nom = :nom,
                          prenom = :prenom,
                          email = :email,
                          telephone = :telephone,
                          photo_profil = :photo_profil
                      WHERE id = :id";
        } else {
            $query = "UPDATE {$this->table}
                      SET nom = :nom,
                          prenom = :prenom,
                          email = :email,
                          telephone = :telephone
                      WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);

        $params = [
            ":nom" => $data['nom'],
            ":prenom" => $data['prenom'],
            ":email" => $data['email'],
            ":telephone" => $data['telephone'],
            ":id" => $id
        ];

        if ($photoPath) {
            $params[":photo_profil"] = $photoPath;
        }

        return $stmt->execute($params);
    }

    // =========================================================
    // UPDATE PASSWORD
    // =========================================================
    public function updatePassword(int $id, string $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = "UPDATE {$this->table} SET mot_de_passe = :mdp WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":mdp" => $hash,
            ":id" => $id
        ]);
    }

    // =========================================================
    // UPLOAD PHOTO PROFIL
    // =========================================================
    private function uploadPhoto($file)
    {
        $uploadDir = "uploads/profiles/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($file["name"]);
        $targetPath = $uploadDir . $fileName;

        move_uploaded_file($file["tmp_name"], $targetPath);

        return $targetPath;
    }
}