function cambiaTipo() {
    const tipo = document.getElementById("tipo").value;
    const labelCreatore = document.getElementById("labelCreatore");
    const generoSelect = document.getElementById("genere");
    const bloccoSegnalibro = document.getElementById("blocco_segnalibro");
    const segnalibro = document.getElementById("segnalibro");
    const labelSegnalibro = document.getElementById("labelSegnalibro");
    const datiSerie = document.getElementById("datiSerie");
    const stagione = document.getElementById("stagione");
    const episodio = document.getElementById("episodio");

    // Aggiornamento delle opzioni del genere in base al tipo scelto
    generoSelect.innerHTML = "";
    GENERI[tipo].forEach(g => {
        const option = document.createElement("option");
        option.value = g;
        option.textContent = g;
        generoSelect.appendChild(option);
    });

    // Etichetta creatore
    labelCreatore.textContent = (tipo === "film" || tipo === "serie_tv") ? "Regista:" : "Autore:";

    // Segnalibro
    if (tipo === "film") {
        bloccoSegnalibro.style.display = "none";    // nasconde etichetta + campo insieme
        segnalibro.disabled = true;
    } else if (tipo === "serie_tv") {
        bloccoSegnalibro.style.display = "none";
        segnalibro.disabled = true;
    } else {
        bloccoSegnalibro.style.display = "block";
        segnalibro.disabled = false;
        labelSegnalibro.textContent = tipo === "libro" ? "Pagina:" : "Capitolo:";
    }

    // Stagione e episodio solo per le serie TV
    if (tipo === "serie_tv") {
        datiSerie.style.display = "block";
        stagione.disabled = false;
        episodio.disabled = false;
    } else {
        datiSerie.style.display = "none";
        stagione.disabled = true;
        episodio.disabled = true;
        stagione.value = "";
        episodio.value = "";
    }
}

// Stelle di valutazione + anteprima hover
const stelleContainer = document.getElementById("stelle");
const valutazioneInput = document.getElementById("valutazione");
const stelle = document.querySelectorAll(".stella");

function aggiornaStelle(valore) {
    stelle.forEach(s => {
        s.textContent = parseInt(s.dataset.valore) <= valore ? "\u2605" : "\u2606"; // Rispettivamente ★ e ☆
    });
}

stelle.forEach(stella => {
    stella.addEventListener("click", () => {
        const valore = parseInt(stella.dataset.valore);
        valutazioneInput.value = valore;
        aggiornaStelle(valore);
    });
    stella.addEventListener("mouseenter", () => {
        aggiornaStelle(parseInt(stella.dataset.valore));
    });
});

stelleContainer.addEventListener("mouseleave", () => {
    aggiornaStelle(parseInt(valutazioneInput.value)); // torna al valore scelto, non a zero
});

// Stampa tutti i possibili generi e roba per il secondo e terzo genere
function popolaGeneri(tipo) {
    ["genere_1", "genere_2", "genere_3"].forEach((idSelect, indice) => {
        const select = document.getElementById(idSelect);
        select.innerHTML = "";
        if (indice > 0) {
            const nessuno = document.createElement("option");
            nessuno.value = "";
            nessuno.textContent = "-- Nessuno --";
            select.appendChild(nessuno);
        }
        GENERI[tipo].forEach(g => {
            const option = document.createElement("option");
            option.value = g;
            option.textContent = g;
            select.appendChild(option);
        });
    });
}

document.getElementById("tipo").addEventListener("change", cambiaTipo);
document.addEventListener("DOMContentLoaded", cambiaTipo); // stato iniziale coerente al caricamento