<?php
    require_once "dbaccess.php";
    header("Content-Type: application/json");

    if (!isset($_SESSION["utente_id"])) {
        http_response_code(401);
        echo json_encode(["errore" => "Non autenticato"]);
        exit;
    }
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["errore" => "Metodo non consentito"]);
        exit;
    }

    $id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);
    if ($id === false) {
        http_response_code(400);
        echo json_encode(["errore" => "ID non valido"]);
        exit;
    }

    $utente_id = $_SESSION["utente_id"];

    // Verifica che l'opera appartenga davvero all'utente loggato prima di toccarla
    $stmt = $mysqli->prepare("SELECT preferito FROM opere WHERE id = ? AND utente_id = ?");
    $stmt->bind_param("ii", $id, $utente_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["errore" => "Opera non trovata"]);
        exit;
    }

    $nuovoStato = $res->fetch_assoc()["preferito"] ? 0 : 1;

    $stmt2 = $mysqli->prepare("UPDATE opere SET preferito = ? WHERE id = ? AND utente_id = ?");
    $stmt2->bind_param("iii", $nuovoStato, $id, $utente_id);
    $stmt2->execute();

    echo json_encode(["preferito" => (bool)$nuovoStato]);
?>