CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,

    destinataire_id INT NOT NULL,

    type ENUM(
        'match_publie',
        'sanction',
        'vote',
        'reglement',
        'cotisation'
    ),

    message TEXT,

    lien VARCHAR(255),

    lu BOOLEAN DEFAULT FALSE,

    date_creation DATETIME,

    CONSTRAINT fk_notif_user
        FOREIGN KEY (destinataire_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);