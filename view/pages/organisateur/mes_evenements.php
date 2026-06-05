<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_connecte = 0;
if (isset($_SESSION['id_utilisateurs'])) {
    $id_connecte = (int)$_SESSION['id_utilisateurs'];
} elseif (isset($_SESSION['id'])) {
    $id_connecte = (int)$_SESSION['id'];
}

if ($id_connecte === 0) {
    header("Location: /saticket/login.php");
    exit();
}

$role_actuel = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
if ($role_actuel !== 'organisateur') {
    header("Location: /saticket/admin.php?error=acces_refuse");
    exit();
}

require_once __DIR__ . '/../../../model/EvenementRepository.php';
require_once __DIR__ . '/../../../model/TicketsRepository.php';

$evenementRepo = new EvenementRepository();
$ticketsRepo   = new TicketsRepository();

try {
    $listeEvenements = $evenementRepo->getEvenementsByOrganisateur($id_connecte);
} catch (Exception $e) {
    $listeEvenements = [];
    error_log("Erreur chargement mes événements: " . $e->getMessage());
}

$ticketsParEvenement = [];
foreach ($listeEvenements as $ev) {
    $ticketsParEvenement[$ev['id']] = $evenementRepo->getTicketStatsForEvent((int)$ev['id']);
}

$nomOrganisateur = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? ''));
if (trim($nomOrganisateur) === '') $nomOrganisateur = 'Organisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/../../sections/admin/head.php'; ?>
<style>
/* ── Ticket électronique ── */
.ticket-wrap {
    background: linear-gradient(135deg,#1d3557 0%,#457b9d 100%);
    border-radius: 20px; padding: 26px 28px; color: #fff;
    position: relative; overflow: hidden; max-width: 440px; margin: 0 auto;
    box-shadow: 0 8px 32px rgba(29,53,87,.4);
}
.ticket-wrap::before, .ticket-wrap::after {
    content:''; position:absolute; width:42px; height:42px;
    background:#f0f0f0; border-radius:50%; top:50%; transform:translateY(-50%);
}
.ticket-wrap::before { left:-21px; }
.ticket-wrap::after  { right:-21px; }
.ticket-deco { position:absolute; right:-25px; bottom:-25px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,.07); }
.ticket-logo  { font-size:20px; font-weight:900; letter-spacing:2px; color:#a8dadc; }
.ticket-badge { background:rgba(0,176,155,.22); border:1px solid #00b09b; color:#7fffd4;
                border-radius:20px; padding:3px 12px; font-size:11px; font-weight:700;
                display:inline-block; margin:6px 0 8px; }
.ticket-event { font-size:17px; font-weight:800; line-height:1.2; margin-bottom:4px; }
.ticket-divider { border-top:2px dashed rgba(255,255,255,.28); margin:12px 0; }
.ticket-row { display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px; opacity:.88; }
.ticket-row strong { color:#fff; }
.ticket-amount { font-size:22px; font-weight:900; }
.ticket-ref-txt { font-size:10px; letter-spacing:2px; opacity:.5; margin-top:8px; }
.ticket-qr { background:#fff; border-radius:8px; padding:5px; display:inline-block; }

/* ── Commandes table dans modale ── */
.commandes-table th { font-size:12px; }
.commandes-table td { vertical-align:middle; font-size:13px; }
.btn-emettre { padding:3px 10px; font-size:11px; }
</style>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once __DIR__ . '/../../sections/admin/menuHaut.php'; ?>
    <?php require_once __DIR__ . '/../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item active">Mes Événements</li>
      </ol>
      <h1 class="page-header">Mes Événements <small>Gérez, suivez les ventes et émettez les billets</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Liste de mes événements</h4>
        </div>
        <div class="panel-body">
          <div class="mb-3 text-right">
            <button class="btn btn-primary" onclick="openAddEventModal()">
              <i class="fa fa-plus mr-2"></i> Créer un événement
            </button>
          </div>

          <?php if (empty($listeEvenements)): ?>
            <div class="alert alert-info text-center">
              <i class="fa fa-info-circle mr-2"></i>
              Vous n'avez pas encore créé d'événement. Cliquez sur <strong>Créer un événement</strong> pour commencer.
            </div>
          <?php else: ?>
          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="5%">ID</th>
                <th>Titre</th>
                <th>Lieu</th>
                <th width="15%">Date</th>
                <th width="18%" class="text-center">Tickets vendus / Total</th>
                <th width="14%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($listeEvenements as $ev):
                $stats    = $ticketsParEvenement[$ev['id']] ?? ['total' => 0, 'vendus' => 0];
                $total    = (int)$stats['total'];
                $vendus   = (int)$stats['vendus'];
                $pct      = $total > 0 ? round($vendus / $total * 100) : 0;
                $barClass = $pct >= 100 ? 'danger' : ($pct >= 75 ? 'warning' : 'success');
              ?>
              <tr>
                <td>EV-<?= (int)$ev['id']; ?></td>
                <td>
                  <strong class="text-inverse"><?= htmlspecialchars($ev['titre'] ?? ''); ?></strong>
                  <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars(substr($ev['description'] ?? '', 0, 55)); ?>...</div>
                </td>
                <td><?= htmlspecialchars($ev['lieu'] ?? ''); ?></td>
                <td><?= !empty($ev['date']) ? date('d/m/Y à H:i', strtotime($ev['date'])) : 'N/A'; ?></td>
                <td class="text-center" id="stats-ev-<?= (int)$ev['id']; ?>">
                  <span class="badge badge-<?= $barClass; ?> f-s-12 mb-1"><?= $vendus; ?> / <?= $total > 0 ? $total : '—'; ?></span>
                  <?php if ($total > 0): ?>
                    <div class="progress" style="height:7px;">
                      <div class="progress-bar bg-<?= $barClass; ?>" style="width:<?= $pct; ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $pct; ?>% vendus</small>
                  <?php else: ?>
                    <small class="text-muted">Aucun ticket défini</small>
                  <?php endif; ?>
                </td>
                <td>
                  <!-- Tickets -->
                  <button class="btn btn-xs btn-success mb-1" title="Gérer les tickets"
                          onclick="openTicketModal(<?= (int)$ev['id']; ?>, '<?= addslashes($ev['titre'] ?? ''); ?>')">
                    <i class="fa fa-ticket"></i>
                  </button>
                  <!-- Commandes / Émettre billets -->
                  <button class="btn btn-xs btn-warning mb-1" title="Voir commandes et émettre billets"
                          onclick="openCommandesModal(<?= (int)$ev['id']; ?>, '<?= addslashes($ev['titre'] ?? ''); ?>')">
                    <i class="fa fa-list-alt"></i>
                  </button>
                  <!-- Modifier -->
                  <button class="btn btn-xs btn-primary mb-1" title="Modifier"
                          onclick="openEditEventModal('<?= (int)$ev['id']; ?>','<?= addslashes($ev['titre'] ?? ''); ?>','<?= addslashes($ev['lieu'] ?? ''); ?>','<?= !empty($ev['date']) ? date('Y-m-d\TH:i', strtotime($ev['date'])) : ''; ?>','<?= addslashes($ev['description'] ?? ''); ?>')">
                    <i class="fa fa-edit"></i>
                  </button>
                  <!-- Supprimer -->
                  <button class="btn btn-xs btn-danger mb-1" title="Supprimer"
                          onclick="confirmDeleteEvent(<?= (int)$ev['id']; ?>,'<?= addslashes($ev['titre'] ?? ''); ?>')">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- ══════════ MODAL CRÉER / MODIFIER ÉVÉNEMENT ══════════ -->
    <div class="modal fade" id="modalEvent" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
          <div class="modal-header bg-inverse text-white">
            <h5 class="modal-title" id="modalEventTitle">Nouvel Événement</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <form action="/saticket/controller/EvenementController.php" method="POST">
            <div class="modal-body">
              <input type="hidden" id="eventId" name="id">
              <input type="hidden" name="redirect_to" value="organisateur">
              <div class="form-group">
                <label class="f-w-600">Titre</label>
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
                <textarea class="form-control" id="eventDesc" name="description" rows="3"></textarea>
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

    <!-- ══════════ MODAL TICKETS ══════════ -->
    <div class="modal fade" id="modalTicket" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalTicketTitle">Gérer les tickets</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form action="/saticket/controller/TicketsController.php" method="POST" class="mb-4">
              <input type="hidden" id="ticketEventId" name="event_id">
              <input type="hidden" name="redirect_to" value="organisateur">
              <h6 class="f-w-700 mb-3"><i class="fa fa-plus-circle text-success mr-1"></i> Ajouter un lot de tickets</h6>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="f-w-600">Type</label>
                    <select class="form-control" name="type" required>
                      <option value="Standard">Standard</option>
                      <option value="VIP">VIP</option>
                      <option value="VVIP">VVIP</option>
                      <option value="Étudiant">Étudiant</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="f-w-600">Prix (FCFA)</label>
                    <input type="number" class="form-control" name="prix" min="0" step="100" placeholder="Ex: 5000" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="f-w-600">Nombre à vendre</label>
                    <input type="number" class="form-control" name="quantite" min="1" placeholder="Ex: 200" required>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-success btn-block" name="btnAddTicket">
                <i class="fa fa-save mr-1"></i> Ajouter ce lot
              </button>
            </form>
            <hr>
            <h6 class="f-w-700 mb-3"><i class="fa fa-bar-chart text-primary mr-1"></i> Suivi des ventes en temps réel</h6>
            <div id="ticketStatsContainer">
              <div class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════ MODAL COMMANDES + ÉMISSION BILLETS ══════════ -->
    <div class="modal fade" id="modalCommandes" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">
          <div class="modal-header text-white" style="background:linear-gradient(135deg,#f7971e,#ffd200);">
            <h5 class="modal-title text-dark" id="modalCommandesTitre">
              <i class="fa fa-list-alt mr-2"></i>Commandes — <span id="modalCommandesEv"></span>
            </h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div id="commandesContainer">
              <div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>
            </div>
            <small class="text-muted float-right mt-2">
              <i class="fa fa-refresh mr-1"></i>Actualisé automatiquement toutes les 10s
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════ MODAL BILLET ÉLECTRONIQUE ══════════ -->
    <div class="modal fade" id="modalBillet" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:18px; overflow:hidden; border:none; background:#e8ecf0;">
          <div class="modal-header" style="background:linear-gradient(135deg,#1d3557,#457b9d); border:none;">
            <h5 class="modal-title text-white"><i class="fa fa-ticket mr-2"></i>Billet Électronique</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body p-4">
            <div class="ticket-wrap" id="billetVisuel">
              <div class="ticket-deco"></div>
              <div class="ticket-logo">SA<span style="color:#fff;">TICKET</span></div>
              <div class="ticket-badge">✔ BILLET CONFIRMÉ</div>
              <div class="ticket-event" id="b-event">—</div>
              <div class="ticket-divider"></div>
              <div class="ticket-row"><span>👤 Acheteur</span><strong id="b-client">—</strong></div>
              <div class="ticket-row"><span>🎟 Quantité</span><strong id="b-qty">—</strong></div>
              <div class="ticket-row"><span>💳 Paiement</span><strong id="b-type">—</strong></div>
              <div class="ticket-divider"></div>
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="ticket-amount" id="b-montant">—</div>
                  <div style="font-size:11px;opacity:.6;">Montant payé</div>
                </div>
                <div class="ticket-qr">
                  <img id="b-qr-img" src="" alt="QR" style="width:85px;height:85px;display:block;">
                </div>
              </div>
              <div class="ticket-ref-txt" id="b-ref">REF: —</div>
            </div>
            <div class="d-flex mt-3" style="gap:10px;">
              <button class="btn btn-primary flex-fill" onclick="imprimerBillet()">
                <i class="fa fa-print mr-1"></i> Imprimer
              </button>
              <button class="btn btn-success flex-fill" onclick="telechargerBillet()">
                <i class="fa fa-download mr-1"></i> Télécharger PNG
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php require_once __DIR__ . '/../../sections/admin/sectionConfig.php'; ?>
  </div>

  <?php require_once __DIR__ . '/../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <script>
    var refreshTicketInterval   = null;
    var refreshCommandesInterval = null;
    var currentEventId          = null;

    // ══ Événement : créer ══
    function openAddEventModal() {
      $('#modalEventTitle').text('Créer un Nouvel Événement');
      $('#eventId').val(''); $('#eventTitre').val(''); $('#eventLieu').val('');
      $('#eventDate').val(''); $('#eventDesc').val('');
      $('#btnSubmitEvent').attr('name','btnSaveEvent');
      $('#modalEvent').modal('show');
    }

    // ══ Événement : modifier ══
    function openEditEventModal(id,titre,lieu,date,desc) {
      $('#modalEventTitle').text('Modifier : ' + titre);
      $('#eventId').val(id); $('#eventTitre').val(titre); $('#eventLieu').val(lieu);
      $('#eventDate').val(date); $('#eventDesc').val(desc);
      $('#btnSubmitEvent').attr('name','btnUpdateEvent');
      $('#modalEvent').modal('show');
    }

    // ══ Événement : supprimer ══
    function confirmDeleteEvent(id,name) {
      Swal.fire({
        title:'Supprimer ' + name + ' ?', text:"Cette action est irréversible.",
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#ff5b57', cancelButtonColor:'#f2f3f4',
        confirmButtonText:'Oui, supprimer', cancelButtonText:'Annuler', reverseButtons:true
      }).then(r => {
        if (r.isConfirmed)
          window.location.href='/saticket/controller/EvenementController.php?action=delete&id='+id+'&redirect_to=organisateur';
      });
    }

    // ══ Tickets : modale gestion ══
    function openTicketModal(eventId, eventTitre) {
      currentEventId = eventId;
      $('#modalTicketTitle').text('Tickets — ' + eventTitre);
      $('#ticketEventId').val(eventId);
      loadTicketStats(eventId);
      $('#modalTicket').modal('show');
      if (refreshTicketInterval) clearInterval(refreshTicketInterval);
      refreshTicketInterval = setInterval(function(){
        loadTicketStats(currentEventId);
        refreshRowStats(currentEventId);
      }, 10000);
    }
    $('#modalTicket').on('hidden.bs.modal', function(){
      if (refreshTicketInterval) clearInterval(refreshTicketInterval);
    });

    function loadTicketStats(eventId) {
      $.getJSON('/saticket/controller/TicketsController.php?action=getByEvent&event_id='+eventId, function(data){
        var html = '';
        if (!data || data.length === 0) {
          html = '<div class="alert alert-warning text-center">Aucun lot de tickets défini.</div>';
        } else {
          html += '<div class="table-responsive"><table class="table table-bordered table-sm">';
          html += '<thead class="thead-dark"><tr><th>Type</th><th>Prix</th><th>Total</th><th>Vendus</th><th>Restants</th><th>Progression</th></tr></thead><tbody>';
          $.each(data, function(i,t){
            var total=parseInt(t.quantite_totale)||0, vendus=parseInt(t.quantite_vendue)||0;
            var restant=total-vendus, pct=total>0?Math.round(vendus/total*100):0;
            var cls=pct>=100?'danger':(pct>=75?'warning':'success');
            html+='<tr><td><span class="badge badge-secondary">'+escHtml(t.type)+'</span></td>';
            html+='<td>'+parseInt(t.prix).toLocaleString('fr-FR')+' FCFA</td>';
            html+='<td><strong>'+total+'</strong></td>';
            html+='<td><span class="text-'+cls+' f-w-700">'+vendus+'</span></td>';
            html+='<td>'+restant+'</td>';
            html+='<td style="min-width:110px"><div class="progress mb-1" style="height:9px"><div class="progress-bar bg-'+cls+'" style="width:'+pct+'%"></div></div><small class="text-muted">'+pct+'%</small></td></tr>';
          });
          html += '</tbody></table></div>';
          html += '<small class="text-muted float-right"><i class="fa fa-refresh mr-1"></i>Mise à jour auto. toutes les 10s</small>';
        }
        $('#ticketStatsContainer').html(html);
      }).fail(function(){
        $('#ticketStatsContainer').html('<div class="alert alert-danger">Impossible de charger les statistiques.</div>');
      });
    }

    function refreshRowStats(eventId) {
      $.getJSON('/saticket/controller/TicketsController.php?action=getEventSummary&event_id='+eventId, function(data){
        if (!data) return;
        var total=parseInt(data.total)||0, vendus=parseInt(data.vendus)||0;
        var pct=total>0?Math.round(vendus/total*100):0;
        var cls=pct>=100?'danger':(pct>=75?'warning':'success');
        var cell=$('#stats-ev-'+eventId);
        if (cell.length) {
          cell.html(
            '<span class="badge badge-'+cls+' f-s-12 mb-1">'+vendus+' / '+(total>0?total:'—')+'</span>'+
            (total>0?'<div class="progress" style="height:7px"><div class="progress-bar bg-'+cls+'" style="width:'+pct+'%"></div></div><small class="text-muted">'+pct+'% vendus</small>':'<small class="text-muted">Aucun ticket défini</small>')
          );
        }
      });
    }

    // ══ Commandes : modale ══
    function openCommandesModal(eventId, eventTitre) {
      currentEventId = eventId;
      $('#modalCommandesEv').text(eventTitre);
      loadCommandes(eventId);
      $('#modalCommandes').modal('show');
      if (refreshCommandesInterval) clearInterval(refreshCommandesInterval);
      refreshCommandesInterval = setInterval(function(){ loadCommandes(currentEventId); }, 10000);
    }
    $('#modalCommandes').on('hidden.bs.modal', function(){
      if (refreshCommandesInterval) clearInterval(refreshCommandesInterval);
    });

    function loadCommandes(eventId) {
      $.getJSON('/saticket/controller/TicketsController.php?action=getCommandesEvenement&event_id='+eventId, function(data){
        var html = '';
        if (!data || data.length === 0) {
          html = '<div class="alert alert-info text-center"><i class="fa fa-info-circle mr-1"></i>Aucune commande pour cet événement.</div>';
        } else {
          html += '<div class="table-responsive"><table class="table table-bordered table-sm commandes-table">';
          html += '<thead class="thead-dark"><tr><th>#</th><th>Référence</th><th>Acheteur</th><th class="text-center">Qté</th><th>Montant</th><th>Paiement</th><th>Date</th><th class="text-center">Billet</th></tr></thead><tbody>';
          $.each(data, function(i, c){
            var statut = (c.payment_statut || '').toLowerCase().trim();
            var isPaye = ['réussi','success','validated','réussie'].indexOf(statut) >= 0;
            var statutBadge = isPaye
              ? '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Payé</span>'
              : (statut !== ''
                  ? '<span class="badge badge-danger">'+escHtml(c.payment_statut)+'</span>'
                  : '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Attente</span>');
            var btnBillet = isPaye
              ? '<button class="btn btn-xs btn-primary btn-emettre" onclick="emettreTicket(\''+escHtml(c.reference||'')+'\',\''+escHtml(c.evenement_titre||'')+'\',\''+escHtml(c.client_nom||'')+'\','+parseInt(c.quantite||0)+','+parseInt(c.montant||0)+',\''+escHtml(c.payment_type||'Wave')+'\')"><i class="fa fa-qrcode mr-1"></i>Émettre</button>'
              : '<span class="text-muted" style="font-size:11px;">—</span>';
            html += '<tr>';
            html += '<td class="text-muted" style="font-size:11px;">'+(i+1)+'</td>';
            html += '<td style="font-size:11px;font-weight:700;letter-spacing:1px;">'+escHtml(c.reference||'—')+'</td>';
            html += '<td>'+escHtml(c.client_nom||'—')+'</td>';
            html += '<td class="text-center"><strong>'+parseInt(c.quantite||0)+'</strong></td>';
            html += '<td class="text-success f-w-700">'+parseInt(c.montant||0).toLocaleString('fr-FR')+' FCFA</td>';
            html += '<td>'+statutBadge+'</td>';
            html += '<td style="font-size:11px;">'+(c.createdat?c.createdat.substring(0,16).replace('T',' '):'—')+'</td>';
            html += '<td class="text-center">'+btnBillet+'</td>';
            html += '</tr>';
          });
          html += '</tbody></table></div>';
        }
        $('#commandesContainer').html(html);
      }).fail(function(){
        $('#commandesContainer').html('<div class="alert alert-danger">Impossible de charger les commandes.</div>');
      });
    }

    // ══ Billet électronique ══
    function emettreTicket(ref, eventTitre, clientNom, qty, montant, typePaiement) {
      document.getElementById('b-event').textContent   = eventTitre;
      document.getElementById('b-client').textContent  = clientNom;
      document.getElementById('b-qty').textContent     = qty + ' billet'+(qty>1?'s':'');
      document.getElementById('b-type').textContent    = typePaiement;
      document.getElementById('b-montant').textContent = montant.toLocaleString('fr-FR')+' FCFA';
      document.getElementById('b-ref').textContent     = 'REF: '+ref;
      var qrData = encodeURIComponent('SATICKET|'+ref+'|'+eventTitre+'|QTY:'+qty+'|CLIENT:'+clientNom);
      document.getElementById('b-qr-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=85x85&data='+qrData;
      // Fermer la modale commandes et ouvrir le billet
      $('#modalCommandes').modal('hide');
      setTimeout(function(){ $('#modalBillet').modal('show'); }, 400);
    }

    function imprimerBillet() {
      var billet = document.getElementById('billetVisuel').outerHTML;
      var w = window.open('','_blank');
      w.document.write('<html><head><title>Billet SaTicket</title><style>');
      w.document.write('body{font-family:Arial,sans-serif;background:#e8ecf0;display:flex;justify-content:center;padding:30px;}');
      w.document.write('.ticket-wrap{background:linear-gradient(135deg,#1d3557,#457b9d);color:#fff;border-radius:20px;padding:26px 28px;max-width:440px;position:relative;overflow:hidden;}');
      w.document.write('.ticket-wrap::before,.ticket-wrap::after{content:"";position:absolute;width:42px;height:42px;background:#f0f0f0;border-radius:50%;top:50%;transform:translateY(-50%);}');
      w.document.write('.ticket-wrap::before{left:-21px}.ticket-wrap::after{right:-21px}');
      w.document.write('.ticket-deco{position:absolute;right:-25px;bottom:-25px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,.07);}');
      w.document.write('.ticket-logo{font-size:20px;font-weight:900;letter-spacing:2px;color:#a8dadc;}');
      w.document.write('.ticket-badge{background:rgba(0,176,155,.22);border:1px solid #00b09b;color:#7fffd4;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;display:inline-block;margin:6px 0 8px;}');
      w.document.write('.ticket-event{font-size:17px;font-weight:800;margin-bottom:4px;}');
      w.document.write('.ticket-divider{border-top:2px dashed rgba(255,255,255,.28);margin:12px 0;}');
      w.document.write('.ticket-row{display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;opacity:.88;}');
      w.document.write('.ticket-row strong{color:#fff;}.ticket-amount{font-size:22px;font-weight:900;}');
      w.document.write('.ticket-ref-txt{font-size:10px;letter-spacing:2px;opacity:.5;margin-top:8px;}');
      w.document.write('.ticket-qr{background:#fff;border-radius:8px;padding:5px;display:inline-block;}');
      w.document.write('.d-flex{display:flex;align-items:center;justify-content:space-between;}');
      w.document.write('</style></head><body>'+billet+'</body></html>');
      w.document.close();
      setTimeout(function(){ w.print(); }, 600);
    }

    function telechargerBillet() {
      html2canvas(document.getElementById('billetVisuel'), {scale:2, backgroundColor:null, useCORS:true}).then(function(canvas){
        var link = document.createElement('a');
        link.download = 'billet-saticket-'+Date.now()+'.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
      });
    }

    function escHtml(str) { return $('<div>').text(String(str)).html(); }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>Swal.fire({title:"Succès !",text:"<?= htmlspecialchars($_GET['messageSuccess']); ?>",icon:"success",confirmButtonColor:"#00acac"});</script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>Swal.fire({title:"Erreur !",text:"<?= htmlspecialchars($_GET['messageError']); ?>",icon:"error",confirmButtonColor:"#ff5b57"});</script>
  <?php endif; ?>
</body>
</html>