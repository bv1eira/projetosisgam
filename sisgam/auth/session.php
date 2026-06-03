<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {    
    session_unset();
    session_destroy();
    header("Location: ../");
    exit;
}
?>