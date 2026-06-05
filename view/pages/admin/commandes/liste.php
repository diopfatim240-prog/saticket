<?php
// Sécurité / Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Accepter plusieurs schémas de session
$id_connecte = 0;
if (isset($_SESSION['id_utilisateurs'])) {
    $id_connecte = (int)$_SESSION['id_utilisateurs'];
} elseif (isset($_SESSION['id'])) {
    $id_connecte = (int)$_SESSION['id'];
}

// Nettoyage du rôle : on passe en minuscule et on enlève les espaces inutiles
$role_actuel = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';

// 1. Vérification connexion : rediriger vers le login si non connecté
if ($id_connecte === 0 && empty($_SESSION['nom'])) {
    header("Location: /saticket/login.php");
    exit();
}

// 2. Vérification accès : autorisé si admin ou organisateur
// Note : Si tu as un doute, tu peux décommenter la ligne suivante pour afficher ton rôle réel à l'écran
// die("Rôle détecté dans la session : " . $role_actuel);

if ($role_actuel !== 'organisateur' && $role_actuel !== 'admin') {
    // Si la redirection boucle, change l'URL de destination ici
    header("Location: /saticket/admin.php?error=acces_refuse");
    exit();
}

// Inclusion robuste du repository
require_once __DIR__ . '/../../../../model/CommandesRepository.php';

$commandeRepo = new CommandesRepository();

// Filtrage : l'admin voit tout, l'organisateur voit les commandes de ses événements
if ($role_actuel === 'admin') {
    $listeCommandes = $commandeRepo->getAllOrders();
} elseif ($role_actuel === 'organisateur') {
    $listeCommandes = $commandeRepo->getOrdersByOrganisateur($id_connecte);
} else {
    $listeCommandes = $commandeRepo->getOrdersByUser($id_connecte);
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once '../../../sections/admin/head.php'; ?>

<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <h1 class="page-header">
        Gestion des Commandes 
        <small><?= ($role_actuel === 'admin') ? 'Toutes les ventes' : 'Mes réservations'; ?></small>
      </h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Liste des commandes (Trouvées : <?= count($listeCommandes ?? []); ?>)</h4>
          <?php if ($role_actuel === 'admin'): ?>
            <div class="panel-heading-btn">
              <button class="btn btn-xs btn-primary" type="button" onclick="openAddCommandeModal()">
                <i class="fa fa-plus mr-1"></i> Ajouter une commande
              </button>
            </div>
          <?php endif; ?>
        </div>
        <div class="panel-body">
          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="1%">ID</th>
                <th>Référence</th>
                <th>Événement</th>
                <th>Acheteur</th>
                <th>Quantité</th>
                <th>Montant Total</th>
                <th>Date de commande</th>
              <th width="1%">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($listeCommandes)): ?>
                <?php foreach ($listeCommandes as $commande): ?>
                  <tr>
                    <td><?= $commande['id']; ?></td>
                    <td><span class="label label-inverse"><?= htmlspecialchars($commande['reference'] ?? 'MANUEL'); ?></span></td>
                    <td><b><?= htmlspecialchars($commande['evenement_titre'] ?? 'N/A'); ?></b></td>
                    <td><?= htmlspecialchars($commande['client_nom'] ?? 'Inconnu'); ?></td>
                    <td class="text-center"><?= (int)($commande['quantite'] ?? 0); ?></td>
                    <td class="text-success f-w-700"><?= number_format($commande['montant'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                    <td><?= !empty($commande['createdat']) ? date('d/m/Y H:i', strtotime($commande['createdat'])) : 'Non définie'; ?></td>
                    <td>
                      <button class="btn btn-xs btn-info" title="Détails" disabled><i class="fa fa-eye"></i></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">Aucune commande trouvée dans le système.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <?php require_once '../../../sections/admin/script.php'; ?>

  <!-- MODAL AJOUT COMMANDE (Admin) -->
  <div class="modal fade" id="modalAddCommande" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0">
        <div class="modal-header bg-inverse text-white">
          <h5 class="modal-title">Ajouter une commande</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="/saticket/controller/CommandesController.php" method="POST">
          <div class="modal-body">
            <div class="form-group">
              <label class="f-w-600">Utilisateur (id)</label>
              <input type="number" class="form-control" name="id_utilisateurs" required min="1">
            </div>

            <div class="form-group">
              <label class="f-w-600">Événement (id)</label>
              <input type="number" class="form-control" name="id_evenements" required min="1">
            </div>

            <div class="form-group">
              <label class="f-w-600">Ticket (id)</label>
              <input type="number" class="form-control" name="id_tickets" required min="1">
            </div>

            <div class="form-group">
              <label class="f-w-600">Quantité</label>
              <input type="number" class="form-control" name="quantite" required min="1">
            </div>

            <div class="form-group">
              <label class="f-w-600">Montant (FCFA)</label>
              <input type="number" class="form-control" name="montant" required min="0">
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" name="btnCommander" value="1">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if (isset($_GET['messageSuccess'])): ?>
    <script>
      Swal.fire({
        title: 'Succès !',
        text: '<?= htmlspecialchars($_GET['messageSuccess']); ?>',
        icon: 'success',
        confirmButtonColor: '#00acac'
      });
    </script>
  <?php elseif (isset($_GET['messageError'])): ?>
    <script>
      Swal.fire({
        title: 'Erreur !',
        text: '<?= htmlspecialchars($_GET['messageError']); ?>',
        icon: 'error',
        confirmButtonColor: '#ff5b57'
      });
    </script>
  <?php endif; ?>

  <script>
    function openAddCommandeModal() {
      $('#modalAddCommande').modal('show');
    }
  </script>
</body>
</html>

