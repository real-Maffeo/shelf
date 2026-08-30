<?php
    require_once "db.php";
    header("Content-Type: application/json");

    if (isset($_SESSION["username"])) {     // Trovato utente loggato
        echo json_encode(["status" => "ok", "username" => $_SESSION["username"]]);
    } else {
        http_response_code(401);
        echo json_encode(["errore" => "Non sei autenticato!"]);
    }
    header("Location: ../index.html");
?>