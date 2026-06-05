<?php 
// CRUD FILE - Repository utilisateurs : create, read, update, delete
require_once("DBRepository.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class UtilisateurRepository extends DBRepository
{
    public function __construct() {
        parent::__construct();
    }

    public function getAll(int $etat) {
        $sql = "SELECT * FROM utilisateurs WHERE etat = :etat";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['etat' => $etat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getAll : " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllEtatAndRole(int $etat, string $role) {
        $sql = "SELECT * FROM utilisateurs WHERE etat = :etat AND role = :role";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['etat' => $etat, 'role' => $role]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getAllEtatAndRole : " . $e->getMessage());
            throw $e;
        }
    }

    public function getUtilisateur(int $id) {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getUtilisateur : " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserByEmail(string $email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getUserByEmail : " . $e->getMessage());
            throw $e;
        }
    }

    public function add(string $nom, $email, $role, $password, $telephone, $etat = 1) {
        $sql = "INSERT INTO utilisateurs (nom, email, role, createdat, telephone, password, etat)
                VALUES (:nom, :email, :role, NOW(), :telephone, :password, :etat)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'nom'       => $nom,
                'email'     => $email,
                'role'      => $role,
                'telephone' => $telephone,
                'password'  => password_hash($password, PASSWORD_BCRYPT),
                'etat'      => $etat,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur add utilisateur : " . $e->getMessage());
            throw $e;
        }
    }

   
    public function updateUser(int $id, string $nom, string $email, string $role, int $etat, string $telephone = '') {
        try {
            // Colonnes obligatoires (toujours présentes)
            $sets   = ["nom = :nom", "email = :email", "role = :role", "etat = :etat"];
            $params = ['id' => $id, 'nom' => $nom, 'email' => $email, 'role' => $role, 'etat' => $etat];

            // Colonnes optionnelles on vérifie avant d'inclure
            $colonnesTable = $this->db->query("SHOW COLUMNS FROM utilisateurs")->fetchAll(PDO::FETCH_COLUMN);

            if (in_array('telephone', $colonnesTable)) {
                $sets[]              = "telephone = :telephone";
                $params['telephone'] = $telephone;
            }
            if (in_array('updatedat', $colonnesTable)) {
                $sets[] = "updatedat = NOW()";
            }
            if (in_array('updated_by', $colonnesTable)) {
                $sets[]              = "updated_by = :updated_by";
                $params['updated_by'] = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
            }

            $sql  = "UPDATE utilisateurs SET " . implode(', ', $sets) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return true; 
        } catch (PDOException $e) {
            error_log("Erreur updateUser id={$id} : " . $e->getMessage());
            return false;
        }
    }

    // Alias simple utilisé dans certains endroits
    public function update($id, $nom, $email, $role, $telephone) {
        return $this->updateUser((int)$id, $nom, $email, $role, 1, $telephone);
    }

    public function activer(int $id) {
        return $this->db->prepare("UPDATE utilisateurs SET etat = 1 WHERE id = :id")->execute(['id' => $id]);
    }

    public function desactiver(int $id) {
        return $this->db->prepare("UPDATE utilisateurs SET etat = 0 WHERE id = :id")->execute(['id' => $id]);
    }

    public function delete(int $id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur delete utilisateur id={$id} : " . $e->getMessage());
            throw $e;
        }
    }

    public function register($nom, $email, $password, $role, $telephone) {
        $sql  = "INSERT INTO utilisateurs (nom, email, password, role, telephone) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $email, $password, $role, $telephone]);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email AND etat = 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && (password_verify($password, $user['password']) || $user['password'] === $password)) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur login : " . $e->getMessage());
            throw $e;
        }
    }
}