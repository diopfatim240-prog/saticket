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
if ($role_actuel !== 'acheteur') {
    header("Location: /saticket/admin.php?error=acces_refuse");
    exit();
}

require_once __DIR__ . '/../../../model/CommandesRepository.php';
$commandeRepo   = new CommandesRepository();
$listeCommandes = $commandeRepo->getOrdersByUser($id_connecte);

// Enrichir avec statut paiement
if (!empty($listeCommandes)) {
    foreach ($listeCommandes as &$commande) {
        $payment = $commandeRepo->getPaymentStatusByCommandeId((int)($commande['id'] ?? 0));
        $commande['payment_statut']     = $payment['statut'] ?? null;
        $commande['payment_type']       = $payment['type']   ?? null;
        $commande['payment_id_paiement'] = $payment['id']    ?? null;
    }
    unset($commande);
}

$nomAcheteur = $_SESSION['nom'] ?? $_SESSION['prenom'] ?? 'Acheteur';
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/../../sections/admin/head.php'; ?>
<style>
/* ===== MES ACHATS ===== */
.billet-card {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(0,0,0,.09);
    margin-bottom: 18px;
    display: flex;
    background: #fff;
    transition: box-shadow .2s;
}
.billet-card:hover { box-shadow: 0 6px 28px rgba(0,0,0,.15); }

