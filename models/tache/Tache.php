<?php

namespace Models\Tache;

use PDO;

class Tache
{
    private PDO $conn;
    private string $table = "tache";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // =========================================================
    // TOUTES LES TÂCHES (avec jointures pour affichage)
    // =========================================================
    public function readAll(array $filtres = []): array
    {
        $sql = "SELECT
                    t.*,
                    c.nom        AS categorie_nom,
                    c.couleur    AS categorie_couleur,
                    c.icone      AS categorie_icone,
                    u1.nom       AS joueur_nom,
                    u1.prenom    AS joueur_prenom,
                    u1.photo_profil AS joueur_photo,
                    u2.nom       AS bureau_nom,
                    u2.prenom    AS bureau_prenom,
                    ms.date      AS seance_date,
                    ms.type      AS seance_type
                FROM {$this->table} t
                JOIN categorie_tache c  ON c.id  = t.categorie_id
                JOIN users u1           ON u1.id = t.assigne_a
                JOIN users u2           ON u2.id = t.assigne_par
                LEFT JOIN match_seance ms ON ms.id = t.seance_id
                WHERE 1=1";

        $params = [];

        if (!empty($filtres['statut'])) {
            $sql .= " AND t.statut = :statut";
            $params[':statut'] = $filtres['statut'];
        }

        if (!empty($filtres['categorie_id'])) {
            $sql .= " AND t.categorie_id = :categorie_id";
            $params[':categorie_id'] = $filtres['categorie_id'];
        }

        if (!empty($filtres['assigne_a'])) {
            $sql .= " AND t.assigne_a = :assigne_a";
            $params[':assigne_a'] = $filtres['assigne_a'];
        }

        if (!empty($filtres['priorite'])) {
            $sql .= " AND t.priorite = :priorite";
            $params[':priorite'] = $filtres['priorite'];
        }

        $sql .= " ORDER BY
                    FIELD(t.statut, 'en_retard', 'a_faire', 'en_cours', 'termine'),
                    FIELD(t.priorite, 'haute', 'moyenne', 'faible'),
                    t.deadline ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // TÂCHES D'UN JOUEUR (vue mes_taches)
    // =========================================================
    public function readByJoueur(int $joueurId): array
    {
        $sql = "SELECT
                    t.*,
                    c.nom     AS categorie_nom,
                    c.couleur AS categorie_couleur,
                    c.icone   AS categorie_icone,
                    ms.date   AS seance_date,
                    ms.type   AS seance_type
                FROM {$this->table} t
                JOIN categorie_tache c    ON c.id  = t.categorie_id
                LEFT JOIN match_seance ms ON ms.id = t.seance_id
                WHERE t.assigne_a = ?
                  AND t.statut != 'termine'
                ORDER BY
                    FIELD(t.statut, 'en_retard', 'a_faire', 'en_cours'),
                    t.deadline ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$joueurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // FIND BY ID (fiche complète)
    // =========================================================
    public function findById(int $id): array|false
    {
        $sql = "SELECT
                    t.*,
                    c.nom        AS categorie_nom,
                    c.couleur    AS categorie_couleur,
                    c.icone      AS categorie_icone,
                    u1.nom       AS joueur_nom,
                    u1.prenom    AS joueur_prenom,
                    u1.photo_profil AS joueur_photo,
                    u2.nom       AS bureau_nom,
                    u2.prenom    AS bureau_prenom,
                    ms.date      AS seance_date,
                    ms.type      AS seance_type
                FROM {$this->table} t
                JOIN categorie_tache c    ON c.id  = t.categorie_id
                JOIN users u1             ON u1.id = t.assigne_a
                JOIN users u2             ON u2.id = t.assigne_par
                LEFT JOIN match_seance ms ON ms.id = t.seance_id
                WHERE t.id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CRÉER UNE TÂCHE
    // =========================================================
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                    (titre, description, categorie_id, seance_id,
                     assigne_a, assigne_par, priorite, statut,
                     deadline, recurrente, intervalle_jours)
                VALUES
                    (:titre, :description, :categorie_id, :seance_id,
                     :assigne_a, :assigne_par, :priorite, 'a_faire',
                     :deadline, :recurrente, :intervalle_jours)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':titre'            => htmlspecialchars(trim($data['titre'])),
            ':description'      => $data['description']    ?? null,
            ':categorie_id'     => intval($data['categorie_id']),
            ':seance_id'        => !empty($data['seance_id']) ? intval($data['seance_id']) : null,
            ':assigne_a'        => intval($data['assigne_a']),
            ':assigne_par'      => intval($data['assigne_par']),
            ':priorite'         => $data['priorite']        ?? 'moyenne',
            ':deadline'         => $data['deadline'],
            ':recurrente'       => !empty($data['recurrente']) ? 1 : 0,
            ':intervalle_jours' => !empty($data['intervalle_jours']) ? intval($data['intervalle_jours']) : null,
        ]);
    }

