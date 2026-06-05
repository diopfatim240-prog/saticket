<?php
// 1. Gestion de la session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusion du modèle de paiement (Vérifie le chemin vers ton dossier model)
require_once '../../../../model/PaiementRepository.php';

try {
    $paiementRepository = new PaiementRepository();
    $currentUserId = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_utilisateurs'] ?? null;
    $currentRole = strtolower($_SESSION['role'] ?? 'user');
    if ($currentRole !== 'admin' && $currentUserId) {
        $transactions = $paiementRepository->getAllTransactionsByUser((int)$currentUserId);
    } else {
        $transactions = $paiementRepository->getAllTransactions();
    }
} catch (Exception $e) {
    $transactions = [];
    error_log("Erreur de chargement de la liste des paiements : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once '../../../sections/admin/head.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">

<body>
  <div id="page-loader" class="fade show">
    <span class="spinner"></span>
  </div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Paiements</a></li>
        <li class="breadcrumb-item active">Liste</li>
      </ol>

      <h1 class="page-header">Gestion des Paiements <small>Suivi des revenus et transactions</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Historique des Transactions (Base de données)</h4>
          <div class="panel-heading-btn">
            <button class="btn btn-xs btn-primary" type="button" onclick="openAddPaymentModal()">
              <i class="fa fa-plus mr-1"></i> Ajouter un paiement
            </button>
          </div>
        </div>

        <div class="panel-body">
          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="1%">N° Trans</th>
                <th>Client</th>
                <th>Réf Commande</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th>Statut</th>
                <th>Date de Création</th>
                <th width="10%">Actions</th>
              </tr>
            </thead>
            <tbody>

              <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $tr): ?>
                  <tr>
                    <td class="f-w-600 text-inverse">PAY-0<?= $tr['id']; ?></td>
                    
                    <td>
                        <b><?= htmlspecialchars(($tr['prenom'] ?? 'Inconnu') . ' ' . ($tr['nom'] ?? 'Client')); ?></b>
                    </td>
                    
                    <td>
                        <span class="label label-inverse">CMD #<?= htmlspecialchars($tr['id_commande'] ?? 'N/A'); ?></span>
                    </td>
                    
                    <td class="f-w-700 text-success">
                        <?= number_format($tr['montant'] ?? 0, 0, ',', ' '); ?> FCFA
                    </td>
                    
                    <td>
                        <span class="badge badge-primary"><?= htmlspecialchars($tr['type'] ?? 'Non spécifié'); ?></span>
                    </td>
                    
                    <td>
                      <?php if (($tr['statut'] ?? '') === 'Réussi' || ($tr['statut'] ?? '') === 'Success' || ($tr['statut'] ?? '') === 'Validated'): ?>
                        <span class="badge badge-success"><i class="fa fa-check-circle"></i> Réussi</span>
                      <?php else: ?>
                        <span class="badge badge-danger"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($tr['statut'] ?? 'Échoué'); ?></span>
                      <?php endif; ?>
                    </td>
                    
                    <td>
                        <?= !empty($tr['createdat']) ? date('d/m/Y H:i', strtotime($tr['createdat'])) : '---'; ?>
                    </td>
                    
                    <td>
                      <button class="btn btn-xs btn-info" onclick="viewTransaction('PAY-0<?= $tr['id']; ?>', '<?= htmlspecialchars(($tr['prenom'] ?? '') . ' ' . ($tr['nom'] ?? ''), ENT_QUOTES); ?>', '<?= $tr['montant'] ?? 0; ?>', '<?= htmlspecialchars($tr['type'] ?? '', ENT_QUOTES); ?>')">
                        <i class="fa fa-search"></i> Détails
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">
                    Aucun paiement trouvé dans la base de données.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalTransaction" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title">Détails de la Transaction</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="transactionContent">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT PAIEMENT MANUEL -->
    <div class="modal fade" id="modalAddPayment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title">Ajouter un paiement (manuel)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form action="/saticket/controller/PaiementController.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="f-w-600">ID Commande</label>
                            <input type="number" class="form-control" name="id_commande" id="paymentIdCommande" required min="1">
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Méthode</label>
                            <select class="form-control" name="type" id="paymentType">
                                <option value="Wave">Wave</option>
                                <option value="OrangeMoney">OrangeMoney</option>
                                <option value="MoovMoney">MoovMoney</option>
                                <option value="Carte">Carte</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Statut</label>
                            <select class="form-control" name="statut" id="paymentStatut">
                                <option value="Réussi">Réussi</option>
                                <option value="Échoué">Échoué</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" name="btnSavePayment" value="1">Enregistrer</button>
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
    // --- POPUP DES DÉTAILS (MODAL) ---
    function viewTransaction(id, client, montant, methode) {
        let html = `
            <table class="table table-profile">
                <tr><td class="field" style="width:30%">Référence</td><td><b>${id}</b></td></tr>
                <tr><td class="field">Acheteur</td><td>${client ? client : 'Données non liées'}</td></tr>
                <tr><td class="field">Total réglé</td><td><b>${new Intl.NumberFormat('fr-FR').format(montant)} FCFA</b></td></tr>
                <tr><td class="field">Opérateur</td><td><span class="badge badge-primary">${methode}</span></td></tr>
            </table>
        `;
        $('#transactionContent').html(html);
        $('#modalTransaction').modal('show');
    }

    // --- OUVRIR MODAL AJOUT PAIEMENT ---
    function openAddPaymentModal() {
        $('#modalAddPayment').modal('show');
        $('#paymentIdCommande').val('');
        $('#paymentType').val('Wave');
        $('#paymentStatut').val('Réussi');
    }
  </script>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
        Swal.fire({
            title: "Opération réussie",
            text: "<?= htmlspecialchars($_GET['messageSuccess']); ?>",
            icon: "success",
            confirmButtonColor: "#00acac"
        });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
        Swal.fire({
            title: "Erreur détectée",
            text: "<?= htmlspecialchars($_GET['messageError']); ?>",
            icon: "error",
            confirmButtonColor: "#ff5b57"
        });
    </script>
  <?php endif; ?>
</body>
</html>