<?php
namespace Models\Performance;

use PDO;

class Performance
{
    private PDO $conn;
    private string $table = "performance";

    public int $id;
    public int $seance_id;
    public int $joueur_id;
    public int $buts;
    public int $passes;
    public int $points_total;
    public string $date_enregistrement;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Récupère les statistiques globales d'un joueur
     */
    public function getGlobalStats(int $joueurId): array
    {
        $sql = "SELECT 
                    COUNT(id) as total_matchs,
                    SUM(buts) as total_buts,
                    SUM(passes) as total_passes,
                    AVG(buts * 3 + passes * 2) as note_moyenne,
                    (SELECT COUNT(*) FROM match_seance ms 
                     JOIN resultat_match rm ON rm.match_id = ms.id 
                     JOIN performance p ON p.seance_id = ms.id 
                     WHERE p.joueur_id = :joueur_id 
                     AND (
                        (rm.equipe_gagnante = 'A' AND p.equipe_type = 'A') OR 
                        (rm.equipe_gagnante = 'B' AND p.equipe_type = 'B')
                     )) as victoires
                FROM {$this->table}
                WHERE joueur_id = :joueur_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':joueur_id' => $joueurId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcul des ratios
        if ($stats && $stats['total_matchs'] > 0) {
            $stats['buts_par_match'] = round($stats['total_buts'] / $stats['total_matchs'], 1);
            $stats['passes_par_match'] = round($stats['total_passes'] / $stats['total_matchs'], 1);
            $stats['win_rate'] = round(($stats['victoires'] / $stats['total_matchs']) * 100);
            $stats['note_moyenne'] = round($stats['note_moyenne'] / 10, 1); // Normalisé sur 10
        } else {
            $stats = [
                'total_matchs' => 0, 'total_buts' => 0, 'total_passes' => 0, 
                'note_moyenne' => 0, 'victoires' => 0, 'buts_par_match' => 0, 
                'passes_par_match' => 0, 'win_rate' => 0
            ];
        }

        return $stats;
    }

    /**
     * Récupère l'évolution des performances pour le graphique
     */
    public function getEvolution(int $joueurId): array
    {
        $sql = "SELECT 
                    date_enregistrement as date,
                    buts,
                    passes,
                    ROUND((buts * 3 + passes * 2) / 10, 1) as note
                FROM {$this->table}
                WHERE joueur_id = :joueur_id
                ORDER BY date_enregistrement ASC
                LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':joueur_id' => $joueurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les moyennes de l'équipe pour la comparaison
     */
    public function getTeamAverages(): array
    {
        $sql = "SELECT 
                    AVG(buts) as avg_buts,
                    AVG(passes) as avg_passes
                FROM {$this->table}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'avg_buts' => round($res['avg_buts'] ?? 0, 1),
            'avg_passes' => round($res['avg_passes'] ?? 0, 1)
        ];
    }

    /**
     * Récupère le détail des performances par match pour un joueur
     */
    public function getPerformanceByMatch(int $joueurId): array
    {
        $sql = "SELECT 
                    ms.date,
                    ms.lieu,
                    ms.type,
                    p.buts,
                    p.passes,
                    p.points_total,
                    ROUND((p.buts * 3 + p.passes * 2) / 10, 1) as note,
                    rm.buts_equipe_a,
                    rm.buts_equipe_b,
                    rm.equipe_gagnante,
                    p.equipe_type
                FROM {$this->table} p
                JOIN match_seance ms ON p.seance_id = ms.id
                LEFT JOIN resultat_match rm ON rm.match_id = ms.id
                WHERE p.joueur_id = :joueur_id
                ORDER BY ms.date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':joueur_id' => $joueurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre une nouvelle performance
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (seance_id, joueur_id, buts, passes, date_enregistrement)
                VALUES (:seance_id, :joueur_id, :buts, :passes, :date_enregistrement)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':seance_id' => $data['seance_id'],
            ':joueur_id' => $data['joueur_id'],
            ':buts' => $data['buts'] ?? 0,
            ':passes' => $data['passes'] ?? 0,
            ':date_enregistrement' => date('Y-m-d')
        ]);
    }
}
