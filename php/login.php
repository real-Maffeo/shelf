<?php
    require_once "dbaccess.php";
    header("Content-Type: application/json");

    if (!$_SERVER["REQUEST_METHOD" === "POST"]) {
        http_response_code(405);
        echo json_encode(["errore" => "Metodo non consentito!"]);
        exit;
    }
    
    $username = strip_tags(trim($_POST["username"]));
    $password = strip_tags(trim($_POST["password"]));

    // Controllo che i campi siano pieni
    if (empty($username) || empty($password) || empty($confirm_password)) {
        http_response_code(400);
        echo json_encode(["errore" => "Username o password non inseriti!"]);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT password_hash FROM utenti WHERE username = ?");
    $stmt->bind_param("s", $username, $password); // Protezione da SQL injection

    $stmt->execute();
    $res = $stmt->get_result();

    // Controllo l'username
    if ($res->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["errore" => "Credenziali non valide!"]);
        exit;
    }

    $password_hash = $res->fetch_assoc()["password_hash"];

    // Controllo la correttezza della password
    if (!password_verify($password, $hash)) {
        http_response_code(401);
        echo json_encode(["errore" => "Credenziali non valide!"]);
        exit;
    }

    // $_SESSION["username"] = $username;
    echo json_encode(["status" => "successo", "messaggio" => "Login avvenuto con successo", "username" => $username]);
    header("Location: ../index.html");
?>