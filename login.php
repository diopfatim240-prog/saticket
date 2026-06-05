<!DOCTYPE html>
<html lang="en">
  <?php require_once 'view/sections/login/head.php'; ?>
<body class="pace-top">
  <!-- ================== LOADER ================== -->
  <?php require_once 'view/sections/login/loader.php'; ?>
  
  
  
  <!-- begin #page-container -->
  <div id="page-container" class="fade">
    <!-- ================== FORM ================== -->
    <?php require_once 'view/sections/login/form.php'; ?>
    
<!-- ================== CONFIG ================== -->
    <?php require_once 'view/sections/login/config.php'; ?>
    <!-- end theme-panel -->
    
    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
  </div>
  <!-- end page container -->
  
  <!-- ================== BEGIN BASE JS ================== -->
  <?php require_once 'view/sections/login/script.php'; ?>

  <!-- ================== Message d'erreur ================== -->
  <?php if(isset($_GET['error']) && $_GET['error'] == 1 && isset($_GET['message'])) : ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: '<?php echo htmlspecialchars (string: $_GET['title'], flags: ENT_QUOTES, encoding: 'UTF-8'); ?>',
        text:'<?php echo htmlspecialchars (string: $_GET['message'], flags: ENT_QUOTES, encoding: 'UTF-8'); ?>'
      });
    </script>
    <?php endif; ?>

</body>
</html> 