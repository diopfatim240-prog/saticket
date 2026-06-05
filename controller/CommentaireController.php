<?php
// CRUD FILE - Gestion des commentaires : création, modification, suppression, consultation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_connecte = $_SESSION['id'] ?? $_SESSION['id_utilisateurs'] ?? $_SESSION['user_id'] ?? 0;
$role        = strtolower(trim($_SESSION['role'] ?? ''));
$allowedRoles = ['organisateur', 'acheteur', 'admin'];

if ($id_connecte === 0 || !in_array($role, $allowedRoles, true)) {
    header("Location: /saticket/login.php");
    exit();
}

require_once __DIR__ . '/../model/CommentaireRepository.php';
require_once __DIR__ . '/../model/EvenementRepository.php';

$repo          = new CommentaireRepository();
$evenementRepo = new EvenementRepository();

if ($role === 'organisateur') {
    $redirectBase = '/saticket/view/pages/organisateur/mes_commentaires.php';
} elseif ($role === 'acheteur') {
    $redirectBase = '/saticket/view/pages/acheteur/mes_commentaires.php';
} else {
    $redirectBase = '/saticket/view/pages/admin/commentaire/liste.php';
}

// ── AJOUTER ──
if (isset($_POST['btnAddComment'])) {
    $note = trim($_POST['note'] ?? '5');
    $avis = trim($_POST['avis'] ?? '');

    if (empty($avis)) {
        header("Location: {$redirectBase}?messageError=" . urlencode("L'avis est obligatoire."));
        exit();
    }

    $id_evenement = (int)($_POST['id_evenements'] ?? 0);
    if ($id_evenement === 0) {
        $tous = $evenementRepo->getAll();
        $id_evenement = !empty($tous) ? (int)$tous[0]['id'] : 1;
    }

    try {
        $repo->add($note, $avis, $id_connecte, $id_evenement);
        header("Location: {$redirectBase}?messageSuccess=" . urlencode("Commentaire publié !"));
        exit();
    } catch (Exception $e) {
        header("Location: {$redirectBase}?messageError=" . urlencode("Erreur BDD : " . $e->getMessage()));
        exit();
    }
}

// ── MODIFIER (AJAX) ──
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id   = (int)($_POST['id']   ?? 0);
    $note = trim($_POST['note']  ?? '5');
    $avis = trim($_POST['avis']  ?? '');

    $com = $repo->getById($id);
    if (!$com || (int)$com['id_utilisateurs'] !== (int)$id_connecte) {
        echo 'forbidden'; exit();
    }
    try {
        $repo->update($id, $note, $avis);
        echo 'success';
    } catch (Exception $e) {
        echo 'error';
    }
    exit();
}

// ── SUPPRIMER (AJAX) ──
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id  = (int)($_GET['id'] ?? 0);
    $com = $repo->getById($id);

    if (!$com || ((int)$com['id_utilisateurs'] !== (int)$id_connecte && $role !== 'admin')) {
        echo 'forbidden'; exit();
    }
    try {
        $repo->delete($id);
        echo 'success';
    } catch (Exception $e) {
        echo 'error';
    }
    exit();
}