.billet-stripe {
    width: 8px;
    flex-shrink: 0;
}
.billet-stripe.paye   { background: linear-gradient(180deg,#00b09b,#96c93d); }
.billet-stripe.attente { background: linear-gradient(180deg,#f7971e,#ffd200); }
.billet-stripe.echoue  { background: linear-gradient(180deg,#ff5b57,#e74c3c); }

.billet-body {
    flex: 1; padding: 16px 20px;
    display: flex; align-items: center; flex-wrap: wrap; gap: 14px;
}
.billet-ref {
    font-size: 11px; color: #aaa; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
}
.billet-titre { font-size: 16px; font-weight: 700; color: #1d3557; }
.billet-meta  { font-size: 13px; color: #6c757d; }

.billet-montant { font-size: 18px; font-weight: 800; color: #00acac; white-space: nowrap; }
.billet-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; margin-left: auto; }

/* Billet électronique style ticket */
.ticket-electronique {
    background: linear-gradient(135deg,#1d3557 0%,#457b9d 100%);
    color: #fff;
    border-radius: 18px;
    padding: 28px 32px;
    position: relative;
    overflow: hidden;
    min-width: 320px;
    max-width: 480px;
    margin: 0 auto;
    box-shadow: 0 8px 32px rgba(29,53,87,.35);
}
.ticket-electronique::before {
    content: '';
    position: absolute;
    width: 40px; height: 40px;
    background: #f5f5f5;
    border-radius: 50%;
    left: -20px; top: 50%;
    transform: translateY(-50%);
}
.ticket-electronique::after {
    content: '';
    position: absolute;
    width: 40px; height: 40px;
    background: #f5f5f5;
    border-radius: 50%;
    right: -20px; top: 50%;
    transform: translateY(-50%);
}
.ticket-divider {
    border-top: 2px dashed rgba(255,255,255,.35);
    margin: 16px 0;
}
.ticket-qr {
    background: #fff; border-radius: 8px; padding: 8px;
    display: inline-block; margin-top: 4px;
}
.ticket-logo { font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #a8dadc; }
.ticket-event { font-size: 20px; font-weight: 800; margin: 6px 0 2px; }
.ticket-row-info { display: flex; justify-content: space-between; font-size: 13px; opacity: .85; margin-bottom: 4px; }
.ticket-badge-paye {
    background: rgba(0,176,155,.25); border: 1px solid #00b09b;
    color: #7fffd4; border-radius: 20px; padding: 3px 14px;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    display: inline-block; margin-bottom: 10px;
}
.ticket-ref { font-size: 11px; letter-spacing: 2px; opacity: .7; margin-top: 8px; }

/* vide */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 64px; color: #dee2e6; margin-bottom: 20px; }
</style>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once __DIR__ . '/../../sections/admin/menuHaut.php'; ?>
    <?php require_once __DIR__ . '/../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">

      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:10px;">
        <div>
          <h1 class="page-header mb-0">🎫 Mes Billets</h1>
          <p class="text-muted mt-1 mb-0">Retrouvez tous vos billets achetés et téléchargez vos billets électroniques</p>
        </div>
        <a href="/saticket/view/pages/acheteur/parcourir_evenements.php" class="btn btn-primary">
          <i class="fa fa-search mr-1"></i> Parcourir les événements
        </a>
      </div>

      <?php if (empty($listeCommandes)): ?>
        <div class="panel panel-inverse">
          <div class="panel-body empty-state">
            <i class="fa fa-ticket"></i>
            <h4 class="text-muted">Vous n'avez encore aucun billet acheté.</h4>
            <p class="text-muted mb-4">Parcourez les événements disponibles et achetez votre premier billet !</p>
            <a href="/saticket/view/pages/acheteur/parcourir_evenements.php" class="btn btn-primary btn-lg">
              <i class="fa fa-calendar mr-1"></i> Voir les événements
            </a>
          </div>
        </div>
      <?php else: ?>

        <!-- Stats rapides -->
        <div class="row mb-4">
          <?php
            $totalPaye   = 0; $nbPaye = 0; $nbAttente = 0;
            foreach ($listeCommandes as $c) {
              $s = strtolower(trim((string)($c['payment_statut'] ?? '')));
              if (in_array($s, ['réussi','success','validated','réussie'])) { $nbPaye++; $totalPaye += (int)($c['montant'] ?? 0); }
              else $nbAttente++;
            }
          ?>
          <div class="col-md-4 mb-3">
            <div class="panel panel-inverse mb-0" style="border-radius:12px;">
              <div class="panel-body text-center py-3">
                <div style="font-size:28px; font-weight:800; color:#00acac;"><?= count($listeCommandes); ?></div>
                <div class="text-muted" style="font-size:13px;">Commandes totales</div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="panel panel-inverse mb-0" style="border-radius:12px;">
              <div class="panel-body text-center py-3">
                <div style="font-size:28px; font-weight:800; color:#00b09b;"><?= $nbPaye; ?></div>
                <div class="text-muted" style="font-size:13px;">Billets confirmés</div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="panel panel-inverse mb-0" style="border-radius:12px;">
              <div class="panel-body text-center py-3">
                <div style="font-size:28px; font-weight:800; color:#f7971e;"><?= number_format($totalPaye, 0, ',', ' '); ?> <small style="font-size:14px;">FCFA</small></div>
                <div class="text-muted" style="font-size:13px;">Total dépensé</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des commandes -->
        <div class="panel panel-inverse">
          <div class="panel-heading">
            <h4 class="panel-title">Historique (<?= count($listeCommandes); ?> billet<?= count($listeCommandes) > 1 ? 's' : ''; ?>)</h4>
          </div>
          <div class="panel-body">
            <?php foreach ($listeCommandes as $commande):
              $payStatut = strtolower(trim((string)($commande['payment_statut'] ?? '')));
              $isPaye    = in_array($payStatut, ['réussi','success','validated','réussie']);
              $isEchoue  = ($payStatut !== '' && !$isPaye);
              $stripeClass = $isPaye ? 'paye' : ($isEchoue ? 'echoue' : 'attente');
            ?>
            <div class="billet-card">
              <div class="billet-stripe <?= $stripeClass; ?>"></div>
              <div class="billet-body">

                <div style="flex:1; min-width:200px;">
                  <div class="billet-ref"><?= htmlspecialchars($commande['reference'] ?? 'SANS-REF'); ?></div>
                  <div class="billet-titre"><?= htmlspecialchars($commande['evenement_titre'] ?? 'Événement'); ?></div>
                  <div class="billet-meta mt-1">
                    <i class="fa fa-ticket mr-1"></i><?= (int)($commande['quantite'] ?? 0); ?> billet<?= (int)($commande['quantite'] ?? 0) > 1 ? 's' : ''; ?> &nbsp;·&nbsp;
                    <i class="fa fa-calendar mr-1"></i><?= !empty($commande['createdat']) ? date('d/m/Y à H:i', strtotime($commande['createdat'])) : 'N/A'; ?>
                    <?php if (!empty($commande['payment_type'])): ?>
                      &nbsp;·&nbsp;<i class="fa fa-credit-card mr-1"></i><?= htmlspecialchars($commande['payment_type']); ?>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="billet-montant"><?= number_format($commande['montant'] ?? 0, 0, ',', ' '); ?> FCFA</div>

                <div class="billet-actions">
                  <?php if ($isPaye): ?>
                    <span class="badge badge-success" style="font-size:12px; padding:6px 12px;"><i class="fa fa-check-circle mr-1"></i> Payé</span>
                    <button class="btn btn-xs btn-primary" onclick="voirBillet(<?= $commande['id']; ?>, '<?= addslashes($commande['evenement_titre'] ?? ''); ?>', '<?= addslashes($commande['reference'] ?? ''); ?>', <?= (int)($commande['quantite'] ?? 0); ?>, <?= (int)($commande['montant'] ?? 0); ?>, '<?= addslashes($commande['payment_type'] ?? 'Wave'); ?>')">
                      <i class="fa fa-qrcode mr-1"></i> Voir billet
                    </button>
                  <?php elseif ($isEchoue): ?>
                    <span class="badge badge-danger" style="font-size:12px; padding:6px 12px;"><i class="fa fa-times-circle mr-1"></i> Échoué</span>
                  <?php else: ?>
                    <span class="badge badge-warning" style="font-size:12px; padding:6px 12px;"><i class="fa fa-clock-o mr-1"></i> En attente</span>
                  <?php endif; ?>
                </div>

              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      <?php endif; ?>
    </div>

    <!-- ===== MODAL BILLET ÉLECTRONIQUE ===== -->
    <div class="modal fade modal-billet" id="modalBillet" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <div class="modal-content" style="border-radius:18px; overflow:hidden; border:none; background:#e8ecf0;">
          <div class="modal-header" style="background:linear-gradient(135deg,#1d3557,#457b9d); border:none;">
            <h5 class="modal-title text-white"><i class="fa fa-ticket mr-2"></i>Billet Électronique</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body p-4">
            <div class="ticket-electronique" id="billetVisuel">
              <div class="ticket-logo">SA<span style="color:#fff;">TICKET</span></div>
              <div class="ticket-badge-paye">✔ BILLET CONFIRMÉ</div>
              <div class="ticket-event" id="b-event">Nom de l'événement</div>

              <div class="ticket-row-info">
                <span>👤 Titulaire</span>
                <strong><?= htmlspecialchars($nomAcheteur); ?></strong>
              </div>
              <div class="ticket-row-info">
                <span>🎟 Quantité</span>
                <strong id="b-qty">—</strong>
              </div>
              <div class="ticket-row-info">
                <span>💳 Paiement</span>
                <strong id="b-type">—</strong>
              </div>

              <div class="ticket-divider"></div>

              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div style="font-size:22px; font-weight:900;" id="b-montant">—</div>
                  <div style="font-size:11px; opacity:.7;">Montant payé</div>
                </div>
                <div class="ticket-qr" id="b-qr">
                  <!-- QR code généré dynamiquement -->
                </div>
              </div>

              <div class="ticket-ref" id="b-ref">REF: —</div>
            </div>

            <div class="text-center mt-3">
              <button class="btn btn-primary" onclick="imprimerBillet()">
                <i class="fa fa-print mr-1"></i> Imprimer / Télécharger
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <?php require_once __DIR__ . '/../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- QR code simple via API Google Charts -->
  <script>
    function voirBillet(id, eventTitre, ref, qty, montant, typePaiement) {
      document.getElementById('b-event').textContent   = eventTitre;
      document.getElementById('b-qty').textContent     = qty + ' billet' + (qty > 1 ? 's' : '');
      document.getElementById('b-type').textContent    = typePaiement;
      document.getElementById('b-montant').textContent = formatMoney(montant) + ' FCFA';
      document.getElementById('b-ref').textContent     = 'REF: ' + ref;

      // QR code avec les données du billet
      var qrData = encodeURIComponent('SATICKET|' + ref + '|' + eventTitre + '|QTY:' + qty);
      document.getElementById('b-qr').innerHTML =
        '<img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=' + qrData + '" alt="QR" style="width:90px;height:90px;display:block;">';

      $('#modalBillet').modal('show');
    }

    function imprimerBillet() {
      var billet = document.getElementById('billetVisuel').outerHTML;
      var w = window.open('', '_blank');
      w.document.write('<html><head><title>Billet SaTicket</title>');
      w.document.write('<style>body{font-family:sans-serif;background:#e8ecf0;display:flex;justify-content:center;padding:30px;}');
      w.document.write('.ticket-electronique{background:linear-gradient(135deg,#1d3557,#457b9d);color:#fff;border-radius:18px;padding:28px 32px;max-width:480px;position:relative;}');
      w.document.write('.ticket-electronique::before,.ticket-electronique::after{content:"";position:absolute;width:40px;height:40px;background:#e8ecf0;border-radius:50%;top:50%;transform:translateY(-50%);}');
      w.document.write('.ticket-electronique::before{left:-20px;}.ticket-electronique::after{right:-20px;}');
      w.document.write('.ticket-logo{font-size:28px;font-weight:900;letter-spacing:2px;color:#a8dadc;}');
      w.document.write('.ticket-badge-paye{background:rgba(0,176,155,.25);border:1px solid #00b09b;color:#7fffd4;border-radius:20px;padding:3px 14px;font-size:11px;font-weight:700;display:inline-block;margin-bottom:10px;}');
      w.document.write('.ticket-event{font-size:20px;font-weight:800;margin:6px 0 2px;}');
      w.document.write('.ticket-row-info{display:flex;justify-content:space-between;font-size:13px;opacity:.85;margin-bottom:4px;}');
      w.document.write('.ticket-divider{border-top:2px dashed rgba(255,255,255,.35);margin:16px 0;}');
      w.document.write('.ticket-qr{background:#fff;border-radius:8px;padding:8px;display:inline-block;}');
      w.document.write('.ticket-ref{font-size:11px;letter-spacing:2px;opacity:.7;margin-top:8px;}');
      w.document.write('</style></head><body>' + billet + '</body></html>');
      w.document.close();
      setTimeout(function(){ w.print(); }, 600);
    }

    function formatMoney(n) {
      return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
      Swal.fire({
        title: '🎉 Commande confirmée !',
        html: '<p><?= htmlspecialchars($_GET['messageSuccess']); ?></p><p class="text-muted" style="font-size:13px;">Votre billet apparaîtra ici une fois le paiement validé.</p>',
        icon: 'success', confirmButtonColor: '#00acac'
      });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
      Swal.fire({ title: 'Erreur', text: '<?= htmlspecialchars($_GET['messageError']); ?>', icon: 'error', confirmButtonColor: '#ff5b57' });
    </script>
  <?php endif; ?>
</body>
</html>