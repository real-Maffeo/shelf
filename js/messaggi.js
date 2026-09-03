function mostraParametro(nome) {
    const valore = new URLSearchParams(window.location.search).get(nome);
    if (valore) {
        const div = document.getElementById(nome === "successo" ? "successo" : "errore");
        if (div) div.textContent = valore;
    }
}
mostraParametro("errore");
mostraParametro("successo");