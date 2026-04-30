CREATE TABLE categorie_tache (
    id INT AUTO_INCREMENT PRIMARY KEY,
 
    nom VARCHAR(100) NOT NULL,
 
    icone VARCHAR(10),
 
    description TEXT,
 
    couleur VARCHAR(7) DEFAULT '#0d9488',
 
    actif BOOLEAN DEFAULT TRUE
);