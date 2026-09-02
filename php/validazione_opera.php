<?php
    // Usato da aggiungi.php e modifica.php

    // Controllo correttezza dati
    function validaOpera($tipo, $titolo, $creatore, $genere1, $genere2, $genere3, $valutazione, $descrizione, $copertina_url, $TIPI_VALIDI) {
        if (!in_array($tipo, $TIPI_VALIDI, true)) {
            return "Categoria non valida!";
        }
        if (!in_array($genere1, GENERI[$tipo], true)) {
            return "Il genere principale non è valido per questa categoria!";
        }
        if ($genere2 !== "" && !in_array($genere2, GENERI[$tipo], true)) {
            return "Il secondo genere non è valido per questa categoria!";
        }
        if ($genere3 !== "" && !in_array($genere3, GENERI[$tipo], true)) {
            return "Il terzo genere non è valido per questa categoria!";
        }
        $generiScelti = array_filter([$genere1, $genere2, $genere3], fn($g) => $g !== "");
        if (count($generiScelti) !== count(array_unique($generiScelti))) {
            return "Non puoi selezionare lo stesso genere più volte!";
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
        if ($copertina_url !== "" && !filter_var($copertina_url, FILTER_VALIDATE_URL)) {
            return "Il link della copertina non è valido!";
        }
        if (strlen($segnalibro) > 32) {
            return "Il segnalibro è troppo lungo (max 32 caratteri)!";
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

    function renderStelle($valutazione) {
        $stelle = "";
        for ($i = 1; $i <= 5; $i++) {
            $stelle .= $i <= $valutazione ? "&#9733;" : "&#9734;";
        }
        return $stelle;
    }
?>