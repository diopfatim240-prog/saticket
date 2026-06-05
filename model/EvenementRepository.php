<?php 
// CRUD FILE - Repository événements : create, read, update, delete
require_once("DBRepository.php");

class EvenementRepository extends DBRepository
{
    public function __construct() { 
        parent::__construct(); 
    }

    // Tous les événements (pour les acheteurs)
    public function getAll() {
        try {
            $sql = "SELECT id, titre, description, date, lieu, createdat, updatedat 
                    FROM evenements 
                    ORDER BY createdat DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAll : " . $e->getMessage());
            return [];
        }
    }

    // Événements d'un organisateur
    public function getAllByUser(int $userId) {
        try {
            $sql = "SELECT id, titre, description, date, lieu, createdat, updatedat 
                    FROM evenements 
                    WHERE id_organisateur = :userId 
                    ORDER BY createdat DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllByUser : " . $e->getMessage());
            return [];
        }
    }

    public function getEvenementsByOrganisateur(int $id_utilisateur) {
        return $this->getAllByUser($id_utilisateur);
    }

    public function getById(int $id) {
        try {
            $sql = "SELECT * FROM evenements WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getById : " . $e->getMessage());
            return null;
        }
    }

    public function add($titre, $lieu, $date, $description, $id_organisateur = null) {
        try {
            $sql = "INSERT INTO evenements (titre, lieu, date, description, id_organisateur, createdat, updatedat) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            return $this->db->prepare($sql)->execute([$titre, $lieu, $date, $description, (int)$id_organisateur]);
        } catch (PDOException $e) {
            error_log("Erreur add evenement : " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $titre, $lieu, $date, $description) {
        try {
            $sql = "UPDATE evenements SET titre = ?, lieu = ?, date = ?, description = ?, updatedat = NOW() WHERE id = ?";
            return $this->db->prepare($sql)->execute([$titre, $lieu, $date, $description, (int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur update evenement : " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        try {
            return $this->db->prepare("DELETE FROM evenements WHERE id = ?")->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur delete evenement : " . $e->getMessage());
            throw $e;
        }
    }

    public function getTicketStatsForEvent(int $id_evenement): array {
        try {
            $stmtTotal = $this->db->prepare(
                "SELECT COALESCE(SUM(quantite_totale), SUM(quantite), 0) AS total FROM tickets WHERE id_evenements = ?"
            );
            $stmtTotal->execute([$id_evenement]);
            $total = (int)($stmtTotal->fetchColumn() ?: 0);

            $vendus = 0;
            try {
                $stmtVendus = $this->db->prepare(
                    "SELECT COALESCE(SUM(GREATEST(0, quantite_totale - quantite)), 0) AS vendus FROM tickets WHERE id_evenements = ?"
                );
                $stmtVendus->execute([$id_evenement]);
                $vendus = (int)($stmtVendus->fetchColumn() ?: 0);
            } catch (PDOException $e) {
                $vendus = 0;
            }

            return ['total' => $total, 'vendus' => $vendus];
        } catch (PDOException $e) {
            error_log("Erreur getTicketStatsForEvent : " . $e->getMessage());
            return ['total' => 0, 'vendus' => 0];
        }
    }
}