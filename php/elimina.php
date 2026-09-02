<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit("Metodo non consentito!");
    }

    $id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);
    $utente_id = $_SESSION["utente_id"];

    if ($id === false) {
        header("Location: lista.php?messaggio=" . urlencode("Opera non trovata."));
        exit;
    }

    $stmt = $mysqli->prepare("SELECT tipo FROM opere WHERE id = ? AND utente_id = ?");
    $stmt->bind_param("ii", $id, $utente_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        header("Location: lista.php?messaggio=" . urlencode("Opera non trovata."));
        exit;
    }
    $tipo = $res->fetch_assoc()["tipo"];

    $stmtDel = $mysqli->prepare("DELETE FROM opere WHERE id = ? AND utente_id = ?");
    $stmtDel->bind_param("ii", $id, $utente_id);

    if (!$stmtDel->execute()) {
        http_response_code(500);
        exit("Errore durante l'eliminazione.");
    }

    header("Location: lista.php?tipo=" . urlencode($tipo) . "&messaggio=" . urlencode("Eliminato con successo."));
    exit;
?>