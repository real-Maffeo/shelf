CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(32) UNIQUE NOT NULL,
    password_hash VARCHAR(256) NOT NULL
);

CREATE TABLE opere (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    tipo ENUM('film','libro','fumetto','serie_tv') NOT NULL,
    titolo VARCHAR(64) NOT NULL,
    creatore VARCHAR(64),   -- Regista, autore, o ideatore, a seconda del tipo
    genere_1 VARCHAR(16) NOT NULL,
    genere_2 VARCHAR(16) DEFAULT NULL,
    genere_3 VARCHAR(16) DEFAULT NULL,
    copertina_url VARCHAR(256) DEFAULT NULL,
    valutazione TINYINT UNSIGNED NOT NULL,  -- 1-5, controllato lato PHP
    descrizione TEXT,
    segnalibro VARCHAR(32) DEFAULT NULL,    -- NULL per tipo='film'
    preferito BOOLEAN DEFAULT FALSE,
    data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id)
);

/*
il campo creatore e' opzionale perche' potrei voler segnare un film che ho visto 
senza ricordarmi chi era il regista, o un fumetto senza sapere l'autore a memoria, 
soprattutto quelli stranieri — bloccare l'inserimento per questo sarebbe una frizione inutile
*/