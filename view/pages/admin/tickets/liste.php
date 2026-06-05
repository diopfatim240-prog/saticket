<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../../model/TicketsRepository.php';
require_once '../../../../model/EvenementRepository.php';

try {
    $ticketsRepo = new TicketsRepository();
    $eventsRepo = new EvenementRepository();
    
    $listeTickets = $ticketsRepo->getAll();
    $listeEvents = $eventsRepo->getAll(); 
} catch (Exception $e) {
    $listeTickets = [];
    $listeEvents = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once '../../../sections/admin/head.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">

<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Billetterie</a></li>
        <li class="breadcrumb-item active">Liste des Tickets</li>
      </ol>

      <h1 class="page-header">Gestion des Tickets <small>Suivi des stocks et tarifs</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Lots de Billets Disponibles</h4>
        </div>
        
        <div class="panel-body">
          <div class="mb-3 text-right">
            <button class="btn btn-primary" onclick="openAddTicketModal()">
                <i class="fa fa-plus mr-2"></i> Émettre un lot de tickets
            </button>
          </div>

          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="8%">ID Lot</th>
                <th>Événement Associé</th>
                <th>Catégorie / Type</th>
                <th>Prix Unitaire</th>
                <th>Stock Quantité</th>
                <th width="12%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($listeTickets)): ?>
                <?php foreach ($listeTickets as $tk): ?>
                  <tr>
                    <td class="f-w-600 text-inverse">LOT-0<?= $tk['id']; ?></td>
                    <td><strong class="text-primary"><?= htmlspecialchars($tk['nom_evenement'] ?? 'Événement inconnu'); ?></strong></td>
                    <td>
                        <?php if(in_array(strtoupper($tk['type']), ['VIP', 'VVIP'])): ?>
                            <span class="label label-purple"><?= htmlspecialchars($tk['type']); ?></span>
                        <?php else: ?>
                            <span class="label label-default"><?= htmlspecialchars($tk['type']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="f-w-600 text-inverse"><?= number_format($tk['prix'], 0, ',', ' '); ?> FCFA</td>
                    <td>
                        <span class="badge badge-<?= ($tk['total'] > 10) ? 'success' : 'danger'; ?>">
                            <?= $tk['total']; ?> ex.
                        </span>
                    </td>
                    <td>
                      <button class="btn btn-xs btn-primary" 
                              onclick="openEditTicketModal('<?= $tk['id']; ?>', '<?= $tk['prix']; ?>', '<?= $tk['total']; ?>', '<?= htmlspecialchars($tk['type']); ?>')">
                          <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-xs btn-danger" onclick="confirmDeleteTicket('<?= $tk['id']; ?>')">
                          <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">Aucun lot de tickets configuré actuellement.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalTicket" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title" id="modalTicketTitle">Détails du Lot de Tickets</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="/saticket/controller/TicketsController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="ticketId" name="id">
                        
                        <div class="form-group" id="divSelectEvent">
                            <label class="f-w-600">Pour l'événement :</label>
                            <select class="form-control" name="event_id" id="ticketEvent">
                                <?php foreach($listeEvents as $ev): ?>
                                    <option value="<?= $ev['id']; ?>"><?= htmlspecialchars($ev['titre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Catégorie du Billet</label>
                            <select class="form-control" name="type" id="ticketType">
                                <option value="Standard">Standard</option>
                                <option value="VIP">VIP</option>
                                <option value="VVIP">VVIP / Premium</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Prix (FCFA)</label>
                            <input type="number" class="form-control" name="prix" id="ticketPrix" required min="0">
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Quantité globale mise en vente</label>
                            <input type="number" class="form-control" name="quantite" id="ticketQte" required min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitTicket" name="btnAddTicket">Émettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../../../sections/admin/sectionConfig.php'; ?>
  </div>

  <?php require_once '../../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function openAddTicketModal() {
        $('#modalTicketTitle').text('Émettre un nouveau lot de billets');
        $('#ticketId').val('');
        $('#ticketPrix').val('');
        $('#ticketQte').val('');
        $('#divSelectEvent').show();
        $('#btnSubmitTicket').attr('name', 'btnAddTicket').text('Émettre');
        $('#modalTicket').modal('show');
    }

    function openEditTicketModal(id, prix, quantite, type) {
        $('#modalTicketTitle').text('Modifier Tarifs & Stock - Lot #' + id);
        $('#ticketId').val(id);
        $('#ticketPrix').val(prix);
        $('#ticketQte').val(quantite);
        $('#ticketType').val(type);
        $('#divSelectEvent').hide(); 
        $('#btnSubmitTicket').attr('name', 'btnUpdateTicket').text('Mettre à jour');
        $('#modalTicket').modal('show');
    }

    function confirmDeleteTicket(id) {
        Swal.fire({
            title: 'Supprimer ce lot ?',
            text: "Tous les billets invendus de cette catégorie seront supprimés.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5b57',
            cancelButtonColor: '#f2f3f4',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/saticket/controller/TicketsController.php?action=delete&id=" + id;
            }
        });
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
        Swal.fire({
            title: "Succès !",
            text: "<?= htmlspecialchars($_GET['messageSuccess']); ?>",
            icon: "success",
            confirmButtonColor: "#00acac"
        });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
        Swal.fire({
            title: "Erreur SQL",
            text: "<?= htmlspecialchars($_GET['messageError']); ?>",
            icon: "error",
            confirmButtonColor: "#ff5b57"
        });
    </script>
  <?php endif; ?>
</body>
</html>