<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";
    require_once "validazione_opera.php";

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
                <?php if (isset($_GET["errore"])): ?>
                    <div id="errore"><?= htmlspecialchars($_GET["errore"]) ?></div> <!-- Stampa errore -->
                <?php endif; ?>
                
                <form method="POST" novalidate>
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

                    <label for="genere_1">Genere:</label>
                    <select name="genere_1" id="genere_1" required>
                    </select>

                    <label for="genere_2">Genere secondario (facoltativo):</label>
                    <select name="genere_2" id="genere_2">
                        <option value="">-- Nessuno --</option>
                    </select>

                    <label for="genere_3">Terzo genere (facoltativo):</label>
                    <select name="genere_3" id="genere_3">
                        <option value="">-- Nessuno --</option>
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

                    <div id="blocco_segnalibro">
                        <label for="segnalibro" id="labelSegnalibro">Fino a dove sei arrivato:</label>
                        <input type="text" name="segnalibro" id="segnalibro" maxlength="32">
                    </div>

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
                <script src="../js/opera-form.js"></script>
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
        $genere_1 = strip_tags(trim($_POST["genere_1"] ?? ""));
        $genere_2 = strip_tags(trim($_POST["genere_2"] ?? ""));
        $genere_3 = strip_tags(trim($_POST["genere_3"] ?? ""));
        $copertina_url = strip_tags(trim($_POST["copertina_url"] ?? ""));
        $valutazione = filter_var($_POST["valutazione"] ?? "", FILTER_VALIDATE_INT);
        $descrizione = strip_tags(trim($_POST["commento"] ?? ""));
        $segnalibro = strip_tags(trim($_POST["segnalibro"] ?? ""));

        $errore = validaOpera($tipo, $titolo, $creatore, $genere_1, $genere_2, $genere_3, $valutazione, $descrizione, $copertina_url, $TIPI_VALIDI);
        if ($errore !== null) {
            erroreAggiunta($errore);
        }

        $stagione = filter_var($_POST["stagione"] ?? "", FILTER_VALIDATE_INT);
        $episodio = filter_var($_POST["episodio"] ?? "", FILTER_VALIDATE_INT);
        $segnalibro = costruisciSegnalibro($tipo, $segnalibro, $stagione, $episodio);

        if ($segnalibro !== null && strlen($segnalibro) > 32) {
            erroreAggiunta("Segnalibro troppo lungo: usa numeri più piccoli per stagione/episodio.");
        }

        $stmt = $mysqli->prepare(
            "INSERT INTO opere (utente_id, tipo, titolo, creatore, genere_1, genere_2, genere_3, copertina_url, valutazione, descrizione, segnalibro)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $genere_2_db = $genere_2 !== "" ? $genere_2 : null;
        $genere_3_db = $genere_3 !== "" ? $genere_3 : null;
        $utente_id = $_SESSION["utente_id"];
        $creatore_db = $creatore !== "" ? $creatore : null;
        $copertina_db = $copertina_url !== "" ? $copertina_url : null;
        $descrizione_db = $descrizione !== "" ? $descrizione : null;

        $stmt->bind_param(
            "isssssssiss",
            $utente_id, $tipo, $titolo, $creatore_db, $genere_1, $genere_2_db, $genere_3_db, $copertina_db, $valutazione, $descrizione_db, $segnalibro
        );

        if (!$stmt->execute()) {
            http_response_code(500);
            exit("Errore durante il salvataggio");
        }

        header("Location: lista.php?tipo=" . urlencode($tipo) . "&messaggio=" . urlencode("Aggiunto con successo!"));
        exit;

    } else {
        http_response_code(405);
        exit("Metodo non consentito!");
    }
?>