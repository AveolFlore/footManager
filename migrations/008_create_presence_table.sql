CREATE TABLE presence (
    id INT AUTO_INCREMENT PRIMARY KEY,

    seance_id INT NOT NULL,
    joueur_id INT NOT NULL,

    type_presence ENUM(
        'present',
        'absent',
        'retard',
        'excuse'
    ) NOT NULL,

    marque_par INT,

    date_marquage DATE,

    note VARCHAR(200) NULL,

    CONSTRAINT fk_presence_seance
        FOREIGN KEY (seance_id)
        REFERENCES match_seance(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_presence_joueur
        FOREIGN KEY (joueur_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_presence_marqueur
        FOREIGN KEY (marque_par)
        REFERENCES users(id)
        ON DELETE SET NULL
);