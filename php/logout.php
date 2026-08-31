<?php
    require_once "dbaccess.php";
    // header("Content-Type: application/json");

    // Svuoto i dati e distruggo la sessione
    session_unset();
    session_destroy();

    // echo json_encode(["status" => "successo", "messaggio" => "(T^T)"]);
    header("Location: ../index.html?errore=" . urlencode("A presto (T^T)"));
    exit;
?>