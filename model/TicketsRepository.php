<?php 
// CRUD FILE - Repository tickets : create, read, update, delete
require_once("DBRepository.php");

class TicketsRepository extends DBRepository
{
    public function __construct() { parent::__construct(); }

    public function getAll() {
        try {
            $sql = "SELECT t.*, e.titre AS nom_evenement 
                    FROM tickets t
                    INNER JOIN evenements e ON t.id_evenements = e.id 
                    ORDER BY t.createdat DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository getAll : " . $e->getMessage());
            return [];
        }
    }

    public function add($type, $prix, $quantite, $id_evenements) {
        try {
            $sql = "INSERT INTO tickets (type, prix, total, id_evenements, est_valide, createdat) 
                    VALUES (?, ?, ?, ?, 1, NOW())";
            return $this->db->prepare($sql)->execute([$type, $prix, $quantite, $id_evenements]);
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository add : " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $prix, $quantite) {
        try {
            $sql = "UPDATE tickets SET prix = ?, total = ?, updatedat = NOW() WHERE id = ?";
            return $this->db->prepare($sql)->execute([$prix, $quantite, (int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository update : " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        try {
            return $this->db->prepare("DELETE FROM tickets WHERE id = ?")->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository delete : " . $e->getMessage());
            throw $e;
        }
    }

    public function getTicketsByEvent($id_evenements) {
        try {
            $sql = "SELECT * FROM tickets WHERE id_evenements = ? AND est_valide = 1 AND total > 0 ORDER BY prix ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$id_evenements]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository getTicketsByEvent : " . $e->getMessage());
            return [];
        }
    }

    public function getTicketsWithStatsByEvent(int $id_evenements): array {
        try {
            $sql = "SELECT id, type, prix, total AS quantite_totale, total AS quantite, 0 AS quantite_vendue
                    FROM tickets WHERE id_evenements = ? ORDER BY prix ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_evenements]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getTicketsWithStatsByEvent: " . $e->getMessage());
            return [];
        }
    }

    public function getEventTicketSummary(int $id_evenements): array {
        try {
            $sql  = "SELECT COALESCE(SUM(total), 0) AS total FROM tickets WHERE id_evenements = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_evenements]);
            $total = (int)($stmt->fetchColumn() ?: 0);
            return ['total' => $total, 'vendus' => 0];
        } catch (PDOException $e) {
            return ['total' => 0, 'vendus' => 0];
        }
    }

    public function getVentesParEvenement(): array {
        try {
            $sql = "SELECT e.id, e.titre,
                           COALESCE(SUM(t.total), 0) AS total,
                           0 AS vendus
                    FROM evenements e
                    INNER JOIN tickets t ON t.id_evenements = e.id
                    GROUP BY e.id, e.titre
                    ORDER BY e.titre ASC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getVentesParEvenement : " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tickets WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository getById : " . $e->getMessage());
            return null;
        }
    }

    public function decrementQuantity($ticketId, $qty) {
        try {
            $sql  = "UPDATE tickets SET total = total - ?, updatedat = NOW() WHERE id = ? AND total >= ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$qty, (int)$ticketId, (int)$qty]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur TicketsRepository decrementQuantity : " . $e->getMessage());
            return false;
        }
    }
}