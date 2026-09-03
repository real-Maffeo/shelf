<?php
    require_once "dbaccess.php";

    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status#client_error_responses per i codici di riposta
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit("Metodo non consentito!");
    }

    $username = strip_tags(trim($_POST["username"] ?? ""));
    $password = strip_tags(trim($_POST["password"] ?? ""));
    $confirm_password = strip_tags(trim($_POST["confirm_password"] ?? ""));
    
    function erroreRegistrazione($messaggio) {
        http_response_code(400);
        header("Location: ../html/registrazione.html?errore=" . urlencode($messaggio));
        exit;
    }

    // Controllo che i campi siano pieni
    if (empty($username) || empty($password) || empty($confirm_password)) {
        erroreRegistrazione("username o password non inseriti!");
    }
    
    // Controllo lunghezza dei campi
    if (strlen($username) < 4 || strlen($username) > 32 || strlen($password) < 8 || strlen($password) > 64) {
        erroreRegistrazione("username o password di dimensioni non accettabili!");
    }

    // Controllo che la stessa password sia stata inserita entrmabe le volte uguale
    if ($password != $confirm_password) {
        erroreRegistrazione("Password diverse!");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT); // Hash della password
    $stmt = $mysqli->prepare("INSERT INTO utenti (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash); // Protezione da SQL injection

    // Controlli sull'esecuzione della query
    if (!$stmt->execute()) {
        if ($mysqli->errno === 1062) {  // 1062: Duplicate entry
            http_response_code(409);    // Conflitto
            header("Location: ../html/registrazione.html?errore=" . urlencode("Username gia' esistente!"));
            exit;
        } else {
            http_response_code(500);
        exit("Errore del database" /*. $stmt->error*/);
        }
    }

    $_SESSION["utente_id"] = $mysqli->insert_id;
    $_SESSION["utente_id"] = $mysqli->insert_id;
    $_SESSION["username"] = $username;
    $_SESSION["ultimo_accesso"] = time();

    header("Location: ../index.html?successo=" . urlencode("Registrazione completata con successo ദ്ദി(｡•̀ ,<)~✩‧₊"));  // Alternativa: ( ˶ˆᗜˆ˵ )     Link: https://emojicombos.com/happy
    exit;
?>