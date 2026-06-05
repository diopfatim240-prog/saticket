<?php 
// CRUD FILE - Repository commentaires : create, read, update, delete
require_once("DBRepository.php");

class CommentaireRepository extends DBRepository
{
    public function __construct() { 
        parent::__construct(); 
    }

    public function getAllComments() {
        try {
            $sql = "SELECT c.id, c.avis, c.note, c.createdat,
                           u.nom AS user_nom, '' AS user_prenom, u.email AS user_email,
                           e.titre AS evenement_titre
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateurs = u.id
                    LEFT JOIN evenements e   ON c.id_evenements = e.id
                    ORDER BY c.id DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllComments : " . $e->getMessage());
            return [];
        }
    }

    public function getAllCommentsByUser(int $userId) {
        try {
            $sql  = "SELECT c.id, c.avis, c.note, c.createdat,
                            u.nom AS user_nom, '' AS user_prenom, u.email AS user_email,
                            e.titre AS evenement_titre
                     FROM commentaires c
                     LEFT JOIN utilisateurs u ON c.id_utilisateurs = u.id
                     LEFT JOIN evenements e   ON c.id_evenements   = e.id
                     WHERE c.id_utilisateurs = ?
                     ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllCommentsByUser : " . $e->getMessage());
            return [];
        }
    }

    public function add($note, $avis, $id_user, $id_evenement = null) {
        try {
            $sql  = "INSERT INTO commentaires (note, avis, date, createdat, updatedat, id_utilisateurs, id_evenements) 
                     VALUES (?, ?, NOW(), NOW(), NOW(), ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                !empty($note) ? $note : '5',
                $avis ?? '',
                $id_user       ? (int)$id_user       : null,
                $id_evenement  ? (int)$id_evenement   : null
            ]);
        } catch (PDOException $e) {
            error_log("Erreur add commentaire : " . $e->getMessage());
            throw $e;
        }
    }

    public function update(int $id, $note, $avis) {
        try {
            $sql  = "UPDATE commentaires SET note = ?, avis = ?, updatedat = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                !empty($note) ? $note : '5',
                trim((string)($avis ?? '')),
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur update commentaire : " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        try {
            return $this->db->prepare("DELETE FROM commentaires WHERE id = ?")
                            ->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur delete commentaire : " . $e->getMessage());
            throw $e;
        }
    }

    public function getById(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM commentaires WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getById commentaire : " . $e->getMessage());
            return null;
        }
    }
}