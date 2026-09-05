<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";
    require_once "validazione_opera.php";

    $TIPI_VALIDI = array_keys(GENERI);
    $id = filter_var($_GET["id"] ?? $_POST["id"] ?? "", FILTER_VALIDATE_INT);
    $utente_id = $_SESSION["utente_id"];

    if ($id === false) {
        header("Location: lista.php?avviso=" . urlencode("Opera non trovata!"));
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {

        $stmt = $mysqli->prepare("SELECT * FROM opere WHERE id = ? AND utente_id = ?");
        $stmt->bind_param("ii", $id, $utente_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            header("Location: lista.php?avviso=" . urlencode("Opera non trovata!"));
            exit;
        }
        $opera = $res->fetch_assoc();

        $stagioneVal = "";
        $episodioVal = "";
        if ($opera["tipo"] === "serie_tv" && $opera["segnalibro"]) {
            if (preg_match('/Stagione (\d+), Episodio (\d+)/', $opera["segnalibro"], $m)) {
                $stagioneVal = $m[1];
                $episodioVal = $m[2];
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Modifica</title>
                <link href="../css/style.css" rel="stylesheet" type="text/css">
            </head>

            <body class="pagina-form">
                <h1>Modifica</h1>
                <?php if (isset($_GET["errore"])): ?>
                    <div id="errore"><?= htmlspecialchars($_GET["errore"]) ?></div>
                <?php endif; ?>

                <form method="POST" novalidate class="form-opera">
                    <div class="campo">
                        <label for="tipo">Categoria:</label>
                        <select name="tipo" id="tipo" required>
                            <?php foreach ($TIPI_VALIDI as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= $t === $opera["tipo"] ? "selected" : "" ?>>
                                    <?= htmlspecialchars(ETICHETTE_TIPO[$t]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="titolo">Titolo:</label>
                        <input type="text" name="titolo" id="titolo" maxlength="64" required value="<?= htmlspecialchars($opera['titolo']) ?>">
                    </div>

                    <div class="campo">
                        <label for="creatore" id="labelCreatore">Regista:</label>
                        <input type="text" name="creatore" id="creatore" maxlength="64" value="<?= htmlspecialchars($opera['creatore'] ?? '') ?>">
                    </div>
                    
                    <div class="campo">
                        <label for="genere_1">Genere:</label>
                        <select name="genere_1" id="genere_1" required></select>
                    </div>

                    <div class="campo">
                        <label for="genere_2">Genere secondario:</label>
                        <select name="genere_2" id="genere_2"><option value="">-- Nessuno --</option></select>
                    </div>
                    
                    <div class="campo">
                        <label for="genere_3">Terzo genere:</label>
                        <select name="genere_3" id="genere_3"><option value="">-- Nessuno --</option></select>
                    </div>

                    <div class="campo">
                        <label for="copertina_url">URL copertina:</label>
                        <input type="url" name="copertina_url" id="copertina_url" maxlength="256" value="<?= htmlspecialchars($opera['copertina_url'] ?? '') ?>">
                    </div>

                    <div class="campo">
                        <label>Valutazione:</label>
                        <span id="stelle" data-valore="0">
                            <span class="stella" data-valore="1">&#9734;</span><span class="stella" data-valore="2">&#9734;</span><span class="stella" data-valore="3">&#9734;</span><span class="stella" data-valore="4">&#9734;</span><span class="stella" data-valore="5">&#9734;</span>
                        </span>
                        <input type="hidden" name="valutazione" id="valutazione" value="<?= (int)$opera['valutazione'] ?>" required>
                    </div>

                    <div class="campo campo-larga">
                        <label for="commento">Commento:</label>
                        <textarea id="commento" name="commento" maxlength="512" rows="4"><?= htmlspecialchars($opera['descrizione'] ?? '') ?></textarea>
                        <span id="contatore-descrizione">0/512</span>
                    </div>

                    <div class="campo campo-larga" id="blocco_segnalibro">
                        <label for="segnalibro" id="labelSegnalibro">Fino a dove sei arrivato:</label>
                        <input type="text" name="segnalibro" id="segnalibro" maxlength="32" value="<?= $opera['tipo'] !== 'serie_tv' ? htmlspecialchars($opera['segnalibro'] ?? '') : '' ?>">
                    </div>

                    <div class="campo campo-larga" id="datiSerie" style="display: none;">
                        <label for="stagione">Stagione:</label>
                        <input type="number" name="stagione" id="stagione" min="1" value="<?= htmlspecialchars($stagioneVal) ?>">
                        <label for="episodio">Episodio:</label>
                        <input type="number" name="episodio" id="episodio" min="1" value="<?= htmlspecialchars($episodioVal) ?>">
                    </div>

                    <div class="campo campo-larga">
                        <button type="submit">Salva</button>
                    </div>
                </form>

                <script>
                    const GENERI = <?= json_encode(GENERI) ?>;
                    const GENERE_1_ATTUALE = <?= json_encode($opera["genere_1"]) ?>;
                    const GENERE_2_ATTUALE = <?= json_encode($opera["genere_2"]) ?>;
                    const GENERE_3_ATTUALE = <?= json_encode($opera["genere_3"]) ?>;
                    const VALUTAZIONE_ATTUALE = <?= (int)$opera["valutazione"] ?>;
                </script>

                <script src="../js/opera-form.js"></script>
                <script src="../js/ui.js"></script>
            </body>
        </html>
        <?php

    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

        function erroreModifica($id, $messaggio) {
            header("Location: modifica.php?id=" . (int)$id . "&errore=" . urlencode($messaggio));
            exit;
        }

        $stmtCheck = $mysqli->prepare("SELECT id FROM opere WHERE id = ? AND utente_id = ?");
        $stmtCheck->bind_param("ii", $id, $utente_id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows === 0) {
            header("Location: lista.php?avviso=" . urlencode("Opera non trovata!"));
            exit;
        }

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
            erroreModifica($id, $errore);
        }

        $stagione = filter_var($_POST["stagione"] ?? "", FILTER_VALIDATE_INT);
        $episodio = filter_var($_POST["episodio"] ?? "", FILTER_VALIDATE_INT);
        $segnalibro = costruisciSegnalibro($tipo, $segnalibro, $stagione, $episodio);

        if ($segnalibro !== null && strlen($segnalibro) > 32) {
            erroreModifica($id, "Segnalibro troppo lungo!");
        }

        $creatore_db = $creatore !== "" ? $creatore : null;
        $copertina_db = $copertina_url !== "" ? $copertina_url : null;
        $descrizione_db = $descrizione !== "" ? $descrizione : null;
        $genere_2_db = $genere_2 !== "" ? $genere_2 : null;
        $genere_3_db = $genere_3 !== "" ? $genere_3 : null;

        $stmt = $mysqli->prepare(
            "UPDATE opere SET tipo = ?, titolo = ?, creatore = ?, genere_1 = ?, genere_2 = ?, genere_3 = ?,
             copertina_url = ?, valutazione = ?, descrizione = ?, segnalibro = ?
             WHERE id = ? AND utente_id = ?"
        );
        $stmt->bind_param(
            "sssssssissii",
            $tipo, $titolo, $creatore_db, $genere_1, $genere_2_db, $genere_3_db,
            $copertina_db, $valutazione, $descrizione_db, $segnalibro, $id, $utente_id
        );

        if (!$stmt->execute()) {
            http_response_code(500);
            exit("Errore durante il salvataggio");
        }

        header("Location: dettaglio.php?id=" . (int)$id . "&successo=" . urlencode("Modifiche salvate!"));
        exit;

    } else {
        http_response_code(405);
        exit("Metodo non consentito!");
    }
?>