<?php
    require_once "dbaccess.php";
    // header("Content-Type: application/json");

    /*
    if (isset($_SESSION["username"])) {     // Trovato utente loggato
        echo json_encode(["status" => "ok", "username" => $_SESSION["utente_id"]]);
    } else {
        http_response_code(401);
        echo json_encode(["errore" => "Non sei autenticato!"]);
        header("Location: ../index.html");
    }
    */

    if (!isset($_SESSION["utente_id"])) {
        header("Location: ../index.html?errore=" . urlencode("Devi effettuare il login!"));
        exit;
    }
?>