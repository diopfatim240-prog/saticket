<?php 
// CRUD FILE - Repository paiements : create, read, update
require_once("DBRepository.php");

class PaiementRepository extends DBRepository
{
    public function __construct() { 
        parent::__construct(); 
    }

    // Récupération blindée de tous les paiements
    public function getAllTransactions() {
        try {
            // Utilisation de LEFT JOIN pour ne jamais masquer les lignes de la table paiement
            $sql = "SELECT p.id, p.type, p.statut, p.id_commande, p.createdat,
                           c.montant AS montant,
                           u.nom AS nom,
                           u.prenom AS prenom
                    FROM paiement p
                    LEFT JOIN commandes c ON p.id_commande = c.id
                    LEFT JOIN utilisateurs u ON c.id_utilisateurs = u.id
                    ORDER BY p.id DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En cas d'erreur de colonne, on fait une requête de secours absolue pour afficher tes données
            error_log("Erreur jointure paiements : " . $e->getMessage());
            try {
                return $this->db->query("SELECT * FROM paiement ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                return [];
            }
        }
    }

    public function getAllTransactionsByUser(int $userId) {
        try {
            $sql = "SELECT p.id, p.type, p.statut, p.id_commande, p.createdat,
                           c.montant AS montant,
                           u.nom AS nom,
                           u.prenom AS prenom
                    FROM paiement p
                    LEFT JOIN commandes c ON p.id_commande = c.id
                    LEFT JOIN utilisateurs u ON c.id_utilisateurs = u.id
                    WHERE c.id_utilisateurs = :userId
                    ORDER BY p.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllTransactionsByUser : " . $e->getMessage());
            return [];
        }
    }

    // Sauvegarde ou mise à jour
    public function saveTransaction($id_commande, $type_paiement, $statut) {
        try {
            $check = $this->db->prepare("SELECT id FROM paiement WHERE id_commande = ?");
            $check->execute([$id_commande]);
            if ($check->fetch()) {
                $sql = "UPDATE paiement SET type = ?, statut = ?, updatedat = NOW() WHERE id_commande = ?";
                return $this->db->prepare($sql)->execute([$type_paiement, $statut, $id_commande]);
            } else {
                $sql = "INSERT INTO paiement (type, statut, id_commande, createdat, updatedat) VALUES (?, ?, ?, NOW(), NOW())";
                return $this->db->prepare($sql)->execute([$type_paiement, $statut, $id_commande]);
            }
        } catch (PDOException $e) {
            error_log("Erreur saveTransaction : " . $e->getMessage());
            return false;
        }
    }
}
?>