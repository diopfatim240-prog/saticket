<!DOCTYPE html>
<html lang="fr">
<!-- ================== SECTION HEAD ================== -->
<?php require_once '../../sections/admin/head.php'; ?>

<body>
  <div id="page-loader" class="fade show">
    <span class="spinner"></span>
  </div>

  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <!-- ================== MENU HAUT ================== -->
    <?php require_once '../../sections/admin/menuHaut.php'; ?>
    <!-- ================== MENU GAUCHE ================== -->
    <?php require_once '../../sections/admin/menuGauche.php'; ?>

    <!-- ================== BASE CONTENT ================== -->
    <div id="content" class="content">
      <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item active">Mon Profil</li>
      </ol>

      <h1 class="page-header">Mon Profil <small>Informations de votre compte</small></h1>

      <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
          <div class="panel panel-inverse">
            <div class="panel-heading">
              <h4 class="panel-title">Informations Personnelles</h4>
            </div>
            <div class="panel-body text-center pt-4">
              <img src="../assets/img/user/user-13.jpg" alt="Avatar" class="img-circle" style="width: 120px; height: 120px; border: 3px solid #00acac;">
              <h5 class="mt-3 mb-1"><?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : 'Utilisateur'; ?></h5>
              <p class="text-muted mb-0"><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?></p>
              <div class="mt-3">
                <span class="label label-success">Administrateur</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Details -->
        <div class="col-lg-8">
          <div class="panel panel-inverse">
            <div class="panel-heading">
              <h4 class="panel-title">Détails du Compte</h4>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>ID Utilisateur</label>
                    <input type="text" class="form-control" value="<?php echo isset($_SESSION['id']) ? htmlspecialchars($_SESSION['id']) : ''; ?>" disabled>
                  </div>
                  <div class="form-group">
                    <label>Nom Complet</label>
                    <input type="text" class="form-control" value="<?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : ''; ?>" disabled>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Adresse Email</label>
                    <input type="email" class="form-control" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" disabled>
                  </div>
                  <div class="form-group">
                    <label>Rôle</label>
                    <input type="text" class="form-control" value="Administrateur" disabled>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Date Actuelle</label>
                <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i'); ?>" disabled>
              </div>
              <div class="mt-3">
                <button class="btn btn-primary">
                  <i class="fa fa-edit mr-2"></i> Éditer le Profil
                </button>
                <button class="btn btn-warning">
                  <i class="fa fa-key mr-2"></i> Changer le Mot de Passe
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================== SECTION CONFIG================== -->
    <?php require_once '../../sections/admin/sectionConfig.php'; ?>

    <!-- ================== SECTION SCROLL TO TOP ================== -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <!-- ================== SECTION SCRIPT ================== -->
  <?php require_once '../../sections/admin/script.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
