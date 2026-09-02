<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";
    require_once "validazione_opera.php";

    $TIPI_VALIDI = array_keys(GENERI);

    $tipo = $_GET["tipo"] ?? $TIPI_VALIDI[0];
    if (!in_array($tipo, $TIPI_VALIDI, true)) {
        $tipo = $TIPI_VALIDI[0]; // fallback silenzioso su un tipo valido, niente errore per un parametro sbagliato nell'URL
    }

    $stmt = $mysqli->prepare(
        "SELECT id, titolo, creatore, genere, copertina_url, valutazione, descrizione, segnalibro, preferito
         FROM opere WHERE utente_id = ? AND tipo = ? ORDER BY data_inserimento DESC"
    );
    $utente_id = $_SESSION["utente_id"];
    $stmt->bind_param("is", $utente_id, $tipo);
    $stmt->execute();
    $risultati = $stmt->get_result();

    $etichette = ["film" => "Film", "libro" => "Libri", "fumetto" => "Fumetti", "serie_tv" => "Serie TV"];
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shelf - <?= htmlspecialchars($etichette[$tipo]) ?></title>
        <link href="../css/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php if (isset($_GET["messaggio"])): ?>
            <div id="messaggio"><?= htmlspecialchars($_GET["messaggio"]) ?></div>
        <?php endif; ?>

        <nav>
            <?php foreach ($TIPI_VALIDI as $t): ?>
                <a href="lista.php?tipo=<?= urlencode($t) ?>"<?= $t === $tipo ? ' class="attivo"' : '' ?>>
                    <?= htmlspecialchars($etichette[$t]) ?>
                </a>
            <?php endforeach; ?>
            <a href="aggiungi.php">+ Aggiungi</a>
            <a href="consigliami.php?tipo=<?= urlencode($tipo) ?>">Consigliami qualcosa</a>
        </nav>

        <h1><?= htmlspecialchars($etichette[$tipo]) ?></h1>

        <!-- filtri laterali -->
        <div id="contenuto">
            <aside id="filtri">
                <div id="filtro-genere">
                    <p>Genere</p>
                    <?php foreach (GENERI[$tipo] as $genere): ?>
                        <label>
                            <input type="checkbox" class="filtro-genere-cb" value="<?= htmlspecialchars($genere) ?>">
                            <?= htmlspecialchars($genere) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div id="filtro-valutazione">
                    <p>Valutazione minima</p>
                    <input type="range" id="filtro-slider" min="1" max="5" value="1">
                    <span id="filtro-slider-valore">1</span>
                </div>

                <label>
                    <input type="checkbox" id="filtro-preferiti">
                    Solo preferiti
                </label>

                <div id="filtro-ricerca">
                    <input type="text" id="filtro-titolo" placeholder="Cerca un titolo...">
                </div>
            </aside>

            <div id="griglia">
                <?php if ($risultati->num_rows === 0): ?>
                    <p>Nessuna opera in questa categoria. <a href="aggiungi.php">Aggiungine una</a>.</p>
                <?php endif; ?>

                <?php while ($opera = $risultati->fetch_assoc()):
                    $generi = array_filter([$opera["genere_1"], $opera["genere_2"], $opera["genere_3"]]);
                ?>
                    <div class="card"
                         data-genere="<?= htmlspecialchars(implode("|", $generi)) ?>"
                         data-valutazione="<?= (int)$opera["valutazione"] ?>"
                         data-preferito="<?= $opera["preferito"] ? "1" : "0" ?>"
                         data-titolo="<?= htmlspecialchars(mb_strtolower($opera["titolo"])) ?>">

                        <button class="cuore" data-id="<?= (int)$opera["id"] ?>" aria-label="Preferito">
                            <?= $opera["preferito"] ? "&#9829;" : "&#9825;" ?>
                        </button>

                        <a href="dettaglio.php?id=<?= (int)$opera["id"] ?>">
                            <img src="<?= htmlspecialchars($opera["copertina_url"] ?: "../images/placeholder_{$tipo}.png") ?>"
                                 onerror="this.src='../images/placeholder_<?= htmlspecialchars($tipo) ?>.png'"
                                 alt="Copertina di <?= htmlspecialchars($opera["titolo"]) ?>">
                            <p class="titolo"><?= htmlspecialchars($opera["titolo"]) ?></p>
                        </a>

                        <p class="genere"><?= htmlspecialchars(implode(", ", $generi)) ?></p>
                        <p class="stelle"><?= renderStelle($opera["valutazione"]) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <script src="../js/lista.js"></script>
    </body>
</html>