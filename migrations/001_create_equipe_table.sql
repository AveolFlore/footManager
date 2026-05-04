CREATE TABLE IF NOT EXISTS equipe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    couleur VARCHAR(7),
    categorie ENUM('senior', 'reserve', 'jeune') NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATE
);
