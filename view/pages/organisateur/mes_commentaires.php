<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_connecte = 0;
if (isset($_SESSION['id_utilisateurs'])) {
    $id_connecte = (int)$_SESSION['id_utilisateurs'];
} elseif (isset($_SESSION['id'])) {
    $id_connecte = (int)$_SESSION['id'];
} elseif (isset($_SESSION['user_id'])) {
    $id_connecte = (int)$_SESSION['user_id'];
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

require_once __DIR__ . '/../../../model/CommentaireRepository.php';

$commentaireRepo   = new CommentaireRepository();
$listeCommentaires = [];
try {
    $listeCommentaires = $commentaireRepo->getAllCommentsByUser($id_connecte);
} catch (Exception $e) {
    error_log("Erreur chargement commentaires: " . $e->getMessage());
}

$notes = array_filter(array_map(fn($c) => is_numeric($c['note']) ? (float)$c['note'] : null, $listeCommentaires));
$moy   = count($notes) > 0 ? round(array_sum($notes) / count($notes), 1) : '—';
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/../../sections/admin/head.php'; ?>
<style>
.com-card {
    background:#fff; border-radius:14px;
    box-shadow:0 2px 16px rgba(0,0,0,.08);
    margin-bottom:16px; padding:18px 22px;
    border-left:5px solid #00acac;
    transition:box-shadow .2s, transform .2s;
}
.com-card:hover { box-shadow:0 6px 26px rgba(0,0,0,.13); transform:translateY(-2px); }
.com-event  { font-size:13px; color:#457b9d; font-weight:700; margin-bottom:4px; }
.com-avis   { font-size:14px; color:#333; margin:8px 0; line-height:1.6; }
.com-note   { display:inline-flex; align-items:center; gap:4px; background:#fff8e1;
              border:1px solid #ffc107; border-radius:20px; padding:2px 12px;
              font-size:12px; font-weight:700; color:#b07d00; }
.com-date   { font-size:11px; color:#aaa; }
.com-actions { display:flex; gap:6px; margin-top:12px; }
.empty-state { text-align:center; padding:50px 20px; }
.empty-state i { font-size:60px; color:#dee2e6; display:block; margin-bottom:16px; }
</style>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once __DIR__ . '/../../sections/admin/menuHaut.php'; ?>
    <?php require_once __DIR__ . '/../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item active">Mes Commentaires</li>
      </ol>

      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:10px;">
        <div>
          <h1 class="page-header mb-0">💬 Mes Commentaires</h1>
          <p class="text-muted mt-1 mb-0">Vos avis publiés</p>
        </div>
        <button class="btn btn-primary" onclick="$('#modalAddComment').modal('show')">
          <i class="fa fa-plus mr-1"></i> Nouveau commentaire
        </button>
      </div>

      <!-- Stats -->
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <div class="panel panel-inverse mb-0" style="border-radius:12px;">
            <div class="panel-body text-center py-3">
              <div style="font-size:28px; font-weight:800; color:#00acac;"><?php echo count($listeCommentaires); ?></div>
              <div class="text-muted" style="font-size:13px;">Commentaires publiés</div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="panel panel-inverse mb-0" style="border-radius:12px;">
            <div class="panel-body text-center py-3">
              <div style="font-size:28px; font-weight:800; color:#ffc107;"><?php echo $moy; ?></div>
              <div class="text-muted" style="font-size:13px;">Note moyenne</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste -->
      <?php if (empty($listeCommentaires)): ?>
        <div class="panel panel-inverse">
          <div class="panel-body empty-state">
            <i class="fa fa-comment-o"></i>
            <h4 class="text-muted">Aucun commentaire publié.</h4>
            <p class="text-muted mb-4">Partagez votre avis !</p>
            <button class="btn btn-primary" onclick="$('#modalAddComment').modal('show')">
              <i class="fa fa-plus mr-1"></i> Écrire un commentaire
            </button>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($listeCommentaires as $com):
          $note    = $com['note'] ?? '—';
          $noteNum = is_numeric($note) ? (int)$note : 0;
          $stars   = str_repeat('★', min($noteNum, 10)) . str_repeat('☆', max(0, 10 - $noteNum));
        ?>
        <div class="com-card">
          <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:10px;">
            <div style="flex:1; min-width:200px;">
              <?php if (!empty($com['evenement_titre'])): ?>
                <div class="com-event"><i class="fa fa-calendar mr-1"></i><?php echo htmlspecialchars($com['evenement_titre']); ?></div>
              <?php endif; ?>
              <div class="com-avis"><?php echo nl2br(htmlspecialchars($com['avis'] ?? '')); ?></div>
              <div class="d-flex align-items-center flex-wrap mt-1" style="gap:10px;">
                <span class="com-note"><i class="fa fa-star mr-1"></i><?php echo htmlspecialchars((string)$note); ?>/10</span>
                <span style="color:#ffc107; font-size:14px;"><?php echo $stars; ?></span>
                <span class="com-date"><i class="fa fa-clock-o mr-1"></i><?php echo !empty($com['createdat']) ? date('d/m/Y à H:i', strtotime($com['createdat'])) : ''; ?></span>
              </div>
            </div>
            <div class="com-actions">
              <button class="btn btn-xs btn-primary"
                onclick="openEdit(<?php echo (int)$com['id']; ?>, '<?php echo htmlspecialchars((string)$note, ENT_QUOTES); ?>', '<?php echo addslashes(htmlspecialchars($com['avis'] ?? '', ENT_QUOTES)); ?>')">
                <i class="fa fa-edit mr-1"></i> Modifier
              </button>
              <button class="btn btn-xs btn-danger" onclick="confirmDelete(<?php echo (int)$com['id']; ?>)">
                <i class="fa fa-trash mr-1"></i> Supprimer
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- ══ MODAL AJOUTER ══ -->
    <div class="modal fade" id="modalAddComment" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">
          <div class="modal-header bg-inverse text-white" style="border:none;">
            <h5 class="modal-title"><i class="fa fa-comment mr-2"></i>Nouveau commentaire</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <form action="/saticket/controller/CommentaireController.php" method="POST">
            <div class="modal-body">
              <div class="form-group">
                <label class="f-w-600">Note (sur 10)</label>
                <div class="d-flex align-items-center" style="gap:12px;">
                  <input type="range" class="form-control-range" name="note" id="noteRange"
                         min="1" max="10" value="5" step="1"
                         oninput="document.getElementById('noteDisplay').textContent=this.value+'/10'"
                         style="flex:1;">
                  <span id="noteDisplay" style="font-weight:700; color:#ffc107; min-width:40px; font-size:16px;">5/10</span>
                </div>
              </div>
              <div class="form-group">
                <label class="f-w-600">Votre avis</label>
                <textarea class="form-control" name="avis" rows="5"
                          placeholder="Partagez votre expérience..." required></textarea>
              </div>
            </div>
            <div class="modal-footer" style="border:none;">
              <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-primary" name="btnAddComment">
                <i class="fa fa-paper-plane mr-1"></i> Publier
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ══ MODAL MODIFIER ══ -->
    <div class="modal fade" id="modalEditComment" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">
          <div class="modal-header text-white" style="background:linear-gradient(135deg,#1d3557,#457b9d); border:none;">
            <h5 class="modal-title"><i class="fa fa-edit mr-2"></i>Modifier le commentaire</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editCommentId">
            <div class="form-group">
              <label class="f-w-600">Note (sur 10)</label>
              <div class="d-flex align-items-center" style="gap:12px;">
                <input type="range" class="form-control-range" id="editNoteRange"
                       min="1" max="10" value="5" step="1"
                       oninput="document.getElementById('editNoteDisplay').textContent=this.value+'/10'"
                       style="flex:1;">
                <span id="editNoteDisplay" style="font-weight:700; color:#ffc107; min-width:40px; font-size:16px;">5/10</span>
              </div>
            </div>
            <div class="form-group">
              <label class="f-w-600">Votre avis</label>
              <textarea class="form-control" id="editAvis" rows="5" placeholder="Votre avis..."></textarea>
            </div>
          </div>
          <div class="modal-footer" style="border:none;">
            <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary" onclick="submitEdit()">
              <i class="fa fa-save mr-1"></i> Enregistrer
            </button>
          </div>
        </div>
      </div>
    </div>

    <?php require_once __DIR__ . '/../../sections/admin/sectionConfig.php'; ?>
  </div>

  <?php require_once __DIR__ . '/../../sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function openEdit(id, note, avis) {
      var noteVal = parseInt(note) || 5;
      $('#editCommentId').val(id);
      $('#editNoteRange').val(noteVal);
      $('#editNoteDisplay').text(noteVal + '/10');
      $('#editAvis').val(avis);
      $('#modalEditComment').modal('show');
    }

    function submitEdit() {
      var id   = $('#editCommentId').val();
      var note = $('#editNoteRange').val();
      var avis = $('#editAvis').val().trim();
      if (!avis) { Swal.fire('Attention', "L'avis ne peut pas être vide.", 'warning'); return; }
      $.ajax({
        url: '/saticket/controller/CommentaireController.php',
        type: 'POST',
        data: { action: 'update', id: id, note: note, avis: avis },
        success: function(res) {
          if (String(res).trim() === 'success') {
            Swal.fire('Modifié !', 'Votre commentaire a été mis à jour.', 'success')
              .then(() => location.reload());
          } else {
            Swal.fire('Erreur', 'Impossible de modifier ce commentaire.', 'error');
          }
        },
        error: function() { Swal.fire('Erreur', 'Connexion impossible.', 'error'); }
      });
    }

    function confirmDelete(id) {
      Swal.fire({
        title: 'Supprimer ce commentaire ?',
        text: 'Cette action est irréversible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff5b57',
        cancelButtonColor: '#f2f3f4',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
      }).then(r => {
        if (r.isConfirmed) {
          $.get('/saticket/controller/CommentaireController.php', { action: 'delete', id: id }, function(res) {
            if (String(res).trim() === 'success') {
              Swal.fire('Supprimé !', 'Le commentaire a été retiré.', 'success')
                .then(() => location.reload());
            } else {
              Swal.fire('Erreur', 'Impossible de supprimer ce commentaire.', 'error');
            }
          });
        }
      });
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>Swal.fire({ title:'✅ Succès', text:'<?php echo htmlspecialchars($_GET['messageSuccess']); ?>', icon:'success', confirmButtonColor:'#00acac' });</script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>Swal.fire({ title:'Erreur', text:'<?php echo htmlspecialchars($_GET['messageError']); ?>', icon:'error', confirmButtonColor:'#ff5b57' });</script>
  <?php endif; ?>
</body>
</html>