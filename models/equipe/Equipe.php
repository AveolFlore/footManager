<?php

namespace Models\Equipe;

use PDO;

class Equipe
{
    private PDO $db;

    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    // Crée une nouvelle équipe dans la base de données
    public function createEquipe(string $nom, string $couleur, string $categorie, string $date_creation): bool
    {
        // On remplace 'type' par 'couleur' pour correspondre à la DB
        $sql = "INSERT INTO equipe (nom, couleur, categorie, date_creation) 
                VALUES (:nom, :couleur, :categorie, :date_creation)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':nom'           => $nom,
            ':couleur'       => $couleur,
            ':categorie'     => $categorie,
            ':date_creation' => $date_creation
        ]);
    }

    public function getAll(): array
    {
        // La table s'appelle 'equipe' (singulier) comme vu sur ta capture
        $sql = "SELECT * FROM equipe ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère une équipe par son ID
    public function findById(int $id): ?array
    {
        // Requête SQL pour sélectionner une équipe avec l'ID donné
        $sql = "SELECT * FROM equipe WHERE id = :id";
        // Préparation de la requête
        $stmt = $this->db->prepare($sql);
        // Exécution avec le paramètre ID
        $stmt->execute([':id' => $id]);
        // Retourne le résultat sous forme de tableau associatif ou null si pas trouvé
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Met à jour une équipe dans la base de données
    public function update(int $id, string $nom, string $couleur, string $categorie, string $date_creation): bool
    {
        // Requête SQL pour mettre à jour une équipe
        $sql = "UPDATE equipe 
                SET nom = :nom, couleur = :couleur, categorie = :categorie, date_creation = :date_creation 
                WHERE id = :id";
        // Préparation de la requête
        $stmt = $this->db->prepare($sql);
        // Exécution avec les paramètres
        return $stmt->execute([
            ':id'            => $id,
            ':nom'           => $nom,
            ':couleur'       => $couleur,
            ':categorie'     => $categorie,
            ':date_creation' => $date_creation
        ]);
    }

    // Supprime une équipe de la base de données
    public function delete(int $id): bool
    {
        // Requête SQL pour supprimer une équipe
        $sql = "DELETE FROM equipe WHERE id = :id";
        // Préparation de la requête
        $stmt = $this->db->prepare($sql);
        // Exécution avec le paramètre ID
        return $stmt->execute([':id' => $id]);
    }

    // Recherche des équipes par nom
    public function searchByName(string $searchTerm): array
    {
        // Requête SQL pour rechercher des équipes dont le nom contient le terme de recherche
        $sql = "SELECT * FROM equipe WHERE nom LIKE :searchTerm ORDER BY date_creation DESC";
        // Préparation de la requête
        $stmt = $this->db->prepare($sql);
        // Exécution avec le paramètre (ajout des % pour la recherche partielle)
        $stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
        // Retourne les résultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}