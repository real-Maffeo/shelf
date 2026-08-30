// js/script.js
const errore = new URLSearchParams(window.location.search).get("errore");
if (errore) {
    const div = document.getElementById("errore");
    if (div) div.textContent = errore;
}