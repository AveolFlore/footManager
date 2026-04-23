CREATE TABLE activite_log (
    id INT AUTO_INCREMENT PRIMARY KEY,

    auteur_id INT,

    type_action VARCHAR(100),

    description TEXT,

    lien VARCHAR(255) NULL,

    date_action DATETIME,

    CONSTRAINT fk_log_auteur
        FOREIGN KEY (auteur_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);