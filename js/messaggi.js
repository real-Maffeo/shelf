// Condiviso tra index.html e registrazione.html
function mostraParametro(nome, idDiv) {
    const valore = new URLSearchParams(window.location.search).get(nome);
    if (valore) {
        const div = document.createElement("div");
        div.id = idDiv;
        div.textContent = valore;
        document.body.prepend(div);
    }
}
document.addEventListener("DOMContentLoaded", () => {
    mostraParametro("errore", "errore");
    mostraParametro("successo", "successo");
});