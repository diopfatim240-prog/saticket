<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentRole = strtolower($_SESSION['role'] ?? '');
if ($currentRole !== 'admin') {
    header('Location: /saticket/admin.php?error=acces_refuse');
    exit();
}

require_once '../../../../model/CommentaireRepository.php';

$commentaireRepo = new CommentaireRepository();
try {
    $listeCommentaires = $commentaireRepo->getAllComments();
} catch (Exception $e) {
    $listeCommentaires = [];
    error_log("Erreur liste commentaires admin : " . $e->getMessage());
}


$totalNotes = array_filter(array_map(fn($c) => is_numeric($c['note']) ? (float)$c['note'] : null, $listeCommentaires));
$moyNotes   = count($totalNotes) > 0 ? round(array_sum($totalNotes) / count($totalNotes), 1) : '—';
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once '../../../sections/admin/head.php'; ?>
<style>
.com-badge-note {
    background: #fff8e1; border: 1px solid #ffc107;
    border-radius: 20px; padding: 3px 12px;
    font-size: 12px; font-weight: 700; color: #b07d00;
    display: inline-flex; align-items: center; gap: 4px;
}
.stars { color: #ffc107; font-size: 13px; letter-spacing: 1px; }
.filter-bar { max-width: 340px; }
.stat-box { background:#fff; border-radius:12px; padding:16px 20px;
            box-shadow:0 2px 12px rgba(0,0,0,.08); text-align:center; }
.stat-box .val { font-size:26px; font-weight:800; }
.stat-box .lbl { font-size:12px; color:#aaa; }
</style>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item active">Commentaires</li>
      </ol>

      <h1 class="page-header">💬 Tous les Commentaires <small>Vue administrateur — lecture seule</small></h1>

      <!-- Stats -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="stat-box">
            <div class="val" style="color:#1d3557;"><?= count($listeCommentaires); ?></div>
            <div class="lbl">Commentaires au total</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-box">
            <div class="val" style="color:#ffc107;"><?= $moyNotes; ?></div>
            <div class="lbl">Note moyenne / 10</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-box">
            <div class="val" style="color:#00acac;"><?= count(array_unique(array_column($listeCommentaires, 'evenement_titre'))); ?></div>
            <div class="lbl">Événements concernés</div>
          </div>
        </div>
      </div>

      <div class="panel panel-inverse">
        <div class="panel-heading d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
          <h4 class="panel-title">Liste des commentaires (<?= count($listeCommentaires); ?>)</h4>
          <!-- Filtre rapide -->
          <div class="input-group filter-bar">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher...">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
          </div>
        </div>

        <div class="panel-body">
          <?php if (empty($listeCommentaires)): ?>
            <div class="alert alert-info text-center">
              <i class="fa fa-comment-o mr-2"></i>Aucun commentaire publié pour l'instant.
            </div>
          <?php else: ?>
          <div class="table-responsive">
            <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="18%">Auteur</th>
                  <th width="18%">Événement</th>
                  <th>Avis</th>
                  <th width="10%" class="text-center">Note</th>
                  <th width="14%">Date</th>
                  <th width="8%" class="text-center">Suppr.</th>
                </tr>
              </thead>
              <tbody id="commentTable">
                <?php foreach ($listeCommentaires as $com):
                  $noteNum = is_numeric($com['note']) ? (int)$com['note'] : 0;
                  $stars   = str_repeat('★', min($noteNum, 10)) . str_repeat('☆', max(0, 10 - $noteNum));
                  $auteur  = trim(($com['user_prenom'] ?? '') . ' ' . ($com['user_nom'] ?? ''));
                  if (trim($auteur) === '') $auteur = $com['user_email'] ?? 'Inconnu';
                ?>
                <tr class="com-row"
                    data-search="<?= strtolower(htmlspecialchars($auteur . ' ' . ($com['evenement_titre'] ?? '') . ' ' . ($com['avis'] ?? ''))); ?>">
                  <td class="text-muted" style="font-size:11px;"><?= (int)$com['id']; ?></td>
                  <td>
                    <strong><?= htmlspecialchars($auteur); ?></strong>
                    <?php if (!empty($com['user_email'])): ?>
                      <br><small class="text-muted"><?= htmlspecialchars($com['user_email']); ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="text-primary f-w-600"><?= htmlspecialchars($com['evenement_titre'] ?? '—'); ?></span>
                  </td>
                  <td>
                    <span style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; max-width:320px;">
                      <?= htmlspecialchars($com['avis'] ?? ''); ?>
                    </span>
                    <?php if (strlen($com['avis'] ?? '') > 120): ?>
                      <a href="javascript:;" onclick="voirAvis('<?= addslashes(htmlspecialchars($auteur)); ?>','<?= addslashes(htmlspecialchars($com['avis'] ?? '')); ?>')" style="font-size:11px;">Lire plus</a>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <span class="com-badge-note"><i class="fa fa-star"></i> <?= htmlspecialchars((string)($com['note'] ?? '—')); ?>/10</span>
                    <div class="stars mt-1"><?= substr($stars, 0, 10); ?></div>
                  </td>
                  <td style="font-size:12px;">
                    <?= !empty($com['createdat']) ? date('d/m/Y<\b\r>H:i', strtotime($com['createdat'])) : '—'; ?>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-xs btn-danger" title="Supprimer définitivement"
                            onclick="adminDelete(<?= (int)$com['id']; ?>, '<?= addslashes($auteur); ?>')">
                      <i class="fa fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /content -->

    <!-- Modal lecture avis complet -->
    <div class="modal fade" id="modalAvis" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:14px; overflow:hidden;">
          <div class="modal-header bg-inverse text-white" style="border:none;">
            <h5 class="modal-title" id="avisAuteur">Avis complet</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p id="avisTexte" style="white-space:pre-line; font-size:14px; line-height:1.7;"></p>
          </div>
          <div class="modal-footer" style="border:none;">
            <button class="btn btn-white" data-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <?php require_once '../../../sections/admin/sectionConfig.php'; ?>
  </div>

  <?php require_once '../../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Recherche en direct
    document.getElementById('searchInput').addEventListener('input', function() {
      var q = this.value.toLowerCase();
      document.querySelectorAll('.com-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
      });
    });

    // Lire avis complet
    function voirAvis(auteur, avis) {
      document.getElementById('avisAuteur').textContent = 'Avis de ' + auteur;
      document.getElementById('avisTexte').textContent  = avis;
      $('#modalAvis').modal('show');
    }

    // Suppression admin
    function adminDelete(id, auteur) {
      Swal.fire({
        title: 'Supprimer le commentaire de ' + auteur + ' ?',
        text: 'Cette action est définitive.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff5b57',
        cancelButtonColor: '#f2f3f4',
        confirmButtonText: 'Supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
      }).then(r => {
        if (r.isConfirmed) {
          $.get('/saticket/controller/CommentaireController.php', { action: 'delete', id: id }, function(res) {
            if (String(res).trim() === 'success') {
              Swal.fire('Supprimé !', 'Le commentaire a été retiré.', 'success').then(() => location.reload());
            } else {
              Swal.fire('Erreur', 'Impossible de supprimer ce commentaire.', 'error');
            }
          });
        }
      });
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>Swal.fire({ title:'Succès', text:'<?= htmlspecialchars($_GET['messageSuccess']); ?>', icon:'success', confirmButtonColor:'#00acac' });</script>
  <?php endif; ?>
</body>
</html>