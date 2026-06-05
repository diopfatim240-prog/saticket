<?php
// 1. Gestion de la session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusion du modèle (Laissé inchangé à 4 niveaux)
require_once '../../../../model/PromotionsRepository.php';

try {
    $promotionsRepository = new PromotionsRepository();
    $listePromotions = $promotionsRepository->getAll();
} catch (Exception $e) {
    $listePromotions = null;
    error_log("Erreur de chargement des promotions : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>SA Ticket | Gestion des Promotions</title>
    
    <?php 
    // On utilise le chemin physique réel du serveur pour inclure le fichier PHP
    require_once $_SERVER['DOCUMENT_ROOT'] . '/saticket/view/sections/admin/head.php'; 
    ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
</head>

<body>
  <div id="page-loader" class="fade show">
    <span class="spinner"></span>
  </div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/saticket/view/sections/admin/menuHaut.php'; ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/saticket/view/sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Promotions</a></li>
        <li class="breadcrumb-item active">Liste</li>
      </ol>

      <h1 class="page-header">Gestion des Promotions <small>Codes de réduction et offres spéciales</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Liste des Codes Promo</h4>
          <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
          </div>
        </div>
        
        <div class="panel-body">
          <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddPromoModal()">
                <i class="fa fa-plus mr-2"></i> Nouveau Code Promo
            </button>
          </div>

          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="1%"><input type="checkbox" id="check-all" /></th>
                <th width="15%">ID</th>
                <th width="25%">Code</th>
                <th width="25%">Réduction</th>
                <th width="20%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($listePromotions !== null && count($listePromotions) > 0): ?>
                <?php foreach ($listePromotions as $promo): ?>
                  <tr>
                    <td><input type="checkbox" value="<?= $promo['id']; ?>" /></td>
                    <td>PRM-<?= htmlspecialchars($promo['id']); ?></td>
                    <td><b class="text-primary"><?= htmlspecialchars($promo['code']); ?></b></td>
                    <td><span class="label label-success"><?= htmlspecialchars($promo['reduction']); ?>%</span></td>
                    <td>
                      <button class="btn btn-xs btn-primary" 
                              onclick="openEditPromoModal('<?= $promo['id']; ?>', '<?= addslashes($promo['code']); ?>', '<?= $promo['reduction']; ?>')">
                          <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-xs btn-danger" 
                              onclick="confirmDeletePromo('<?= $promo['id']; ?>', '<?= addslashes($promo['code']); ?>')">
                          <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun code promo trouvé dans la base de données.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalPromo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title" id="modalPromoTitle">Nouvelle Promotion</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="promoForm" action="/saticket/controller/PromotionsController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="promoId" name="id">
                        
                        <div class="form-group">
                            <label class="f-w-600">Code Promotionnel</label>
                            <input type="text" class="form-control" id="promoCode" name="code" placeholder="Ex: ETE2024" required style="text-transform: uppercase;">
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Réduction (%)</label>
                            <input type="number" class="form-control" id="promoReduction" name="reduction" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="btnSave" name="btnAddPromo">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/saticket/view/sections/admin/sectionConfig.php'; ?>
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/saticket/view/sections/admin/script.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function openAddPromoModal() {
        $('#modalPromoTitle').text('Créer une promotion');
        $('#promoId').val('');
        $('#promoCode').val('');
        $('#promoReduction').val('');
        $('#btnSave').attr('name', 'btnAddPromo'); 
        $('#modalPromo').modal('show');
    }

    function openEditPromoModal(id, code, reduction) {
        $('#modalPromoTitle').text('Modifier la promotion');
        $('#promoId').val(id);
        $('#promoCode').val(code);
        $('#promoReduction').val(reduction);
        $('#btnSave').attr('name', 'btnUpdatePromo'); 
        $('#modalPromo').modal('show');
    }

    function confirmDeletePromo(id, code) {
        Swal.fire({
            title: 'Supprimer ce code ?',
            text: "Le code promo " + code + " sera définitivement supprimé.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5b57',
            cancelButtonColor: '#f2f3f4',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/saticket/controller/PromotionsController.php?delete=" + id;
            }
        });
    }

    $('#check-all').on('change', function() {
        $('tbody input[type="checkbox"]').prop('checked', this.checked);
    });
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
        Swal.fire({
            title: "<?= htmlspecialchars($_GET['title'] ?? 'Succès'); ?>",
            text: "<?= htmlspecialchars($_GET['messageSuccess']); ?>",
            icon: "success",
            confirmButtonColor: "#00acac"
        });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
        Swal.fire({
            title: "<?= htmlspecialchars($_GET['title'] ?? 'Erreur'); ?>",
            text: "<?= htmlspecialchars($_GET['messageError']); ?>",
            icon: "error",
            confirmButtonColor: "#ff5b57"
        });
    </script>
  <?php endif; ?>
</body>
</html>