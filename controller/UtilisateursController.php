<?php
// CRUD FILE - Gestion des utilisateurs : création, modification, suppression, consultation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../model/UtilisateurRepository.php";

class UtilisateursController {

    public function validateLoginfields($email, $password) {
        if (empty($email) || empty($password)) return "Tous les champs sont obligatoires.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "L'email fourni est invalide.";
        return null;
    }

    public function setErrorAndRedirect($message, $title = "Erreur", $redirecturl = "/saticket/login.php") {
        $_SESSION["error"] = $message;
        header("Location: " . $redirecturl . "?error=1&message=" . urlencode($message) . "&title=" . urlencode($title));
        exit();
    }

    public function authADMIN($email, $password, $utilisateurrepository) {
        $user = $utilisateurrepository->login($email, $password);
        if ($user) {
            $roleNettoye = trim(strtolower($user['role']));
            $_SESSION["id"]      = $user['id'];
            $_SESSION["user_id"] = $user['id'];
            $_SESSION["email"]   = $user['email'];
            $_SESSION["nom"]     = $user['nom'];
            $_SESSION["role"]    = $roleNettoye;

            if (isset($_POST['remember'])) {
                setcookie('remember_me', $user['id'], time() + (86400 * 30), '/', '', true, true);
            }

            switch ($roleNettoye) {
                case 'admin':
                    header("Location: /saticket/admin.php?successConnect=1"); break;
                case 'organisateur':
                    header("Location: /saticket/view/pages/organisateur/mes_evenements.php?successConnect=1"); break;
                case 'acheteur':
                    header("Location: /saticket/view/pages/acheteur/parcourir_evenements.php?successConnect=1"); break;
                default:
                    die("Erreur : rôle '" . $roleNettoye . "' non configuré.");
            }
            exit();
        }
        return false;
    }
}

// ── Instanciation ──
$utilisateursController = new UtilisateursController();
$utilisateurRepository  = new UtilisateurRepository();
$currentAdmin = $_SESSION['id'] ?? $_SESSION['user_id'] ?? 0;
$redirectListe = "/saticket/view/pages/admin/utilisateurs/liste.php";

// ── CONNEXION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['frmLogin'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $err = $utilisateursController->validateLoginfields($email, $password);
    if ($err) $utilisateursController->setErrorAndRedirect($err, "Oups !");
    try {
        if (!$utilisateursController->authADMIN($email, $password, $utilisateurRepository)) {
            $utilisateursController->setErrorAndRedirect("Identifiants incorrects.", "Échec");
        }
    } catch (Exception $e) {
        $utilisateursController->setErrorAndRedirect("Erreur serveur : " . $e->getMessage(), "Erreur");
    }
}

// ── INSCRIPTION ──
if (isset($_POST['btnRegister'])) {
    $nom       = trim($_POST['nom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? 'acheteur';
    try {
        if (!$utilisateurRepository->getUserByEmail($email)) {
            $utilisateurRepository->register($nom, $email, $password, $role, $telephone);
            $user = $utilisateurRepository->getUserByEmail($email);
            $_SESSION['id']   = $user['id'];
            $_SESSION['nom']  = $nom;
            $_SESSION['role'] = $role;
            header("Location: " . ($role === 'organisateur'
                ? "/saticket/view/pages/organisateur/mes_evenements.php"
                : "/saticket/view/pages/acheteur/parcourir_evenements.php"));
            exit();
        } else {
            die("Erreur : Cet email est déjà utilisé.");
        }
    } catch (Exception $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
}

// ── AJOUTER un utilisateur ──
if (isset($_POST['btnAddUser'])) {
    try {
        $password = $_POST['password'] ?? 'saticket2024';
        $utilisateurRepository->add(
            $_POST['nom'], $_POST['email'], $_POST['role'],
            $password, $_POST['telephone'] ?? '', (int)($_POST['etat'] ?? 1)
        );
        header("Location: {$redirectListe}?messageSuccess=" . urlencode("Utilisateur ajouté avec succès !"));
    } catch (Exception $e) {
        header("Location: {$redirectListe}?messageError=" . urlencode("Erreur lors de l'ajout."));
    }
    exit();
}

// ── MODIFIER un utilisateur ──  ← C'était manquant, voici le fix
if (isset($_POST['btnUpdateUser'])) {
    $id        = (int)($_POST['id'] ?? 0);
    $nom       = trim($_POST['nom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $role      = trim($_POST['role'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $etat      = (int)($_POST['etat'] ?? 1);

    if ($id <= 0 || empty($nom) || empty($email)) {
        header("Location: {$redirectListe}?messageError=" . urlencode("Données invalides."));
        exit();
    }

    try {
        $result = $utilisateurRepository->updateUser($id, $nom, $email, $role, $etat, $telephone);
        if ($result) {
            header("Location: {$redirectListe}?messageSuccess=" . urlencode("Utilisateur modifié avec succès !"));
        } else {
            header("Location: {$redirectListe}?messageError=" . urlencode("Aucune modification détectée."));
        }
    } catch (Exception $e) {
        header("Location: {$redirectListe}?messageError=" . urlencode("Erreur lors de la modification."));
    }
    exit();
}

// ── ACTIVER ──
if (isset($_GET['activate'])) {
    $utilisateurRepository->activer((int)$_GET['activate']);
    header("Location: {$redirectListe}?messageSuccess=" . urlencode("Utilisateur activé."));
    exit();
}

// ── DÉSACTIVER ──
if (isset($_GET['desactivate'])) {
    $utilisateurRepository->desactiver((int)$_GET['desactivate']);
    header("Location: {$redirectListe}?messageSuccess=" . urlencode("Utilisateur désactivé."));
    exit();
}

// ── SUPPRIMER ──
if (isset($_GET['delete'])) {
    $utilisateurRepository->delete((int)$_GET['delete']);
    header("Location: {$redirectListe}?messageSuccess=" . urlencode("Utilisateur supprimé !"));
    exit();
}

// ── DÉCONNEXION ──
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /saticket/login.php");
    exit();
}

// ── ACTION générique activate/desactivate ──
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'activer') {
        $utilisateurRepository->activer($id);
    } else {
        $utilisateurRepository->desactiver($id);
    }
    header("Location: {$redirectListe}?messageSuccess=" . urlencode("Statut mis à jour."));
    exit();
}