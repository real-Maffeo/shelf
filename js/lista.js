function filtra() {
    const generiSelezionati = Array.from(document.querySelectorAll(".filtro-genere-cb:checked")).map(cb => cb.value);
    const valutazioneMinima = parseInt(document.getElementById("filtro-slider").value);
    const soloPreferiti = document.getElementById("filtro-preferiti").checked;
    const ricerca = document.getElementById("filtro-titolo").value.trim().toLowerCase();

    document.querySelectorAll(".card").forEach(card => {
        const generiCard = card.dataset.genere.split("|");
        const generiOk = generiSelezionati.length === 0 || generiSelezionati.some(g => generiCard.includes(g));
        const valutazioneOk = parseInt(card.dataset.valutazione) >= valutazioneMinima;
        const preferitiOk = !soloPreferiti || card.dataset.preferito === "1";
        const ricercaOk = ricerca === "" || card.dataset.titolo.includes(ricerca);

        card.style.display = (generiOk && valutazioneOk && preferitiOk && ricercaOk) ? "" : "none";
    });
}

document.querySelectorAll(".filtro-genere-cb").forEach(cb => cb.addEventListener("change", filtra));
document.getElementById("filtro-preferiti").addEventListener("change", filtra);
document.getElementById("filtro-titolo").addEventListener("input", filtra);

const slider = document.getElementById("filtro-slider");
const sliderValore = document.getElementById("filtro-slider-valore");
slider.addEventListener("input", () => {
    sliderValore.textContent = slider.value;
    filtra();
});

document.querySelectorAll(".cuore").forEach(bottone => {
    bottone.addEventListener("click", async () => {
        const id = bottone.dataset.id;
        try {
            const risposta = await fetch("../php/preferito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + encodeURIComponent(id)
            });
            if (!risposta.ok) throw new Error("Richiesta fallita");
            const dati = await risposta.json();

            const card = bottone.closest(".card");
            card.dataset.preferito = dati.preferito ? "1" : "0";
            bottone.innerHTML = dati.preferito ? "&#9829;" : "&#9825;";
            filtra(); // riapplica subito il filtro "solo preferiti", se attivo
        } catch (err) {
            console.error("Errore aggiornamento preferito:", err);
        }
    });
});