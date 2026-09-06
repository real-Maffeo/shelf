// Condiviso tra aggiungi.php e modifica.php - unica differenza: in modifica.php sono
// gia' definite GENERE_1_ATTUALE/GENERE_2_ATTUALE/GENERE_3_ATTUALE/VALUTAZIONE_ATTUALE
// per pre-selezionare i valori esistenti.
function popolaGeneri(tipo) {
    const attuali = {
        genere_1: (typeof GENERE_1_ATTUALE !== "undefined") ? GENERE_1_ATTUALE : null,
        genere_2: (typeof GENERE_2_ATTUALE !== "undefined") ? GENERE_2_ATTUALE : null,
        genere_3: (typeof GENERE_3_ATTUALE !== "undefined") ? GENERE_3_ATTUALE : null,
    };

    ["genere_1", "genere_2", "genere_3"].forEach(idSelect => {
        const select = document.getElementById(idSelect);
        select.innerHTML = "";
        if (idSelect !== "genere_1") {
            const nessuno = document.createElement("option");
            nessuno.value = "";
            nessuno.textContent = "-- Nessuno --";
            select.appendChild(nessuno);
        }
        GENERI[tipo].forEach(g => {
            const option = document.createElement("option");
            option.value = g;
            option.textContent = g;
            if (g === attuali[idSelect]) option.selected = true;
            select.appendChild(option);
        });
    });
}

function cambiaTipo() {
    const tipo = document.getElementById("tipo").value;
    const labelCreatore = document.getElementById("labelCreatore");
    const bloccoSegnalibro = document.getElementById("blocco_segnalibro");
    const segnalibro = document.getElementById("segnalibro");
    const labelSegnalibro = document.getElementById("labelSegnalibro");
    const datiSerie = document.getElementById("datiSerie");
    const stagione = document.getElementById("stagione");
    const episodio = document.getElementById("episodio");

    popolaGeneri(tipo);
    labelCreatore.textContent = (tipo === "film" || tipo === "serie_tv") ? "Regista:" : "Autore:";

    if (tipo === "film" || tipo === "serie_tv") {
        bloccoSegnalibro.style.display = "none";
        segnalibro.disabled = true;
    } else {
        bloccoSegnalibro.style.display = "block";
        segnalibro.disabled = false;
        labelSegnalibro.textContent = tipo === "libro" ? "Pagina:" : "Capitolo:";
    }

    if (tipo === "serie_tv") {
        datiSerie.style.display = "block";
        stagione.disabled = false;
        episodio.disabled = false;
    } else {
        datiSerie.style.display = "none";
        stagione.disabled = true;
        episodio.disabled = true;
    }
}

// Contatore caratteri commento
const descrizione = document.getElementById("commento");
const contatore = document.getElementById("contatore-descrizione");
if (descrizione && contatore) {
    contatore.textContent = "${descrizione.value.length}/512";
    descrizione.addEventListener("input", () => {
        contatore.textContent = "${descrizione.value.length}/512";
    });
}

const valutazioneInput = document.getElementById("valutazione");
const stelle = document.querySelectorAll(".stella");

// https://symbl.cc/en/unicode-table/#geometric-shapes per i caratteri unicode
function aggiornaStelle(valore) {
    stelle.forEach(s => {
        s.textContent = parseInt(s.dataset.valore) <= valore ? "\u2605" : "\u2606";
    });
}

// Stelle dinamiche
stelle.forEach(stella => {
    stella.addEventListener("click", () => {
        const valore = parseInt(stella.dataset.valore);
        valutazioneInput.value = valore;
        aggiornaStelle(valore);
    });
    stella.addEventListener("mouseenter", () => aggiornaStelle(parseInt(stella.dataset.valore)));
});
document.getElementById("stelle").addEventListener("mouseleave", () => aggiornaStelle(parseInt(valutazioneInput.value)));

document.getElementById("tipo").addEventListener("change", cambiaTipo);
document.addEventListener("DOMContentLoaded", () => {
    cambiaTipo();
    if (typeof VALUTAZIONE_ATTUALE !== "undefined") {
        aggiornaStelle(VALUTAZIONE_ATTUALE);
        valutazioneInput.value = VALUTAZIONE_ATTUALE;
    }
});