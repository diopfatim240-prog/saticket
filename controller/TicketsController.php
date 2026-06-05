<?php
// CRUD FILE - Gestion des tickets : création, modification, suppression, consultation
session_start();
require_once __DIR__ . '/../model/TicketsRepository.php';
require_once __DIR__ . '/../model/CommandesRepository.php';

class TicketsController {
    private $repository;
    private $commandesRepository;

    public function __construct() {
        $this->repository          = new TicketsRepository();
        $this->commandesRepository = new CommandesRepository();
    }

    private function getRedirectBase(): string {
        $redirect = $_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '';
        return $redirect === 'organisateur'
            ? '/saticket/view/pages/organisateur/mes_evenements.php'
            : '/saticket/view/pages/admin/tickets/liste.php';
    }

    public function save() {
        if (isset($_POST['btnAddTicket'])) {
            $redirectBase = $this->getRedirectBase();
            try {
                $this->repository->add($_POST['type'], $_POST['prix'], $_POST['quantite'], $_POST['event_id']);
                header("Location: {$redirectBase}?messageSuccess=Lot de tickets créé avec succès !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Erreur lors de l'insertion en BDD.");
            }
            exit();
        }
    }

    public function update() {
        if (isset($_POST['btnUpdateTicket'])) {
            $redirectBase = $this->getRedirectBase();
            try {
                $this->repository->update((int)$_POST['id'], $_POST['prix'], $_POST['quantite']);
                header("Location: {$redirectBase}?messageSuccess=Ticket mis à jour !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Erreur lors de la modification.");
            }
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete') {
            $redirectBase = isset($_GET['redirect_to']) && $_GET['redirect_to'] === 'organisateur'
                ? '/saticket/view/pages/organisateur/mes_evenements.php'
                : '/saticket/view/pages/admin/tickets/liste.php';
            try {
                $this->repository->delete((int)$_GET['id']);
                header("Location: {$redirectBase}?messageSuccess=Ticket supprimé !");
            } catch (Exception $e) {
                header("Location: {$redirectBase}?messageError=Impossible de supprimer ce lot.");
            }
            exit();
        }
    }

    //stats tickets d'un événement (organisateur tableau de suivi)
    public function getByEventAjax() {
        $event_id = (int)($_GET['event_id'] ?? 0);
        header('Content-Type: application/json');
        if ($event_id <= 0) { echo json_encode([]); exit(); }
        echo json_encode($this->repository->getTicketsWithStatsByEvent($event_id));
        exit();
    }

    //résumé total/vendus pour la ligne du tableau
    public function getEventSummaryAjax() {
        $event_id = (int)($_GET['event_id'] ?? 0);
        header('Content-Type: application/json');
        if ($event_id <= 0) { echo json_encode(['total' => 0, 'vendus' => 0]); exit(); }
        echo json_encode($this->repository->getEventTicketSummary($event_id));
        exit();
    }

    //commandes d'un événement avec statut paiement (pour émettre billets)
    public function getCommandesEvenementAjax() {
        $event_id = (int)($_GET['event_id'] ?? 0);
        header('Content-Type: application/json');
        if ($event_id <= 0) { echo json_encode([]); exit(); }
        echo json_encode($this->commandesRepository->getOrdersByEvenement($event_id));
        exit();
    }

    //ventes globales par événement (acheteur)
    public function getVentesAcheteurAjax() {
        header('Content-Type: application/json');
        echo json_encode($this->repository->getVentesParEvenement());
        exit();
    }
}


$controller = new TicketsController();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getByEvent':          $controller->getByEventAjax();             break;
        case 'getEventSummary':     $controller->getEventSummaryAjax();        break;
        case 'getCommandesEvenement': $controller->getCommandesEvenementAjax(); break;
        case 'getVentesAcheteur':   $controller->getVentesAcheteurAjax();      break;
        case 'delete':              $controller->delete();                      break;
    }
}
if (isset($_POST['btnAddTicket']))    { $controller->save(); }
if (isset($_POST['btnUpdateTicket'])) { $controller->update(); }