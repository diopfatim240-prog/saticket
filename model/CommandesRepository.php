<?php 
// CRUD FILE - Repository commandes : create, read, delete
require_once("DBRepository.php");

class CommandesRepository extends DBRepository
{
    public function __construct() { 
        parent::__construct(); 
    }

    private function getUserIdColumn(): string {
        return $this->getExistingColumn('commandes', ['id_utilisateurs', 'id_utilisateur', 'id_user']) ?? 'id_utilisateurs';
    }

    private function getEvenementIdColumn(): string {
        return $this->getExistingColumn('commandes', ['id_evenements', 'id_evenement']) ?? 'id_evenements';
    }

    private function getTicketIdColumn(): string {
        return $this->getExistingColumn('commandes', ['id_tickets', 'id_ticket']) ?? 'id_tickets';
    }

    public function getPaymentStatusByCommandeId(int $commandeId): array {
        try {
            $sql = "SELECT p.id, p.type, p.statut, p.id_commande, p.createdat
                    FROM paiement p
                    WHERE p.id_commande = :commandeId
                    ORDER BY p.id DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['commandeId' => $commandeId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erreur getPaymentStatusByCommandeId : " . $e->getMessage());
            return [];
        }
    }

    public function getAllOrders() {
        $userColumn  = $this->getUserIdColumn();
        $eventColumn = $this->getEvenementIdColumn();
        try {
            $sql = "SELECT c.id, c.reference, c.quantite, c.montant, c.createdat,
                           IFNULL(u.nom, 'Client inconnu') AS client_nom,
                           IFNULL(e.titre, 'Événement inconnu') AS evenement_titre
                    FROM commandes c
                    LEFT JOIN utilisateurs u ON c.{$userColumn} = u.id
                    LEFT JOIN evenements e ON c.{$eventColumn} = e.id
                    ORDER BY c.id DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllOrders : " . $e->getMessage());
            return [];
        }
    }

    public function getOrdersByUser($id_user) {
        $userColumn  = $this->getUserIdColumn();
        $eventColumn = $this->getEvenementIdColumn();
        try {
            $sql = "SELECT c.id, c.reference, c.quantite, c.montant, c.createdat,
                           IFNULL(e.titre, 'Événement inconnu') AS evenement_titre
                    FROM commandes c
                    LEFT JOIN evenements e ON c.{$eventColumn} = e.id
                    WHERE c.{$userColumn} = ?
                    ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getOrdersByUser : " . $e->getMessage());
            return [];
        }
    }

    public function getOrdersByEvenement(int $id_evenement): array {
        $userColumn  = $this->getUserIdColumn();
        $eventColumn = $this->getEvenementIdColumn();
        try {
            $sql = "SELECT c.id, c.reference, c.quantite, c.montant, c.createdat,
                           IFNULL(CONCAT(u.nom), 'Acheteur inconnu') AS client_nom,
                           IFNULL(u.email, '') AS client_email,
                           IFNULL(e.titre, 'Événement inconnu') AS evenement_titre,
                           p.statut AS payment_statut,
                           p.type   AS payment_type
                    FROM commandes c
                    LEFT JOIN utilisateurs u ON c.{$userColumn} = u.id
                    LEFT JOIN evenements e   ON c.{$eventColumn} = e.id
                    LEFT JOIN paiement p     ON p.id_commande = c.id
                    WHERE c.{$eventColumn} = ?
                    ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_evenement]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getOrdersByEvenement : " . $e->getMessage());
            return [];
        }
    }

    public function getOrdersByOrganisateur($organisateurId) {
        $column = $this->getExistingColumn('evenements', ['id_organisateur', 'id_utilisateurs', 'organisateur_id']);
        if (!$column) return [];
        $userColumn  = $this->getUserIdColumn();
        $eventColumn = $this->getEvenementIdColumn();
        try {
            $sql = "SELECT c.*, e.titre AS evenement_titre 
                    FROM commandes c
                    JOIN evenements e ON c.{$eventColumn} = e.id
                    WHERE e.{$column} = ?
                    ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$organisateurId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function add($reference, $quantite, $montant, $id_user, $id_evenement, $id_ticket) {
        $userColumn   = $this->getUserIdColumn();
        $eventColumn  = $this->getEvenementIdColumn();
        $ticketColumn = $this->getTicketIdColumn();
        try {
            $sql = "INSERT INTO commandes (reference, quantite, montant, createdat, {$userColumn}, {$eventColumn}, {$ticketColumn})
                    VALUES (?, ?, ?, NOW(), ?, ?, ?)";
            return $this->db->prepare($sql)->execute([$reference, $quantite, $montant, $id_user, $id_evenement, $id_ticket]);
        } catch (PDOException $e) {
            error_log("Erreur add commande : " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            return $this->db->prepare("DELETE FROM commandes WHERE id = ?")->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur delete commande : " . $e->getMessage());
            return false;
        }
    }

    public function getCommandCountByEvenementIds(array $evenementIds): array {
        if (empty($evenementIds)) return [];
        $eventColumn  = $this->getEvenementIdColumn();
        $placeholders = implode(',', array_fill(0, count($evenementIds), '?'));
        try {
            $sql  = "SELECT {$eventColumn} AS id_evenements, COUNT(*) AS commande_count 
                     FROM commandes WHERE {$eventColumn} IN ($placeholders) GROUP BY {$eventColumn}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_map('intval', $evenementIds));
            $result = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $result[(int)$row['id_evenements']] = (int)$row['commande_count'];
            }
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getTicketsSoldByEvenementIds(array $evenementIds): array {
        if (empty($evenementIds)) return [];
        $eventColumn  = $this->getEvenementIdColumn();
        $placeholders = implode(',', array_fill(0, count($evenementIds), '?'));
        try {
            $sql  = "SELECT {$eventColumn} AS id_evenements, IFNULL(SUM(quantite), 0) AS tickets_sold
                     FROM commandes WHERE {$eventColumn} IN ($placeholders) GROUP BY {$eventColumn}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_map('intval', $evenementIds));
            $result = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $result[(int)$row['id_evenements']] = (int)$row['tickets_sold'];
            }
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }
}