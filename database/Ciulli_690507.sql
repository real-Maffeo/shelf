CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE opere (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    tipo ENUM('film','libro','fumetto','serie_tv') NOT NULL,
    titolo VARCHAR(150) NOT NULL,
    creatore VARCHAR(150),                -- regista, autore, o ideatore, a seconda del tipo
    genere VARCHAR(50) NOT NULL,
    copertina_url VARCHAR(500) DEFAULT NULL,
    valutazione TINYINT UNSIGNED NOT NULL, -- 1-5, controllato lato PHP
    descrizione TEXT,
    segnalibro VARCHAR(50) DEFAULT NULL,   -- NULL per tipo='film'
    preferito BOOLEAN DEFAULT FALSE,
    data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id)
);