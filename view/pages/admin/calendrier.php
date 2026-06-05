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

require_once __DIR__ . '/../../../model/EvenementRepository.php';
$evenementRepo = new EvenementRepository();
$listeEvenements = $evenementRepo->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/../../sections/admin/head.php'; ?>
<body>
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php require_once __DIR__ . '/../../sections/admin/menuHaut.php'; ?>
    <?php require_once __DIR__ . '/../../sections/admin/menuGauche.php'; ?>

    <div id="content" class="content">
      <h1 class="page-header">Agenda Global <small>Calendrier des événements</small></h1>

      <div class="panel panel-inverse">
        <div class="panel-heading"><h4 class="panel-title">Prochains événements (<?= count($listeEvenements ?? []); ?>)</h4></div>
        <div class="panel-body">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Date</th>
                <th>Lieu</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($listeEvenements)): ?>
                <?php foreach ($listeEvenements as $ev): ?>
                  <tr>
                    <td><?= htmlspecialchars($ev['titre'] ?? 'N/A'); ?></td>
                    <td><?= !empty($ev['date']) ? date('d/m/Y', strtotime($ev['date'])) : 'Non définie'; ?></td>
                    <td><?= htmlspecialchars($ev['lieu'] ?? 'N/A'); ?></td>
                    <td><?= htmlspecialchars(substr($ev['description'] ?? '', 0, 120)); ?>...</td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted">Aucun événement planifié.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
  </div>

  <?php require_once __DIR__ . '/../../sections/admin/script.php'; ?>
</body>
</html>
