function cambiaTipo() {
    const tipo = document.getElementById("tipo").value;
    const labelCreatore = document.getElementById("labelCreatore");
    const creatore = document.getElementById("creatore");
    const generoSelect = document.getElementById("genere");
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
    if (tipo === "film" || tipo === "serie_tv") {
        labelCreatore.textContent = "Regista:";
    } else {
        labelCreatore.textContent = "Autore:";
    }

    // Segnalibro
    if (tipo === "film") {
        segnalibro.disabled = true;
        segnalibro.value = "";
    } else if (tipo === "libro") {
        labelSegnalibro.textContent = "Pagina:";
        segnalibro.disabled = false;
    } else if (tipo === "fumetto") {
        labelSegnalibro.textContent = "Capitolo:";
        segnalibro.disabled = false;
    } else {
        segnalibro.disabled = false;
    }

    // Stagione e episodio solo per le serie TV
    if (tipo === "serie_tv") {
        datiSerie.style.display = "block";
        stagione.disabled = false;
        episodio.disabled = false;
        stagione.required = true;
        episodio.required = true;
        segnalibro.style.display = "none";
    } else {
        datiSerie.style.display = "none";
        stagione.disabled = true;
        episodio.disabled = true;
        stagione.required = false;
        episodio.required = false;
        stagione.value = "";
        episodio.value = "";
        segnalibro.style.display = "inline";
    }
}

// Stelle di valutazione
document.querySelectorAll(".stella").forEach(stella => {
    stella.addEventListener("click", () => {
        const valore = parseInt(stella.dataset.valore);
        document.getElementById("valutazione").value = valore;
        document.querySelectorAll(".stella").forEach(s => {
            s.textContent = parseInt(s.dataset.valore) <= valore ? "\u2605" : "\u2734";
        });
    });
});

document.getElementById("tipo").addEventListener("change", cambiaTipo);
document.addEventListener("DOMContentLoaded", cambiaTipo); // stato iniziale coerente al caricamento