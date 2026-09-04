// Tema scuro/chiaro
if (localStorage.getItem("tema") === "scuro") {
    document.body.classList.add("scuro");
}

function aggiornaIconaTema() {
    const bottone = document.getElementById("toggle-tema");
    if (bottone) {
        bottone.textContent = document.body.classList.contains("scuro") ? "☀️" : "🌙";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    aggiornaIconaTema();
    const bottoneTema = document.getElementById("toggle-tema");
    if (bottoneTema) {
        bottoneTema.addEventListener("click", () => {
            document.body.classList.toggle("scuro");
            localStorage.setItem("tema", document.body.classList.contains("scuro") ? "scuro" : "chiaro");
            aggiornaIconaTema();
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