    // =========================================================
    // MODIFIER UNE TÂCHE (bureau uniquement)
    // =========================================================
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET titre            = :titre,
                    description      = :description,
                    categorie_id     = :categorie_id,
                    assigne_a        = :assigne_a,
                    priorite         = :priorite,
                    deadline         = :deadline,
                    recurrente       = :recurrente,
                    intervalle_jours = :intervalle_jours
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':titre'            => htmlspecialchars(trim($data['titre'])),
            ':description'      => $data['description']    ?? null,
            ':categorie_id'     => intval($data['categorie_id']),
            ':assigne_a'        => intval($data['assigne_a']),
            ':priorite'         => $data['priorite']        ?? 'moyenne',
            ':deadline'         => $data['deadline'],
            ':recurrente'       => !empty($data['recurrente']) ? 1 : 0,
            ':intervalle_jours' => !empty($data['intervalle_jours']) ? intval($data['intervalle_jours']) : null,
            ':id'               => $id,
        ]);
    }

    // =========================================================
    // CHANGER LE STATUT
    // =========================================================
    public function changerStatut(int $id, string $statut): bool
    {
        $sql  = "UPDATE {$this->table} SET statut = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$statut, $id]);
    }

    // =========================================================
    // CLÔTURER UNE TÂCHE (joueur marque terminée)
    // =========================================================
    public function cloturer(int $id, int $joueurId, ?string $commentaire): bool
    {
        $sql = "UPDATE {$this->table}
                SET statut               = 'termine',
                    date_cloture         = NOW(),
                    commentaire_cloture  = :commentaire
                WHERE id = :id
                  AND assigne_a = :joueur_id
                  AND statut != 'termine'";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':commentaire' => $commentaire ?? null,
            ':id'          => $id,
            ':joueur_id'   => $joueurId,
        ]);
    }

    // =========================================================
    // MARQUER LES TÂCHES EN RETARD (appeler en début de page)
    // =========================================================
    public function syncRetards(): void
    {
        $sql = "UPDATE {$this->table}
                SET statut = 'en_retard'
                WHERE statut IN ('a_faire', 'en_cours')
                  AND deadline < NOW()";

        $this->conn->exec($sql);
    }

    // =========================================================
    // TÂCHES EN RETARD (pour le bureau / censeur)
    // =========================================================
    public function readEnRetard(): array
    {
        return $this->readAll(['statut' => 'en_retard']);
    }

    // =========================================================
    // PROCHAIN JOUEUR EN ROTATION (le moins chargé ce mois)
    // =========================================================
    public function getProchainJoueurRotation(int $categorieId): array|false
    {
        $sql = "SELECT
                    u.id,
                    u.nom,
                    u.prenom,
                    COUNT(t.id) AS nb_taches_ce_mois
                FROM users u
                LEFT JOIN {$this->table} t
                    ON  t.assigne_a    = u.id
                    AND t.categorie_id = :categorie_id
                    AND MONTH(t.date_creation) = MONTH(NOW())
                    AND YEAR(t.date_creation)  = YEAR(NOW())
                WHERE u.statut = 'valide'
                  AND u.role   = 'joueur'
                GROUP BY u.id
                ORDER BY nb_taches_ce_mois ASC, RAND()
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':categorie_id' => $categorieId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CRÉER LA PROCHAINE OCCURRENCE (tâche récurrente)
    // =========================================================
    public function creerOccurrenceSuivante(int $tacheId): bool
    {
        // Récupérer la tâche source
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE id = ? AND recurrente = 1 LIMIT 1"
        );
        $stmt->execute([$tacheId]);
        $tache = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tache || !$tache['intervalle_jours']) {
            return false;
        }

        // Calculer la prochaine deadline
        $prochaineDeadline = date(
            'Y-m-d H:i:s',
            strtotime($tache['deadline'] . ' +' . $tache['intervalle_jours'] . ' days')
        );

        // Trouver le prochain joueur en rotation
        $prochain = $this->getProchainJoueurRotation($tache['categorie_id']);

        if (!$prochain) {
            return false;
        }

        // Insérer la nouvelle occurrence
        return $this->create([
            'titre'            => $tache['titre'],
            'description'      => $tache['description'],
            'categorie_id'     => $tache['categorie_id'],
            'seance_id'        => $tache['seance_id'],
            'assigne_a'        => $prochain['id'],
            'assigne_par'      => $tache['assigne_par'],
            'priorite'         => $tache['priorite'],
            'deadline'         => $prochaineDeadline,
            'recurrente'       => 1,
            'intervalle_jours' => $tache['intervalle_jours'],
        ]);
    }

    // =========================================================
    // STATS PAR JOUEUR (score d'implication)
    // =========================================================
    public function getStatsByJoueur(int $joueurId): array
    {
        $sql = "SELECT
                    COUNT(*)                                        AS total_assignees,
                    SUM(statut = 'termine')                        AS total_terminees,
                    SUM(statut = 'en_retard')                      AS total_retards,
                    SUM(statut IN ('a_faire','en_cours'))           AS total_en_cours,
                    ROUND(
                        SUM(statut = 'termine') /
                        NULLIF(COUNT(*), 0) * 100
                    , 1)                                           AS taux_ponctualite
                FROM {$this->table}
                WHERE assigne_a = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$joueurId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // TÂCHES DU JOUR (widget dashboard — 3 prochaines)
    // =========================================================
    public function getTachesDuJour(int $limite = 3): array
    {
        $sql = "SELECT
                    t.id,
                    t.titre,
                    t.statut,
                    t.deadline,
                    t.priorite,
                    u.nom    AS joueur_nom,
                    u.prenom AS joueur_prenom,
                    c.icone  AS categorie_icone,
                    c.couleur AS categorie_couleur
                FROM {$this->table} t
                JOIN users u           ON u.id = t.assigne_a
                JOIN categorie_tache c ON c.id = t.categorie_id
                WHERE t.statut != 'termine'
                ORDER BY
                    FIELD(t.statut, 'en_retard', 'a_faire', 'en_cours'),
                    t.deadline ASC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // SUPPRIMER UNE TÂCHE
    // =========================================================
    public function delete(int $id): bool
    {
        $sql  = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // =========================================================
    // COMMENTAIRES D'UNE TÂCHE
    // =========================================================
    public function getCommentaires(int $tacheId): array
    {
        $sql = "SELECT
                    ct.*,
                    u.nom        AS auteur_nom,
                    u.prenom     AS auteur_prenom,
                    u.role       AS auteur_role,
                    u.photo_profil AS auteur_photo
                FROM commentaire_tache ct
                JOIN users u ON u.id = ct.auteur_id
                WHERE ct.tache_id = ?
                ORDER BY ct.date_message ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tacheId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // AJOUTER UN COMMENTAIRE
    // =========================================================
    public function ajouterCommentaire(int $tacheId, int $auteurId, string $message): bool
    {
        $sql = "INSERT INTO commentaire_tache (tache_id, auteur_id, message)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $tacheId,
            $auteurId,
            htmlspecialchars(trim($message)),
        ]);
    }

    // =========================================================
    // TABLEAU DE BORD KANBAN (groupé par statut)
    // =========================================================
    public function readGroupedByStatut(): array
    {
        $toutes = $this->readAll();

        return [
            'a_faire'   => array_filter($toutes, fn($t) => $t['statut'] === 'a_faire'),
            'en_cours'  => array_filter($toutes, fn($t) => $t['statut'] === 'en_cours'),
            'termine'   => array_filter($toutes, fn($t) => $t['statut'] === 'termine'),
            'en_retard' => array_filter($toutes, fn($t) => $t['statut'] === 'en_retard'),
        ];
    }
}