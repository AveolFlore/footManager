CREATE TABLE match_seance (
    id INT AUTO_INCREMENT PRIMARY KEY,

    type ENUM('match', 'entr') NOT NULL,

    date DATE NOT NULL,

    lieu VARCHAR(200),

    description TEXT,

    statut ENUM(
        'planifie',
        'publie',
        'termine'
    ) DEFAULT 'planifie',

    createur_id INT,

    CONSTRAINT fk_match_createur
        FOREIGN KEY (createur_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);