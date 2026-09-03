<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";

    $TIPI_VALIDI = array_keys(GENERI);
    $tipo = $_GET["tipo"] ?? $TIPI_VALIDI[0];
    if (!in_array($tipo, $TIPI_VALIDI, true)) {
        $tipo = $TIPI_VALIDI[0];
    }

    $utente_id = $_SESSION["utente_id"];
    $stmt = $mysqli->prepare("SELECT id FROM opere WHERE utente_id = ? AND tipo = ? ORDER BY RAND() LIMIT 1");
    $stmt->bind_param("is", $utente_id, $tipo);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        header("Location: lista.php?tipo=" . urlencode($tipo) . "&avviso=" . urlencode("Non c'e' ancora nulla da consigliarti in questa categoria!"));
        exit;
    }

    $id = $res->fetch_assoc()["id"];
    header("Location: dettaglio.php?id=" . (int)$id);
    exit;
?>