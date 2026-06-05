<?php
// CRUD FILE - Gestion des commandes : création, lecture, suppression, paiement
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../model/CommandesRepository.php';
require_once __DIR__ . '/../model/TicketsRepository.php';
require_once __DIR__ . '/../model/PaiementRepository.php';

class CommandesController {
    private $repository, $ticketsRepository, $paiementRepository;

    public function __construct() {
        $this->repository         = new CommandesRepository();
        $this->ticketsRepository  = new TicketsRepository();
        $this->paiementRepository = new PaiementRepository();
    }

    public function passerCommande() {
        if (!isset($_POST['btnCommander'])) return;

        $reference    = 'CMD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $quantite     = (int)($_POST['quantite'] ?? 0);
        // Récupération des IDs selon tes nouveaux noms
        $id_user      = $_SESSION['id_utilisateurs'] ?? $_SESSION['id'] ?? null;
        $id_evenement = (int)($_POST['id_evenements'] ?? 0); // Vérifie si ton formulaire envoie id_evenements ou id_evenement
        $id_ticket    = (int)($_POST['id_tickets'] ?? 0);
        
        $redirectErr  = '/saticket/view/pages/acheteur/parcourir_evenements.php';

        if (!$id_user || $id_evenement <= 0 || $id_ticket <= 0 || $quantite <= 0) {
            header("Location: {$redirectErr}?messageError=Données de commande invalides.");
            exit();
        }

        // Appel à la méthode add corrigée
        $success = $this->repository->add($reference, $quantite, 0, $id_user, $id_evenement, $id_ticket);

        if ($success) {
            $this->ticketsRepository->decrementQuantity($id_ticket, $quantite);
            header("Location: /saticket/view/pages/acheteur/mes_achats.php?messageSuccess=Commande réussie !");
        } else {
            // Si ça échoue, cette page te permettra de savoir que le problème est en base de données
            header("Location: {$redirectErr}?messageError=Erreur lors de l'enregistrement de la commande.");
        }
        exit();
    }
}

$controller = new CommandesController();
if (isset($_POST['btnCommander'])) { $controller->passerCommande(); }
?>