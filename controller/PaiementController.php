<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/PaiementRepository.php';

class PaiementController {
    private $repository;

    public function __construct() {
        $this->repository = new PaiementRepository();
    }

    // Gestion des retours de passerelles de paiement
    public function handleCallback() {
        $id_commande = isset($_GET['transaction_id']) ? (int)$_GET['transaction_id'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 'Wave';

        if ($id_commande <= 0) {
            // Redirection relative vers la liste en cas d'erreur
            header("Location: ../view/pages/admin/paiement/liste.php?messageError=Commande invalide");
            exit();
        }

        // Si le paiement est réussi
        if (isset($_GET['payment_success'])) {
            $this->repository->saveTransaction($id_commande, $type, "Réussi");
            header("Location: ../view/pages/admin/paiement/liste.php?messageSuccess=Votre paiement a été validé !");
            exit();
        }

        // Si le paiement a échoué
        if (isset($_GET['payment_failed'])) {
            $this->repository->saveTransaction($id_commande, $type, "Échoué");
            header("Location: ../view/pages/admin/paiement/liste.php?messageError=La transaction a échoué.");
            exit();
        }
    }

    // AJOUT MANUEL d'un paiement depuis la page admin
    public function ajouterManuel() {
        if (!isset($_POST['btnSavePayment'])) {
            return;
        }

        $id_commande = isset($_POST['id_commande']) ? (int)$_POST['id_commande'] : 0;
        $type = trim((string)($_POST['type'] ?? 'Wave'));
        $statut = trim((string)($_POST['statut'] ?? 'Réussi'));

        if ($id_commande <= 0) {
            header("Location: ../view/pages/admin/paiement/liste.php?messageError=Commande invalide");
            exit();
        }

        $success = $this->repository->saveTransaction($id_commande, $type, $statut);

        if ($success) {
            header("Location: ../view/pages/admin/paiement/liste.php?messageSuccess=Paiement enregistré !");
        } else {
            header("Location: ../view/pages/admin/paiement/liste.php?messageError=Impossible d'enregistrer le paiement");
        }
        exit();
    }
}

// Exécution automatique
$controller = new PaiementController();
$controller->ajouterManuel();
$controller->handleCallback();
?>
