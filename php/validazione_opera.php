<?php
    // Usato da aggiungi.php e modifica.php

    // Controllo correttezza dati
    function validaOpera($tipo, $titolo, $creatore, $genere, $valutazione, $descrizione, $copertina_url, $TIPI_VALIDI) {
        if (!in_array($tipo, $TIPI_VALIDI, true)) {
            return "Categoria non valida!";
        }
        if (!in_array($genere, GENERI[$tipo], true)) {            
            return "Genere non valido per questa categoria!";
        }
        if ($titolo === "" || strlen($titolo) > 64) {
            return "Titolo obbligatorio, massimo 64 caratteri!";
        }
        if (strlen($creatore) > 64) {
            return "Il nome del creatore è troppo lungo (max 64 caratteri)!";
        }
        if ($valutazione === false || $valutazione < 1 || $valutazione > 5) {
            return "Seleziona una valutazione da 1 a 5 stelle!";
        }
        if (strlen($descrizione) > 512) {
            return "Il commento è troppo lungo (max 512 caratteri)!";
        }
        if (strlen($segnalibro) > 32) {
            return "Il segnalibro è troppo lungo (max 32 caratteri)!";
        }
        if ($copertina_url !== "" && !filter_var($copertina_url, FILTER_VALIDATE_URL)) {
            return "Il link della copertina non è valido!";
        }
        return null;    // null se tutto valido
    }

    // Segnalibro personalizzato in base al tipo
    function costruisciSegnalibro($tipo, $segnalibro, $stagione, $episodio) {
        if ($tipo === "film") {
            return null;
        } elseif ($tipo === "serie_tv") {
            if ($stagione !== false && $episodio !== false) {
                return "Stagione {$stagione}, Episodio {$episodio}";
            }
            return null;
        }
        return $segnalibro !== "" ? $segnalibro : null;
    }
?>