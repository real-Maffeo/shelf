<?php
    require_once "dbaccess.php";

    // Svuoto i dati e distruggo la sessione
    session_unset();
    session_destroy();

    header("Location: ../index.html?messaggio=" . urlencode("A presto (T^T)"));
    exit;
?>