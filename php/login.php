<?php
    require_once "dbaccess.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit("Metodo non consentito!");
    }
    
    $username = strip_tags(trim($_POST["username"] ?? ""));
    $password = strip_tags(trim($_POST["password"] ?? ""));

    // Controllo che i campi siano pieni
    if (empty($username) || empty($password)) {
        http_response_code(400);
        header("Location: ../index.html?errore=" . urlencode("Username o password non inseriti!"));
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password_hash FROM utenti WHERE username = ?");
    $stmt->bind_param("s", $username); // Protezione da SQL injection

    $stmt->execute();
    $res = $stmt->get_result();

    // Controllo l'username
    if ($res->num_rows === 0) {
        http_response_code(401);
        header("Location: ../index.html?errore=" . urlencode("Credenziali non valide!"));
        exit;
    }

    $row = $res->fetch_assoc();

    // Controllo la correttezza della password
    if (!password_verify($password, $row["password_hash"])) {
        http_response_code(401);
        header("Location: ../index.html?errore=" . urlencode("Credenziali non valide!"));
        exit;
    }

    $_SESSION["utente_id"] = $row["id"];
    $_SESSION["ultimo_accesso"] = time();
    $_SESSION["username"] = $username;
    // echo json_encode(["status" => "successo", "messaggio" => "Login avvenuto con successo", "username" => $username]);
    header("Location: lista.php?messaggio=" . urlencode("Login avvenuto con successo"));
?>