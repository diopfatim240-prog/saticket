<?php
// CRUD FILE - Gestion des événements : création, modification, suppression, consultation
session_start();
require_once __DIR__ . '/../model/EvenementRepository.php';

class EvenementController {
    private $repository;

    public function __construct() {
        $this->repository = new EvenementRepository();
    }

    private function getRedirectBase(): string {
        $redirect = $_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '';
        if ($redirect === 'organisateur') {
            return '/saticket/view/pages/organisateur/mes_evenements.php';
        }
        return '/saticket/view/pages/admin/evenements/liste.php';
    }

    public function save() {
        if (isset($_POST['btnSaveEvent'])) {
            $titre       = $_POST['titre'];
            $date        = $_POST['date'];
            $lieu        = $_POST['lieu'];
            $description = $_POST['description'] ?? '';
            // Récupérer l'ID depuis la session
            $currentUserId = $_SESSION['id_utilisateurs'] ?? $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
            $redirectBase  = $this->getRedirectBase();

            try {
                $this->repository->add($titre, $lieu, $date, $description, $currentUserId);
                header("Location: {$redirectBase}?messageSuccess=Événement créé avec succès !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Erreur lors de la création.");
            }
            exit();
        }
    }

    public function update() {
        if (isset($_POST['btnUpdateEvent'])) {
            $id          = (int)$_POST['id'];
            $titre       = $_POST['titre'];
            $date        = $_POST['date'];
            $lieu        = $_POST['lieu'];
            $description = $_POST['description'] ?? '';
            $currentUserId = $_SESSION['id_utilisateurs'] ?? $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
            $currentRole   = strtolower($_SESSION['role'] ?? 'user');
            $redirectBase  = $this->getRedirectBase();

            $event = $this->repository->getById($id);
            if (!$event) {
                header("Location: {$redirectBase}?messageError=Événement introuvable");
                exit();
            }

            // Vérifier propriété — colonne id_organisateur
            $ownerId = $event['id_organisateur'] ?? null;
            if ($currentRole !== 'admin' && (int)$ownerId !== (int)$currentUserId) {
                header("Location: {$redirectBase}?messageError=Accès refusé");
                exit();
            }

            try {
                $this->repository->update($id, $titre, $lieu, $date, $description);
                header("Location: {$redirectBase}?messageSuccess=Événement modifié avec succès !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Erreur lors de la modification.");
            }
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete') {
            $id            = (int)$_GET['id'];
            $currentUserId = $_SESSION['id_utilisateurs'] ?? $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
            $currentRole   = strtolower($_SESSION['role'] ?? 'user');
            $redirectBase  = isset($_GET['redirect_to']) && $_GET['redirect_to'] === 'organisateur'
                            ? '/saticket/view/pages/organisateur/mes_evenements.php'
                            : '/saticket/view/pages/admin/evenements/liste.php';

            $event = $this->repository->getById($id);
            if (!$event) {
                header("Location: {$redirectBase}?messageError=Événement introuvable");
                exit();
            }

            $ownerId = $event['id_organisateur'] ?? null;
            if ($currentRole !== 'admin' && (int)$ownerId !== (int)$currentUserId) {
                header("Location: {$redirectBase}?messageError=Accès refusé");
                exit();
            }

            try {
                $this->repository->delete($id);
                header("Location: {$redirectBase}?messageSuccess=Événement supprimé !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Impossible de supprimer cet événement.");
            }
            exit();
        }
    }
}

// ROUTER
$controller = new EvenementController();
if (isset($_POST['btnSaveEvent']))   { $controller->save(); }
if (isset($_POST['btnUpdateEvent'])) { $controller->update(); }
if (isset($_GET['action']) && $_GET['action'] === 'delete') { $controller->delete(); }