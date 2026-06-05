<?php

session_start();
require_once __DIR__ . '/../model/PromotionsRepository.php';

$promotionsRepository = new PromotionsRepository();


function forceRedirect($message, $status) {
    $param = $status ? "messageSuccess" : "messageError";
    $targetUrl = "/saticket/view/pages/admin/promotions/liste.php?{$param}=" . urlencode($message);
    
    // On génère un script JS propre qui redirige immédiatement
    echo "<script type='text/javascript'>
        window.location.href = '{$targetUrl}';
    </script>";
    exit();
}

// ==========================================
// 1. CREATE : AJOUT D'UN CODE PROMO
// ==========================================
if (isset($_POST['btnAddPromo'])) {
    $code = $_POST['code'];
    $reduction = $_POST['reduction']; 
    
    $promotionsRepository->add($code, $reduction);
    
    forceRedirect("Code promo actif", true);
}

// ==========================================
// 2. UPDATE : MODIFICATION D'UN CODE PROMO
// ==========================================
if (isset($_POST['btnUpdatePromo'])) {
    $id = (int)$_POST['id'];
    $code = $_POST['code'];
    $reduction = $_POST['reduction']; 
    
    $promotionsRepository->update($id, $code, $reduction);
    
    forceRedirect("Code promo mis à jour.", true);
}

// ==========================================
// 3. DELETE : SUPPRESSION D'UN CODE PROMO
// ==========================================
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $promotionsRepository->delete($id);
    
    forceRedirect("Code supprimé", true);
}
?>