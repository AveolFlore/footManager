CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,

    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,

    telephone VARCHAR(20),
    photo_profil VARCHAR(255),

    date_naissance DATE NOT NULL,

    poste ENUM('gard', 'def', 'mil', 'att'),

    pied_dominant ENUM('droit', 'gauche', '2'),

    numero_maillot INT UNIQUE NULL,

    role ENUM(
        'admin',
        'president',
        'joueur',
        'censeur',
        'medecin',
        'organisateur',
        'entraineur'
    ) NOT NULL,

    equipe_id INT NULL,

    statut ENUM(
        'en_attente',
        'valide',
        'refuse'
    ) DEFAULT 'en_attente',

    date_inscription DATE,

    CONSTRAINT fk_users_equipe
        FOREIGN KEY (equipe_id)
        REFERENCES equipe(id)
        ON DELETE SET NULL
);