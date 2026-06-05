<?php 
// CRUD FILE - Repository promotions : create, read, update, delete
require_once("DBRepository.php");

class PromotionsRepository extends DBRepository
{
    public function __construct() { parent::__construct(); }

    public function getAll() {
        try {
            $sql = "SELECT p.id, p.code, p.reduction, p.titre, p.description, 
                           p.date_debut, p.date_fin, p.est_actif, p.id_evenements,
                           e.titre AS evenement_titre
                    FROM promotions p
                    LEFT JOIN evenements e ON p.id_evenements = e.id
                    ORDER BY p.id DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur PromotionsRepository getAll : " . $e->getMessage());
            return [];
        }
    }

    public function add($code, $reduction, $titre = '', $description = '', $date_debut = null, $date_fin = null, $id_evenements = null) {
        try {
            $sql = "INSERT INTO promotions (code, reduction, titre, description, date_debut, date_fin, id_evenements, est_actif, createdat) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())";
            return $this->db->prepare($sql)->execute([
                $code, $reduction,
                $titre ?: $code,
                $description,
                $date_debut,
                $date_fin,
                $id_evenements
            ]);
        } catch (PDOException $e) {
            error_log("Erreur PromotionsRepository add : " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $code, $reduction, $titre = '', $date_debut = null, $date_fin = null) {
        try {
            $sql = "UPDATE promotions SET code = ?, reduction = ?, titre = ?, date_debut = ?, date_fin = ?, updatedat = NOW() WHERE id = ?";
            return $this->db->prepare($sql)->execute([$code, $reduction, $titre ?: $code, $date_debut, $date_fin, $id]);
        } catch (PDOException $e) {
            error_log("Erreur PromotionsRepository update : " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        try {
            return $this->db->prepare("DELETE FROM promotions WHERE id = ?")->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur PromotionsRepository delete : " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM promotions WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByCode($code) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM promotions WHERE code = ? AND est_actif = 1 LIMIT 1");
            $stmt->execute([$code]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}