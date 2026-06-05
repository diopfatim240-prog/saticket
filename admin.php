<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
require_once 'view/sections/admin/head.php';
?>
<body>
  <div id="page-loader" class="fade show">
 <span class="spinner"></span>
 </div>
 
 
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <!-- ======================================== MENU HAUT ======================================== -->
    <?php require_once 'view/sections/admin/menuHaut.php'; ?>
    <!-- ================== MENU GAUCHE ================== -->
    <?php require_once 'view/sections/admin/menuGauche.php'; ?>
  
    
    <!-- ================== BASE CONTENT ================== -->
    <?php require_once 'view/sections/admin/baseContent.php'; ?>
    
    
    <!-- ================== SECTION CONFIG================== -->
    <?php require_once 'view/sections/admin/sectionConfig.php'; ?>
    
    
    <!-- ================== SECTION SCROLL TO TOP ================== -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    
  </div>
  
  <!-- ================== SECTION SCRIP ================== -->
  <?php require_once 'view/sections/admin/script.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php if(isset($_GET['error']) && $_GET['error'] == 1 && isset($_GET['message'])) : ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Erreur de connexion',
        text:'<?php echo htmlspecialchars (string: $_GET['message'], flags: ENT_QUOTES, encoding: 'UTF-8'); ?>'
      });
    </script>
    <?php endif; ?>

    <?php if(isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['message'])) : ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: '<?php echo htmlspecialchars (string: $_GET['title'], flags: ENT_QUOTES, encoding: 'UTF-8'); ?>',
        text:'<?php echo htmlspecialchars (string: $_GET['message'], flags: ENT_QUOTES, encoding: 'UTF-8'); ?>'
      });
    </script>
    <?php endif; ?>

  
</body>
</html> 