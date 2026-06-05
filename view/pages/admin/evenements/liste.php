<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../../model/EvenementRepository.php';

try {
    $evenementRepository = new EvenementRepository();
    $currentUserId = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_utilisateurs'] ?? null;
    $currentRole = strtolower($_SESSION['role'] ?? 'user');
    if ($currentRole !== 'admin' && $currentUserId) {
        $listeEvents = $evenementRepository->getAllByUser((int)$currentUserId);
    } else {
        $listeEvents = $evenementRepository->getAll();
    }
} catch (Exception $e) {
    $listeEvents = [];
    error_log("Erreur de chargement des événements : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once '../../../sections/admin/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
</head>

<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Événements</a></li>
        <li class="breadcrumb-item active">Liste</li>
      </ol>

      <h1 class="page-header">Gestion des Événements <small>Planification</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Liste des Événements Programmés</h4>
        </div>
        
        <div class="panel-body">
          <div class="mb-3 text-right">
            <button class="btn btn-primary" onclick="openAddEventModal()">
                <i class="fa fa-plus mr-2"></i> Créer un événement
            </button>
          </div>

          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="5%">ID</th>
                <th>Détails de l'Événement</th>
                <th>Lieu / Emplacement</th>
                <th width="20%">Date & Heure</th>
                <th width="10%">Commandes</th>
                <th width="10%">Tickets vendus</th>
                <th width="12%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $commandeStats = [];
                $ticketsSoldStats = [];

                $evenementIds = array_map(fn($x) => (int)($x['id'] ?? 0), is_array($listeEvents) ? $listeEvents : []);
                $evenementIds = array_values(array_filter($evenementIds));

                if (!empty($evenementIds)) {
                    require_once '../../../../model/CommandesRepository.php';
                    $cmdRepo = new CommandesRepository();
                    $commandeStats = $cmdRepo->getCommandCountByEvenementIds($evenementIds);
                    $ticketsSoldStats = $cmdRepo->getTicketsSoldByEvenementIds($evenementIds);
                }
              ?>
              <?php if (!empty($listeEvents)): ?>
                <?php foreach ($listeEvents as $ev): ?>
                  <tr>
                    <td>EV-<?= $ev['id']; ?></td>
                    <td>
                        <strong class="text-inverse" style="font-size: 14px;"><?= htmlspecialchars($ev['titre']); ?></strong><br/>
                        <small class="text-muted"><?= htmlspecialchars(substr($ev['description'] ?? '', 0, 100)); ?>...</small>
                    </td>
                    <td><i class="fa fa-map-marker-alt text-danger mr-1"></i> <b><?= htmlspecialchars($ev['lieu']); ?></b></td>
                    <td class="f-w-600 text-primary">
                        <i class="fa fa-calendar-alt mr-1"></i> <?= date('d/m/Y à H:i', strtotime($ev['date'])); ?>
                    </td>
                    <td class="text-center">
                        <?= isset($commandeStats[(int)$ev['id']]) ? (int)$commandeStats[(int)$ev['id']] : 0; ?>
                    </td>
                    <td class="text-center">
                        <?= isset($ticketsSoldStats[(int)$ev['id']]) ? (int)$ticketsSoldStats[(int)$ev['id']] : 0; ?>
                    </td>
                    <td>
                      <button class="btn btn-xs btn-primary" 
                              onclick="openEditEventModal('<?= $ev['id']; ?>', '<?= addslashes($ev['titre']); ?>', '<?= addslashes($ev['lieu']); ?>', '<?= date('Y-m-d\TH:i', strtotime($ev['date'])); ?>', '<?= addslashes($ev['description'] ?? ''); ?>')">
                          <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-xs btn-danger" onclick="confirmDeleteEvent('<?= $ev['id']; ?>', '<?= addslashes($ev['titre']); ?>')">
                          <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">Aucun événement enregistré.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalEvent" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title" id="modalEventTitle">Nouvel Événement</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                
                <form action="/saticket/controller/EvenementController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="eventId" name="id">
                        
                        <div class="form-group">
                            <label class="f-w-600">Titre de l'événement</label>
                            <input type="text" class="form-control" id="eventTitre" name="titre" required>
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Lieu / Salle</label>
                            <input type="text" class="form-control" id="eventLieu" name="lieu" required>
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Date et Heure</label>
                            <input type="datetime-local" class="form-control" id="eventDate" name="date" required>
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Description</label>
                            <textarea class="form-control" id="eventDesc" name="description" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitEvent" name="btnSaveEvent">Enregistrer</button>
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
    function openAddEventModal() {
        $('#modalEventTitle').text('Créer un Nouvel Événement');
        $('#eventId').val('');
        $('#eventTitre').val('');
        $('#eventLieu').val('');
        $('#eventDate').val('');
        $('#eventDesc').val('');
        $('#btnSubmitEvent').attr('name', 'btnSaveEvent');
        $('#modalEvent').modal('show');
    }

    function openEditEventModal(id, titre, lieu, date, description) {
        $('#modalEventTitle').text('Modifier : ' + titre);
        $('#eventId').val(id);
        $('#eventTitre').val(titre);
        $('#eventLieu').val(lieu);
        $('#eventDate').val(date);
        $('#eventDesc').val(description);
        $('#btnSubmitEvent').attr('name', 'btnUpdateEvent');
        $('#modalEvent').modal('show');
    }

    function confirmDeleteEvent(id, name) {
        Swal.fire({
            title: 'Supprimer ' + name + ' ?',
            text: "Cette action effacera définitivement l'événement.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5b57',
            cancelButtonColor: '#f2f3f4',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/saticket/controller/EvenementController.php?action=delete&id=" + id;
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
  <?php endif; ?>
</body>
</html>