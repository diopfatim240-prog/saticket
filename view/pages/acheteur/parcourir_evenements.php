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

require_once __DIR__ . '/../../../model/EvenementRepository.php';
require_once __DIR__ . '/../../../model/TicketsRepository.php';

$evenementRepo  = new EvenementRepository();
$ticketsRepo    = new TicketsRepository();
$listeEvenements = $evenementRepo->getAll();

// Pré-charger les tickets de chaque événement
$ticketsParEvenement = [];
foreach ($listeEvenements as $ev) {
    $ticketsParEvenement[$ev['id']] = $ticketsRepo->getTicketsByEvent((int)$ev['id']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/../../sections/admin/head.php'; ?>
<style>
/* ===== CARD EVENEMENT ===== */
.ev-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
    transition: transform .22s, box-shadow .22s;
    background: #fff;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.ev-card:hover { transform: translateY(-6px); box-shadow: 0 12px 36px rgba(0,0,0,.16); }

.ev-card-banner {
    height: 148px;
    background: linear-gradient(135deg,#1d3557 0%,#457b9d 60%,#a8dadc 100%);
    display: flex; align-items: center; justify-content: center;
    position: relative;
    overflow: hidden;
}
.ev-card-banner .ev-icon {
    font-size: 54px; color: rgba(255,255,255,.25);
    position: absolute; right: 18px; bottom: 10px;
}
.ev-card-banner .ev-date-badge {
    position: absolute; top: 14px; left: 14px;
    background: rgba(255,255,255,.92);
    border-radius: 10px; padding: 6px 14px;
    font-size: 12px; font-weight: 700; color: #1d3557;
    display: flex; align-items: center; gap: 5px;
}
.ev-card-banner .ev-titre {
    color: #fff; font-size: 18px; font-weight: 700;
    text-shadow: 0 2px 8px rgba(0,0,0,.35);
    padding: 0 20px; text-align: center; z-index: 1;
    line-height: 1.3;
}

.ev-card-body { padding: 18px 20px; flex: 1; display: flex; flex-direction: column; }
.ev-meta { color: #6c757d; font-size: 13px; margin-bottom: 10px; display: flex; gap: 14px; flex-wrap: wrap; }
.ev-meta span { display: flex; align-items: center; gap: 5px; }
.ev-desc { font-size: 13.5px; color: #444; flex: 1; margin-bottom: 14px; line-height: 1.6; }

/* ticket selector */
.ticket-row {
    background: #f8f9fa; border-radius: 10px; padding: 12px 14px; margin-bottom: 10px;
    border: 1px solid #e9ecef;
}
.ticket-row label { font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block; }
.ticket-price { color: #00acac; font-weight: 700; font-size: 15px; }
.ticket-stock { font-size: 11px; color: #aaa; }

/* boutons paiement */
.btn-wave {
    background: linear-gradient(135deg,#0060df,#0090ff);
    color: #fff; border: none; border-radius: 10px;
    padding: 10px 0; font-weight: 700; font-size: 14px;
    width: 100%; transition: opacity .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-wave:hover { opacity: .88; color: #fff; }

.btn-om {
    background: linear-gradient(135deg,#ff6600,#ffaa00);
    color: #fff; border: none; border-radius: 10px;
    padding: 10px 0; font-weight: 700; font-size: 14px;
    width: 100%; transition: opacity .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-om:hover { opacity: .88; color: #fff; }

.pay-btns { display: flex; gap: 8px; margin-top: 10px; }
.pay-btns .col-pay { flex: 1; }

/* no ticket */
.no-ticket { background: #fff8f0; border: 1px dashed #ffc107; border-radius: 10px; padding: 12px; text-align: center; color: #b0803a; font-size: 13px; }

/* modal billet */
.modal-billet .modal-content { border-radius: 16px; border: none; overflow: hidden; }
.modal-billet .modal-header { background: linear-gradient(135deg,#1d3557,#457b9d); color: #fff; border: none; }

/* filtre */
.search-bar { max-width: 380px; }
.search-bar .form-control { border-radius: 20px 0 0 20px; border-right: none; }
.search-bar .btn { border-radius: 0 20px 20px 0; }

/* badge event count */
.ev-count-badge { background: #1d3557; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 13px; font-weight: 600; }
</style>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once __DIR__ . '/../../sections/admin/menuHaut.php'; ?>
    <?php require_once __DIR__ . '/../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">

      <!-- En-tête -->
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:12px;">
        <div>
          <h1 class="page-header mb-0">🎟 Découvrir des Événements</h1>
          <p class="text-muted mt-1 mb-0">Achetez vos billets en toute simplicité via Wave ou Orange Money</p>
        </div>
        <span class="ev-count-badge"><?= count($listeEvenements); ?> événement<?= count($listeEvenements) > 1 ? 's' : ''; ?></span>
      </div>

      <!-- Barre de recherche -->
      <div class="mb-4">
        <div class="input-group search-bar">
          <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un événement...">
          <div class="input-group-append">
            <button class="btn btn-primary"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>

      <!-- Grille événements -->
      <?php if (empty($listeEvenements)): ?>
        <div class="text-center py-5">
          <i class="fa fa-calendar-times-o fa-4x text-muted mb-3"></i>
          <h4 class="text-muted">Aucun événement disponible pour le moment.</h4>
          <p class="text-muted">Revenez bientôt !</p>
        </div>
      <?php else: ?>
        <div class="row" id="eventsGrid">
          <?php foreach ($listeEvenements as $event):
            $tickets = $ticketsParEvenement[$event['id']] ?? [];
            $prixMin  = !empty($tickets) ? min(array_column($tickets, 'prix')) : null;
            $totalDispo = !empty($tickets) ? array_sum(array_column($tickets, 'quantite')) : 0;
            $dateEv = !empty($event['date']) ? date('d M Y', strtotime($event['date'])) : 'Date N/A';
            $heureEv = !empty($event['date']) ? date('H:i', strtotime($event['date'])) : '';

            // Couleur banner selon index
            $gradients = [
                'linear-gradient(135deg,#1d3557 0%,#457b9d 100%)',
                'linear-gradient(135deg,#6a0572 0%,#c77dff 100%)',
                'linear-gradient(135deg,#0f4c75 0%,#1b9aaa 100%)',
                'linear-gradient(135deg,#b5451b 0%,#f4a261 100%)',
                'linear-gradient(135deg,#1b4332 0%,#52b788 100%)',
                'linear-gradient(135deg,#03045e 0%,#00b4d8 100%)',
            ];
            static $idx = 0;
            $grad = $gradients[$idx % count($gradients)];
            $idx++;
          ?>
          <div class="col-xl-4 col-lg-6 col-md-6 mb-4 ev-item"
               data-titre="<?= strtolower(htmlspecialchars($event['titre'] ?? '')); ?>"
               data-lieu="<?= strtolower(htmlspecialchars($event['lieu'] ?? '')); ?>">
            <div class="ev-card">

              <!-- Banner -->
              <div class="ev-card-banner" style="background:<?= $grad; ?>">
                <div class="ev-date-badge"><i class="fa fa-calendar"></i> <?= $dateEv; ?><?= $heureEv ? ' · ' . $heureEv : ''; ?></div>
                <div class="ev-titre"><?= htmlspecialchars($event['titre'] ?? 'Sans titre'); ?></div>
                <i class="fa fa-music ev-icon"></i>
              </div>

              <!-- Body -->
              <div class="ev-card-body">
                <div class="ev-meta">
                  <span><i class="fa fa-map-marker text-danger"></i> <?= htmlspecialchars($event['lieu'] ?? 'Lieu N/A'); ?></span>
                  <?php if ($prixMin !== null): ?>
                    <span><i class="fa fa-tag text-success"></i> À partir de <strong><?= number_format($prixMin, 0, ',', ' '); ?> FCFA</strong></span>
                  <?php endif; ?>
                </div>

                <div class="ev-desc">
                  <?= nl2br(htmlspecialchars(substr($event['description'] ?? '', 0, 180))); ?>
                  <?= strlen($event['description'] ?? '') > 180 ? '…' : ''; ?>
                </div>

                <?php if (!empty($tickets)): ?>
                  <!-- Sélecteur de ticket -->
                  <div class="ticket-row">
                    <label><i class="fa fa-ticket mr-1 text-primary"></i>Choisir un billet</label>
                    <select class="form-control form-control-sm ticket-select" id="ticket-select-<?= (int)$event['id']; ?>" onchange="updateTicketInfo(<?= (int)$event['id']; ?>)">
                      <?php foreach ($tickets as $t): ?>
                        <option value="<?= (int)$t['id']; ?>"
                                data-prix="<?= (int)$t['prix']; ?>"
                                data-dispo="<?= (int)$t['total']; ?>"
                                data-type="<?= htmlspecialchars($t['type']); ?>">
                          <?= htmlspecialchars($t['type']); ?> — <?= number_format($t['prix'], 0, ',', ' '); ?> FCFA
                          (<?= (int)$t['total']; ?> dispo)
                        </option>
                      <?php endforeach; ?>
                    </select>

                    <div class="d-flex align-items-center mt-2" style="gap:10px;">
                      <label class="mb-0" style="font-size:13px; white-space:nowrap;">Qté :</label>
                      <input type="number" class="form-control form-control-sm" id="qty-<?= (int)$event['id']; ?>"
                             value="1" min="1" max="<?= (int)($tickets[0]['total'] ?? 1); ?>" style="width:70px;">
                      <span class="ticket-price" id="total-price-<?= (int)$event['id']; ?>">
                        <?= number_format($tickets[0]['prix'] ?? 0, 0, ',', ' '); ?> FCFA
                      </span>
                    </div>
                    <div class="ticket-stock mt-1" id="stock-info-<?= (int)$event['id']; ?>">
                      Stock : <?= (int)($tickets[0]['total'] ?? 0); ?> billets disponibles
                    </div>
                  </div>

                  <!-- Boutons paiement -->
                  <div class="pay-btns">
                    <div class="col-pay">
                      <button class="btn-wave" onclick="ouvrirPaiement(<?= (int)$event['id']; ?>, 'wave')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="12" fill="#fff" opacity=".2"/><path d="M6 12c1.5-3 3-5 6-5s4.5 2 6 5c-1.5 3-3 5-6 5s-4.5-2-6-5z" fill="#fff"/></svg>
                        Payer Wave
                      </button>
                    </div>
                    <div class="col-pay">
                      <button class="btn-om" onclick="ouvrirPaiement(<?= (int)$event['id']; ?>, 'orange')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#fff" opacity=".25"/><circle cx="12" cy="12" r="5" fill="#fff"/></svg>
                        Orange Money
                      </button>
                    </div>
                  </div>

                <?php else: ?>
                  <div class="no-ticket">
                    <i class="fa fa-times-circle mr-1"></i> Aucun billet disponible pour cet événement
                  </div>
                <?php endif; ?>

              </div><!-- /ev-card-body -->
            </div><!-- /ev-card -->
          </div><!-- /col -->
          <?php endforeach; ?>
        </div><!-- /row -->
      <?php endif; ?>

    </div><!-- /content -->

    <!-- ======= MODAL PAIEMENT ======= -->
    <div class="modal fade" id="modalPaiement" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:440px;">
        <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none;">

          <!-- Header dynamique selon méthode -->
          <div class="modal-header" id="modalPaiementHeader" style="border:none;">
            <h5 class="modal-title text-white" id="modalPaiementTitre">Paiement</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body p-4">
            <!-- Récap commande -->
            <div class="mb-3 p-3" style="background:#f8f9fa; border-radius:10px;">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted" style="font-size:13px;">Billet</span>
                <strong id="recap-type">—</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted" style="font-size:13px;">Quantité</span>
                <strong id="recap-qty">—</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted" style="font-size:13px;">Total</span>
                <strong id="recap-total" style="font-size:17px; color:#00acac;">—</strong>
              </div>
            </div>

            <!-- Numéro de téléphone -->
            <div class="form-group">
              <label class="f-w-600" id="labelTel">Numéro Wave</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="font-weight:700;">+221</span>
                </div>
                <input type="tel" class="form-control" id="inputTel"
                       placeholder="77 XXX XX XX" maxlength="9"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
              </div>
              <small class="text-muted" id="helpTel">Entrez votre numéro Wave sans l'indicatif</small>
            </div>

            <!-- Message d'info paiement -->
            <div class="alert alert-info mb-3" id="alertPaiement" style="font-size:13px; border-radius:10px;">
              <i class="fa fa-info-circle mr-1"></i>
              Vous allez recevoir une notification sur votre téléphone pour confirmer le paiement.
            </div>
          </div>

          <div class="modal-footer" style="border:none; padding: 0 24px 20px;">
            <button type="button" class="btn btn-white btn-block mb-2" data-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-block text-white f-w-700" id="btnConfirmerPaiement"
                    style="border-radius:10px; font-size:15px; padding:12px;"
                    onclick="confirmerPaiement()">
              ✅ Confirmer le paiement
            </button>
          </div>

          <!-- Formulaire caché soumis après confirmation -->
          <form id="formCommande" action="/saticket/controller/CommandesController.php" method="POST" style="display:none;">
            <input type="hidden" name="id_utilisateurs" value="<?= $id_connecte; ?>">
            <input type="hidden" name="id_evenements"   id="hidden_id_evenements">
            <input type="hidden" name="id_tickets"      id="hidden_id_tickets">
            <input type="hidden" name="quantite"         id="hidden_quantite">
            <input type="hidden" name="methode_paiement" id="hidden_methode">
            <input type="hidden" name="telephone"        id="hidden_telephone">
            <input type="hidden" name="btnCommander"     value="1">
          </form>

        </div>
      </div>
    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <?php require_once __DIR__ . '/../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // ── Mettre à jour le prix total affiché sur la carte ──
    function updateTicketInfo(eventId) {
      var sel    = document.getElementById('ticket-select-' + eventId);
      var qtyEl  = document.getElementById('qty-' + eventId);
      var priceEl = document.getElementById('total-price-' + eventId);
      var stockEl = document.getElementById('stock-info-' + eventId);

      if (!sel) return;
      var opt   = sel.options[sel.selectedIndex];
      var prix  = parseInt(opt.dataset.prix) || 0;
      var dispo = parseInt(opt.dataset.dispo) || 1;

      qtyEl.max = dispo;
      if (parseInt(qtyEl.value) > dispo) qtyEl.value = dispo;

      var qty = parseInt(qtyEl.value) || 1;
      priceEl.textContent = formatMoney(prix * qty) + ' FCFA';
      stockEl.textContent = 'Stock : ' + dispo + ' billet' + (dispo > 1 ? 's' : '') + ' disponible' + (dispo > 1 ? 's' : '');
    }

    // Met à jour le total quand la quantité change
    document.addEventListener('input', function(e) {
      if (e.target && e.target.id && e.target.id.startsWith('qty-')) {
        var eventId = e.target.id.replace('qty-', '');
        updateTicketInfo(eventId);
      }
    });

    // ── Ouvrir la modale de paiement ──
    function ouvrirPaiement(eventId, methode) {
      var sel   = document.getElementById('ticket-select-' + eventId);
      var qtyEl = document.getElementById('qty-' + eventId);
      if (!sel || !qtyEl) return;

      var opt   = sel.options[sel.selectedIndex];
      var prix  = parseInt(opt.dataset.prix) || 0;
      var qty   = parseInt(qtyEl.value) || 1;
      var total = prix * qty;
      var type  = opt.dataset.type || opt.text;

      // Récap
      document.getElementById('recap-type').textContent  = type;
      document.getElementById('recap-qty').textContent   = qty;
      document.getElementById('recap-total').textContent = formatMoney(total) + ' FCFA';

      // Hidden form
      document.getElementById('hidden_id_evenements').value = eventId;
      document.getElementById('hidden_id_tickets').value    = sel.value;
      document.getElementById('hidden_quantite').value      = qty;
      document.getElementById('hidden_methode').value       = methode;

      // Couleur header + texte
      var header = document.getElementById('modalPaiementHeader');
      var titre  = document.getElementById('modalPaiementTitre');
      var label  = document.getElementById('labelTel');
      var help   = document.getElementById('helpTel');
      var btnConf = document.getElementById('btnConfirmerPaiement');
      var alert  = document.getElementById('alertPaiement');

      if (methode === 'wave') {
        header.style.background = 'linear-gradient(135deg,#0060df,#0090ff)';
        titre.textContent  = '💙 Paiement Wave';
        label.textContent  = 'Numéro Wave';
        help.textContent   = 'Entrez votre numéro Wave sans l\'indicatif';
        btnConf.style.background = 'linear-gradient(135deg,#0060df,#0090ff)';
        alert.innerHTML = '<i class="fa fa-info-circle mr-1"></i> Vous recevrez une notification Wave sur votre téléphone. Confirmez le paiement depuis l\'app Wave.';
      } else {
        header.style.background = 'linear-gradient(135deg,#ff6600,#ffaa00)';
        titre.textContent  = '🟠 Orange Money';
        label.textContent  = 'Numéro Orange Money';
        help.textContent   = 'Entrez votre numéro Orange sans l\'indicatif';
        btnConf.style.background = 'linear-gradient(135deg,#ff6600,#ffaa00)';
        alert.innerHTML = '<i class="fa fa-info-circle mr-1"></i> Vous recevrez un code USSD ou une notification Orange Money pour confirmer le paiement.';
      }

      document.getElementById('inputTel').value = '';
      $('#modalPaiement').modal('show');
    }

    // ── Confirmer et soumettre ──
    function confirmerPaiement() {
      var tel = document.getElementById('inputTel').value.replace(/\s/g,'');
      if (tel.length < 9) {
        Swal.fire({ icon:'warning', title:'Numéro invalide', text:'Veuillez entrer un numéro de téléphone valide (9 chiffres).', confirmButtonColor:'#0090ff' });
        return;
      }

      document.getElementById('hidden_telephone').value = '+221' + tel;

      $('#modalPaiement').modal('hide');

      Swal.fire({
        title: 'Traitement en cours…',
        html: '<p>Votre demande de paiement est envoyée.<br>Confirmez sur votre téléphone.</p>',
        icon: 'info',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
      }).then(function() {
        document.getElementById('formCommande').submit();
      });
    }

    // ── Recherche ──
    document.getElementById('searchInput').addEventListener('input', function() {
      var q = this.value.toLowerCase();
      document.querySelectorAll('.ev-item').forEach(function(el) {
        var titre = el.dataset.titre || '';
        var lieu  = el.dataset.lieu  || '';
        el.style.display = (titre.includes(q) || lieu.includes(q)) ? '' : 'none';
      });
    });

    function formatMoney(n) {
      return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
      Swal.fire({ title:'✅ Commande passée !', text:'<?= htmlspecialchars($_GET['messageSuccess']); ?>', icon:'success', confirmButtonColor:'#00acac' });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
      Swal.fire({ title:'Erreur', text:'<?= htmlspecialchars($_GET['messageError']); ?>', icon:'error', confirmButtonColor:'#ff5b57' });
    </script>
  <?php endif; ?>

</body>
</html>