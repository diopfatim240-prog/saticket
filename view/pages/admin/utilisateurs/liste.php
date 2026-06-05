<?php 
// 1. Gestion de la session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentRole = strtolower($_SESSION['role'] ?? 'user');
if ($currentRole !== 'admin') {
    header('Location: /saticket/admin.php?error=1&message=' . urlencode('Accès refusé.')); 
    exit(); 
}

// 2. Inclusion du modèle (Laissé inchangé à 4 niveaux)
require_once '../../../../model/UtilisateurRepository.php';

$filterEtat = isset($_GET['etat']) && ($_GET['etat'] === '0' || $_GET['etat'] === '1') ? (int)$_GET['etat'] : 1; 

try {
    $utilisateurRepository = new UtilisateurRepository();
    $listeUtilisateurs = $utilisateurRepository->getAll($filterEtat);
} catch (Exception $e) {
    $listeUtilisateurs = null;
    error_log("Erreur de chargement de la liste : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once '../../../sections/admin/head.php'; ?>
<link class="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">

<style>
    .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #00acac;
        background-color: #00acac;
    }
</style>

<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once '../../../sections/admin/menuHaut.php'; ?>
    <?php require_once '../../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Utilisateurs</a></li>
        <li class="breadcrumb-item active">Liste</li>
      </ol>

      <h1 class="page-header">Gestion des Utilisateurs <small>Liste et contrôle d'accès</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading">
          <h4 class="panel-title">Liste des Utilisateurs</h4>
        </div>
        
      <div class="panel-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
            
            </div>
            <div class="d-flex gap-2">
              <a class="btn btn-sm btn-info" href="?etat=1" <?= $filterEtat === 1 ? 'style="color:white"' : '' ?>>Actifs</a>
              <a class="btn btn-sm btn-secondary" href="?etat=0" <?= $filterEtat === 0 ? 'style="color:white"' : '' ?>>Inactifs</a>
              <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fa fa-plus mr-2"></i> Ajouter un utilisateur
              </button>
            </div>
          </div>


          <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
            <thead>
              <tr>
                <th width="8%">ID</th>
                <th>Utilisateur</th>
                <th>Rôle</th>
               <td>
                 <th width="20%">État</th>
                <?php if ($listeUtilisateurs !== null && count($listeUtilisateurs) > 0): ?>
                <?php foreach ($listeUtilisateurs as $user): ?>
                    <button onclick="openEditModal('<?= htmlspecialchars($user['id'] ?? ''); ?>', ...)">
                <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
                </td>
                </tr>
            </thead>
            <tbody>
              <?php if ($listeUtilisateurs !== null && count($listeUtilisateurs) > 0): ?>
                <?php foreach ($listeUtilisateurs as $user): ?>
                    
                  <tr>
                    <td class="f-w-600 text-inverse">USR-<?= htmlspecialchars($user['id']); ?></td>
                    <td>
                        <strong><?= htmlspecialchars($user['nom']); ?></strong><br/>
                        <small class="text-muted"><?= htmlspecialchars($user['email']); ?></small>
                    </td>
                    <td>
                        <span class="label <?= $user['role'] === 'Admin' ? 'label-info' : 'label-warning'; ?>">
                            <?= htmlspecialchars($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <?php $isActive = isset($user['etat']) && (int)$user['etat'] === 1; ?>
                        <td>
                        <?php 
                        // On vérifie l'état actuel
                        $isActive = (int)$user['etat'] === 1;
                        ?>
                        
                        <button class="btn btn-xs <?= $isActive ? 'btn-warning' : 'btn-success' ?>" 
                                onclick="toggleStatus('<?= $user['id']; ?>', <?= $isActive ? 'true' : 'false' ?>)">
                            <?= $isActive ? 'Désactiver' : 'Activer' ?>
                        </button>
                    </td>
                      
                    </td>

                    <td>
                      <button class="btn btn-xs btn-primary" 
                              onclick="openEditModal('<?= $user['id']; ?>', '<?= addslashes($user['nom']); ?>', '<?= addslashes($user['email']); ?>', '<?= $user['role']; ?>', '<?= htmlspecialchars($user['telephone'] ?? ''); ?>', '<?= (int)($user['etat'] ?? 1); ?>')">
                          <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-xs btn-danger" 
                              onclick="deleteConfirm('<?= $user['id']; ?>', '<?= addslashes($user['nom']); ?>')">
                          <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun utilisateur actif trouvé.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title" id="modalTitle">Nouvel Utilisateur</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                
                <form action="/saticket/controller/UtilisateursController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="userId" name="id">
                        
                        <div class="form-group">
                            <label class="f-w-600">Nom Complet</label>
                            <input type="text" class="form-control" id="userName" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Adresse Email</label>
                            <input type="email" class="form-control" id="userEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Téléphone</label>
                            <input type="text" class="form-control" id="userPhone" name="telephone">
                        </div>
                        <div class="form-group">
                            <label class="f-w-600">Rôle</label>
                            <select class="form-control" id="userRole" name="role">
                                <option value="organisateur">Organisateur</option>
                                <option value="acheteur">Acheteur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="f-w-600">Statut</label>
                            <select class="form-control" id="userEtat" name="etat">
                                <option value="0">Inactif</option>
                                <option value="1">Actif</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="btnSave" name="btnUpdateUser">Enregistrer</button>
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
    function openAddModal() {
        $('#modalTitle').text('Ajouter un nouvel utilisateur');
        $('#userId').val('');
        $('#userName').val('');
        $('#userEmail').val('');
        $('#userPhone').val('');
        $('#userRole').val('user');
        $('#userEtat').val('1');
        $('#btnSave').attr('name', 'btnAddUser'); 
        $('#modalUser').modal('show');
    }

    function openEditModal(id, nom, email, role, telephone, etat) {
        $('#modalTitle').text('Modifier : ' + nom);
        $('#userId').val(id);
        $('#userName').val(nom);
        $('#userEmail').val(email);
        $('#userPhone').val(telephone);
        $('#userRole').val(role);
        $('#userEtat').val(etat);
        $('#btnSave').attr('name', 'btnUpdateUser'); 
        $('#modalUser').modal('show');
    }


    // ✅ Suppression avec redirection vers l'URL absolue
    function deleteConfirm(id, name) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "L'utilisateur " + name + " sera définitivement supprimé.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5b57',
            cancelButtonColor: '#f2f3f4',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/saticket/controller/UtilisateursController.php?delete=" + id;
            }
        });
    }

    // ✅ Activation / Désactivation avec redirection vers l'URL absolue
    // isActive = statut actuel de l'utilisateur (etat=1 => actif)
    function toggleStatus(id, isActive) {
        // Si l'utilisateur est actif => on le désactive
        // Si l'utilisateur est inactif => on le réactive
        const url = isActive
            ? "/saticket/controller/UtilisateursController.php?desactivate=" + id
            : "/saticket/controller/UtilisateursController.php?activate=" + id;
        window.location.href = url;
    }

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