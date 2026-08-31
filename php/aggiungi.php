<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";

    $TIPI_VALIDI = array_keys(GENERI); // film, libro, fumetto, serie_tv

    if ($_SERVER["REQUEST_METHOD"] === "GET") {     // Mostra il form
        ?>
        <!DOCTYPE html>
        <html lang="it">
            <head>
                <meta charset="UTF-8">
                <link href="../css/style.css" rel="stylesheet" type="text/css">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Aggiungi</title>
            </head>
            <body>
                <h1>Aggiungi nuovo titolo</h1>
                
                <form method="POST">
                    <label for="tipo">Categoria:</label>
                    <select name="tipo" id="tipo" required>
                        <?php foreach ($TIPI_VALIDI as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $t))) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <br><br>

                    <label for="titolo">Titolo:</label>
                    <input type="text" name="titolo" id="titolo" maxlength="64" required>

                    <br><br>

                    <label for="creatore" id="labelCreatore">Regista:</label>
                    <input type="text" name="creatore" id="creatore" maxlength="64">

                    <br><br>

                    <label for="genere">Genere:</label>
                    <select name="genere" id="genere" required>
                        <?php foreach (GENERI[$TIPI_VALIDI[0]] as $genere): ?>
                            <option value="<?= htmlspecialchars($genere) ?>"><?= htmlspecialchars($genere) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <br><br>

                    <label for="copertina_url">URL copertina:</label>
                    <input type="url" name="copertina_url" id="copertina_url" maxlength="256">

                    <br><br>

                    <label>Valutazione:</label>
                    <span id="stelle" data-valore="0">
                        <span class="stella" data-valore="1">&#9734;</span><span class="stella" data-valore="2">&#9734;</span><span class="stella" data-valore="3">&#9734;</span><span class="stella" data-valore="4">&#9734;</span><span class="stella" data-valore="5">&#9734;</span>
                    </span>
                    <input type="hidden" name="valutazione" id="valutazione" value="0" required>

                    <br><br>

                    <label for="commento">Commento: </label>
                    <textarea id="commento" name="commento" maxlength="512" rows="4"></textarea>

                    <br><br>

                    <label for="segnalibro" id="labelSegnalibro">Fino a dove sei arrivato:</label>
                    <input type="text" name="segnalibro" id="segnalibro" maxlength="32">

                    <div id="datiSerie" style="display: none;">
                        <label for="stagione">Stagione:</label>
                        <input type="number" name="stagione" id="stagione" min="1">

                        <label for="episodio">Episodio:</label>
                        <input type="number" name="episodio" id="episodio" min="1">
                    </div>

                    <br><br>
                    <button type="submit">Salva</button>
                </form>

                <script>
                    const GENERI = <?= json_encode(GENERI) ?>;
                </script>
                <script src="../js/aggiungi.js"></script>
            </body>
        </html>
        <?php
    }  elseif ($_SERVER["REQUEST_METHOD"] === "POST") {     // Valida e salva

        function erroreAggiunta($messaggio) {
            header("Location: aggiungi.php?errore=" . urlencode($messaggio));
            exit;
        }

        // Recupero e sanifica dei dati
        $tipo = strip_tags(trim($_POST["tipo"] ?? ""));
        $titolo = strip_tags(trim($_POST["titolo"] ?? ""));
        $creatore = strip_tags(trim($_POST["creatore"] ?? ""));
        $genere = strip_tags(trim($_POST["genere"] ?? ""));
        $copertina_url = strip_tags(trim($_POST["copertina_url"] ?? ""));
        $valutazione = filter_var($_POST["valutazione"] ?? "", FILTER_VALIDATE_INT);
        $descrizione = strip_tags(trim($_POST["commento"] ?? ""));
        $segnalibro = strip_tags(trim($_POST["segnalibro"] ?? ""));

        // Controllo correttezza dati
        if (!in_array($tipo, $TIPI_VALIDI, true)) {
            erroreAggiunta("Categoria non valida!");
        }
        if (!in_array($genere, GENERI[$tipo], true)) {
            erroreAggiunta("Genere non valido per questa categoria!");
        }
        if ($titolo === "" || strlen($titolo) > 64) {
            erroreAggiunta("Titolo obbligatorio, massimo 64 caratteri!");
        }
        if (strlen($creatore) > 64) {
            erroreAggiunta("Il nome del creatore è troppo lungo (max 64 caratteri)!");
        }
        if ($valutazione === false || $valutazione < 1 || $valutazione > 5) {
            erroreAggiunta("Seleziona una valutazione da 1 a 5 stelle!");
        }
        if (strlen($descrizione) > 512) {
            erroreAggiunta("Il commento è troppo lungo (max 512 caratteri)!");
        }
        if ($copertina_url !== "" && !filter_var($copertina_url, FILTER_VALIDATE_URL)) {
            erroreAggiunta("Il link della copertina non è valido!");
        }

        // Segnalibro personalizzato in base al tipo
        if ($tipo === "serie_tv") {
            $stagione = filter_var($_POST["stagione"] ?? "", FILTER_VALIDATE_INT);
            $episodio = filter_var($_POST["episodio"] ?? "", FILTER_VALIDATE_INT);
            if ($stagione === false || $episodio === false) {
                erroreAggiunta("Stagione ed episodio sono obbligatori per le serie TV!");
            }
            $segnalibro = "Stagione {$stagione}, Episodio {$episodio}";
        } elseif ($tipo === "film") {
            $segnalibro = null;
        } else {
            if (strlen($segnalibro) > 32) {
                erroreAggiunta("Il segnalibro è troppo lungo (max 32 caratteri)!");
            }
            $segnalibro = $segnalibro !== "" ? $segnalibro : null;
        }

        $stmt = $mysqli->prepare("INSERT INTO opere (utente_id, tipo, titolo, creatore, genere, copertina_url, valutazione, descrizione, segnalibro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $utente_id = $_SESSION["utente_id"];
        $creatore_db = $creatore !== "" ? $creatore : null;
        $copertina_db = $copertina_url !== "" ? $copertina_url : null;
        $descrizione_db = $descrizione !== "" ? $descrizione : null;

        $stmt->bind_param("isssssiss", $utente_id, $tipo, $titolo, $creatore_db, $genere, $copertina_db, $valutazione, $descrizione_db, $segnalibro);

        if (!$stmt->execute()) {
            http_response_code(500);
            exit("Errore durante il salvataggio!");
        }

        header("Location: lista.php?tipo=" . urlencode($tipo) . "&messaggio=" . urlencode("Aggiunto con successo!"));
        exit;

    } else {
        http_response_code(405);
        exit("Metodo non consentito!");
    }
?>