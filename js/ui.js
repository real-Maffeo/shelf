// Tema scuro: applica subito la preferenza salvata, prima che la pagina sia visibile
if (localStorage.getItem("tema") === "scuro") {
    document.body.classList.add("scuro");
}

document.addEventListener("DOMContentLoaded", () => {
    const bottoneTema = document.getElementById("toggle-tema");
    if (bottoneTema) {
        bottoneTema.addEventListener("click", () => {
            document.body.classList.toggle("scuro");
            localStorage.setItem("tema", document.body.classList.contains("scuro") ? "scuro" : "chiaro");
        });
    }

    // Messaggi a scomparsa automatica dopo qualche secondo
    ["errore", "successo", "avviso"].forEach(id => {
        const div = document.getElementById(id);
        if (div && div.textContent.trim() !== "") {
            setTimeout(() => {
                div.style.transition = "opacity 0.5s";
                div.style.opacity = "0";
                setTimeout(() => { div.style.display = "none"; }, 500);
            }, 4000);
        }
    });
});