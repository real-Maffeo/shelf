<?php
    require_once "controllo_sessione.php";
    require_once "dbaccess.php";
    require_once "generi.php";
    require_once "validazione_opera.php";

    $id = filter_var($_GET["id"] ?? "", FILTER_VALIDATE_INT);
    $utente_id = $_SESSION["utente_id"];

    if ($id === false) {
        header("Location: lista.php?avviso=" . urlencode("Opera non trovata!"));
        exit;
    }

    $stmt = $mysqli->prepare("SELECT * FROM opere WHERE id = ? AND utente_id = ?");
    $stmt->bind_param("ii", $id, $utente_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        header("Location: lista.php?avviso=" . urlencode("Opera non trovata!"));
        exit;
    }

    $opera = $res->fetch_assoc();
    $generi = array_filter([$opera["genere_1"], $opera["genere_2"], $opera["genere_3"]]);
    $etichette = ["film" => "Film", "libro" => "Libro", "fumetto" => "Fumetto", "serie_tv" => "Serie TV"];
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shelf - <?= htmlspecialchars($opera["titolo"]) ?></title>
        <link href="../css/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php if (isset($_GET["messaggio"])): ?>
            <div id="successo"><?= isset($_GET["successo"]) ? htmlspecialchars($_GET["successo"]) : "" ?></div>
            <div id="avviso"><?= isset($_GET["avviso"]) ? htmlspecialchars($_GET["avviso"]) : "" ?></div>
        <?php endif; ?>

        <a href="lista.php?tipo=<?= urlencode($opera["tipo"]) ?>">&larr; Torna alla lista</a>

        <div id="dettaglio">
            <img src="<?= htmlspecialchars($opera["copertina_url"] ?: "../images/placeholder_{$opera['tipo']}.svg") ?>"
                 onerror="this.src='../images/placeholder_<?= htmlspecialchars($opera['tipo']) ?>.svg'"
                 alt="Copertina di <?= htmlspecialchars($opera["titolo"]) ?>">

            <h1><?= htmlspecialchars($opera["titolo"]) ?></h1>
            <p class="categoria"><?= htmlspecialchars($etichette[$opera["tipo"]]) ?></p>

            <?php if ($opera["creatore"]): ?>
                <p class="creatore"><?= htmlspecialchars($opera["creatore"]) ?></p>
            <?php endif; ?>

            <p class="generi"><?= htmlspecialchars(implode(", ", $generi)) ?></p>
            <p class="stelle"><?= renderStelle($opera["valutazione"]) ?></p>

            <?php if ($opera["segnalibro"]): ?>
                <p class="segnalibro">Segnalibro: <?= htmlspecialchars($opera["segnalibro"]) ?></p>
            <?php endif; ?>

            <?php if ($opera["descrizione"]): ?>
                <p class="descrizione"><?= nl2br(htmlspecialchars($opera["descrizione"])) ?></p>
            <?php endif; ?>

            <div class="azioni">
                <a href="modifica.php?id=<?= (int)$opera['id'] ?>">Modifica</a>

                <form action="elimina.php" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo elemento?');">
                    <input type="hidden" name="id" value="<?= (int)$opera['id'] ?>">
                    <button type="submit">Elimina</button>
                </form>
            </div>
        </div>
        <script src="../js/ui.js"></script>
    </body>
</html>