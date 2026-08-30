<?php
    require_once "dbaccess.php";
    header("Content-Type: application/json");

    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status#client_error_responses per i codici di riposta
    if (!$_SERVER["REQUEST_METHOD" === "POST"]) {
        http_response_code(405);
        echo json_encode(["errore" => "Metodo non consentito!"]);
        exit;
    }
    
    $username = strip_tags(trim($_POST["username"]));
    $password = strip_tags(trim($_POST["password"]));
    $confirm_password = strip_tags(trim($_POST["confirm_password"]));

    // Controllo che i campi siano pieni
    if (empty($username) || empty($password) || empty($confirm_password)) {
        http_response_code(400);
        echo json_encode(['errore' => 'username o password non inseriti!']);
        exit;
    }
    
    // Controllo lunghezza dei campi
    if (strlen($username) < 4 || strlen($username) > 32 || strlen($password) < 8 || strlen($password) > 64) {
        http_response_code(400); 
        echo json_encode(['errore' => 'username o password troppo lunghi!']);
        exit;
    }

    // Controllo che la stessa password sia stata inserita entrmabe le volte uguale
    if ($password != $confirm_password) {
        http_response_code(400); 
        echo json_encode(["errore" => "Password diverse!"]);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT); // Hash della password
    $stmt = $mysqli->prepare("INSERT INTO user (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password); // Protezione da SQL injection

    // Controlli sull'esecuzione della query
    if (!$stmt->execute()) {
        if ($mysqli->errno === 1062) {  // 1062: Duplicate entry
            http_response_code(409);    // Conflitto
            echo json_encode(["errore" => "Username gia' esistente!"]);
        } else {
            http_response_code(500);
            echo json_encode(["errore" => "Errore database: " . $stmt->error]);
        }
        exit;
    }

    // $_SESSION["username"] = $username;
    echo json_encode(["status" => "successo", "messaggio" => "Registrazione avvenuta con successo", "username" => $username]);
    header("Location: ../index.html");
?>