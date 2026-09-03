<?php
    require_once "dbaccess.php";

    $TIMEOUT_SECONDI = 3600; // 1 ora

    if (isset($_SESSION["utente_id"])) {
        if (isset($_SESSION["ultimo_accesso"]) && (time() - $_SESSION["ultimo_accesso"]) > $TIMEOUT_SECONDI) {
            session_unset();
            session_destroy();
            header("Location: ../index.html?errore=" . urlencode("Sessione scaduta, effettua nuovamente il login."));
            exit;
        }
        $_SESSION["ultimo_accesso"] = time(); // Ricordarsi perche' e qua'
    } else {
        header("Location: ../index.html?errore=" . urlencode("Devi effettuare il login!"));
        exit;
    }
?>