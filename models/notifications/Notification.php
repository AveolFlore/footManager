<?php

namespace Models\Notifications;

use PDO;

class Notification
{
    private PDO $conn;
    private string $table = "notifications";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crée une notification pour un utilisateur.
     */
    public function create(array $data)
    {
        $query = "INSERT INTO {$this->table} (destinataire_id, type, message, lien, date_creation) 
                  VALUES (:destinataire_id, :type, :message, :lien, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':destinataire_id' => $data['destinataire_id'],
            ':type' => $data['type'],
            ':message' => $data['message'],
            ':lien' => $data['lien'] ?? null
        ]);
    }

    /**
     * Récupère les notifications non lues d'un utilisateur.
     */
    public function getUnread($user_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE destinataire_id = ? AND lu = FALSE ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte les notifications non lues.
     */
    public function countUnread($user_id)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE destinataire_id = ? AND lu = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Marquer comme lue.
     */
    public function markAsRead($id)
    {
        $query = "UPDATE {$this->table} SET lu = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
