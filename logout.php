<?php
session_start();

// Destroy la session
session_destroy();

// Détruire les cookies de remember-me s'ils existent
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/', '', true, true);
}

// Redirection vers la page de login
header("Location: login.php");
exit();
?>
