<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";

    if ($_SERVER["REQUEST_METHOD"] === "GET") {     // Mostra il form
        ?>
        <!DOCTYPE html>
        <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Aggiungi</title>
            </head>
            <body>

                <h1>Aggiungi nuovo titolo</h1>

                <!-- 
                    tipo ENUM('film','libro','fumetto','serie_tv') NOT NULL,
                    titolo VARCHAR(64) NOT NULL,
                    creatore VARCHAR(64),   -- Regista, autore, o ideatore, a seconda del tipo
                    genere VARCHAR(16) NOT NULL,
                    copertina_url VARCHAR(256) DEFAULT NULL,
                    valutazione TINYINT UNSIGNED NOT NULL,  -- 1-5, controllato lato PHP
                    descrizione TEXT,
                    segnalibro VARCHAR(32) DEFAULT NULL,    -- NULL per tipo='film'
                    preferito BOOLEAN DEFAULT FALSE,
                -->

                <form method="POST">
                    <!-- Campi del form -->
                    <label for="tipo">Categoria:</label>
                    <select name="tipo" id="tipo" onchange="cambiaTipo()" required>
                        <option value="film">Film</option>
                        <option value="libro">Libro</option>
                        <option value="fumetto">Fumetto</option>
                        <option value="serie_tv">Serie TV</option>
                    </select>

                    <br><br>

                    <label for="titolo">Titolo:</label>
                    <input type="text" name="titolo" id="titolo" required>

                    <br><br>

                    <label for="creatore" id="labelCreatore">Chi l'ha creato:</label>
                    <input type="text" name="creatore" id="creatore" required>

                    <br><br>

                    <label for="genere">Genere:</label>
                    <select name="genere" id="genere" required>
                        <?php foreach ($GENERI[tipo] as $genere): ?>
                            <option value="<?= htmlspecialchars($genere) ?>">
                                <?= htmlspecialchars($genere) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <br><br>

                    <label for="copertina_url">URL:</label>
                    <input type="url" name="copertina_url" id="copertina_url">

                    <br><br>

                    <!-- Non so come implementare valutazione -->

                    <br><br>

                    <label for="commento">Commento: </label>
                    <input type="text" id="commento" name="commento" pattern="^[a-zA-Z0-9!?/.,;:]{0,512}$"> <!-- il limite 512 e' per prova, da definire, da decidere quali caratteri consentire -->

                    <br><br>

                    <label for="segnalibro">Fino a dove sei arrivato:</label>
                    <input type="text" name="segnalibro" id="segnalibro" pattern="^[a-zA-Z0-9]">

                    <div id="datiSerie" style="display: none;">
                        <label for="stagione">Stagione:</label>
                        <input type="number" name="stagione" id="stagione" min="1">

                        <label for="episodio">Episodio:</label>
                        <input type="number" name="episodio" id="episodio" min="1">
                    </div>

                    <script>
                    function segnalibro() {
                        const tipo = document.getElementById("tipo").value;
                        const segnalibro = document.getElementById("segnalibro");

                        if (tipo === "film") {
                            segnalibro.disabled = true;
                            segnalibro.value = NULL;
                        } else if (tipo === "libro") {
                            label.textContent = "Pagina: ";
                            segnalibro.disabled = false;
                        } else if (tipo === "fumetto") {
                            label.textContent = "Capitolo: ";
                            segnalibro.disabled = false;
                        } else {
                            
                            segnalibro.disabled = false;
                        }
                    }
                        function cambiaTipo() {
                            const tipo = document.getElementById("tipo").value;
                            const labelCreatore = document.getElementById("labelCreatore");
                            const creatore = document.getElementById("creatore");
                            const datiSerie = document.getElementById("datiSerie");
                            const stagione = document.getElementById("stagione");
                            const episodio = document.getElementById("episodio");

                            // Gestione creatore
                            if (tipo === "film" || tipo === "serie_tv") {
                                labelCreatore.textContent = "Regista:";
                                creatore.disabled = false;
                                creatore.required = true;
                            } else if (tipo === "libro" || tipo === "fumetto") {
                                labelCreatore.textContent = "Autore:";
                                creatore.disabled = false;
                                creatore.required = true;
                            }

                            // Gestione segnalibro/stagione ed episodio
                            if (tipo === "film") {
                                segnalibro.disabled = true;
                                segnalibro.value = NULL;
                            } else if (tipo === "libro") {
                                label.textContent = "Pagina: ";
                                segnalibro.disabled = false;
                            } else if (tipo === "fumetto") {
                                label.textContent = "Capitolo: ";
                                segnalibro.disabled = false;
                            } else if (tipo === "serie_tv") {
                                segnalibro.disabled = false;
                            }

                            if (tipo === "serie_tv") {
                                datiSerie.style.display = "block";
                                stagione.disabled = false;
                                episodio.disabled = false;
                                stagione.required = true;
                                episodio.required = true;
                            } else {
                                datiSerie.style.display = "none";
                                stagione.disabled = true;
                                episodio.disabled = true;
                                stagione.required = false;
                                episodio.required = false;
                                stagione.value = "";
                                episodio.value = "";
                            }
                        }
                    </script>

                    <!-- Non so come implementare preferito -->

                    <button type="submit">Salva</button>
                </form>
            </body>
        </html>
        <?php
    }  elseif ($_SERVER["REQUEST_METHOD"] !== "POST") {     // Valida e salva

        /* 
            tipo ENUM('film','libro','fumetto','serie_tv') NOT NULL,
            titolo VARCHAR(64) NOT NULL,
            creatore VARCHAR(64),   -- Regista, autore, o ideatore, a seconda del tipo
            genere VARCHAR(16) NOT NULL,
            copertina_url VARCHAR(256) DEFAULT NULL,
            valutazione TINYINT UNSIGNED NOT NULL,  -- 1-5, controllato lato PHP
            descrizione TEXT,
            segnalibro VARCHAR(32) DEFAULT NULL,    -- NULL per tipo='film'
            preferito BOOLEAN DEFAULT FALSE,
        */

        // Recuperazione e validazione dei dati
        $tipo = strip_tags(trim($_POST["tipo"] ?? ""));
        $titolo = strip_tags(trim($_POST["titolo"] ?? ""));
        $creatore = strip_tags(trim($_POST["creatore"] ?? ""));
        $genere = strip_tags(trim($_POST["genere"] ?? ""));
        $copertina_url = trim($_POST["copertina_url"] ?? "");
        $valutazione = strip_tags(trim($_POST["valutazione"] ?? ""));
        $descrizione = strip_tags(trim($_POST["descrizione"] ?? ""));
        $segnalibro = strip_tags(trim($_POST["segnalibro"] ?? ""));
        if ($tipo === "serie_tv") {
            $stagione = $_POST["stagione"] ?? "";
            $episodio = $_POST["episodio"] ?? "";

            $segnalibro = "Stagione " . $stagione . ", Episodio " . $episodio;
        }
        $preferito = strip_tags(trim($_POST["preferito"] ?? ""));

        echo "<p>Dati salvati correttamente.</p>";
        } else {
            http_response_code(405);
            exit("Metodo non consentito!");
        }



        $tipo = $_POST["tipo"] ?? "";
        $creatore = strip_tags(trim($_POST["creatore"] ?? ""));

        if ($tipo === "serie_tv") {
            $stagione = $_POST["stagione"] ?? "";
            $episodio = $_POST["episodio"] ?? "";

            $output = "Stagione " . $stagione . ", Episodio " . $episodio;
        }

?>