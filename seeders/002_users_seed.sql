-- Nettoyage pour éviter les erreurs de doublons
DELETE FROM users;

-- ==========================================================
-- 1. LE PRÉSIDENT ET LE COACH
-- ==========================================================
INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, date_naissance, poste, pied_dominant, numero_maillot, role, equipe_id, statut, date_inscription) VALUES
('Famous', 'President', 'famous@gmail.com', '$2y$10$WNBKLaqa/3ZkxAaR9KEo8OLD/V0NcD77YRYcF9yWofxBYXroVTgi2', '90000000', '1975-01-01', NULL, NULL, NULL, 'president', NULL, 'valide', CURDATE()),
('Zidane', 'Zinedine', 'coach@example.com', '$2y$10$WNBKLaqa/3ZkxAaR9KEo8OLD/V0NcD77YRYcF9yWofxBYXroVTgi2', '91000000', '1972-06-23', NULL, NULL, NULL, 'entraineur', 1, 'valide', CURDATE());

-- ==========================================================
-- 2. ÉQUIPE A (8 joueurs - equipe_id = 1)
-- ==========================================================
INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, date_naissance, poste, pied_dominant, numero_maillot, role, equipe_id, statut, date_inscription) VALUES
('Lloris', 'Hugo', 'hugo.a@example.com', '$2y$10$hashedpassword', '92000001', '1986-12-26', 'gard', 'gauche', 1, 'joueur', 1, 'valide', CURDATE()),
('Varane', 'Raphael', 'raph.a@example.com', '$2y$10$hashedpassword', '92000002', '1993-04-25', 'def', 'droit', 4, 'joueur', 1, 'valide', CURDATE()),
('Hernandez', 'Theo', 'theo.a@example.com', '$2y$10$hashedpassword', '92000003', '1997-10-06', 'def', 'gauche', 22, 'joueur', 1, 'valide', CURDATE()),
('Pogba', 'Paul', 'paul.a@example.com', '$2y$10$hashedpassword', '92000004', '1993-03-15', 'mil', 'droit', 6, 'joueur', 1, 'valide', CURDATE()),
('Kante', 'Ngolo', 'ngolo.a@example.com', '$2y$10$hashedpassword', '92000005', '1991-03-29', 'mil', 'droit', 13, 'joueur', 1, 'valide', CURDATE()),
('Griezmann', 'Antoine', 'anto.a@example.com', '$2y$10$hashedpassword', '92000006', '1991-03-21', 'att', 'gauche', 7, 'joueur', 1, 'valide', CURDATE()),
('Mbappe', 'Kylian', 'kyk.a@example.com', '$2y$10$hashedpassword', '92000007', '1998-12-20', 'att', 'droit', 10, 'joueur', 1, 'valide', CURDATE()),
('Giroud', 'Olivier', 'olive.a@example.com', '$2y$10$hashedpassword', '92000008', '1986-09-30', 'att', 'gauche', 9, 'joueur', 1, 'valide', CURDATE());

-- ==========================================================
-- 3. ÉQUIPE B (8 joueurs - equipe_id = 2)
-- ==========================================================
INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, date_naissance, poste, pied_dominant, numero_maillot, role, equipe_id, statut, date_inscription) VALUES
('Maignan', 'Mike', 'mike.b@example.com', '$2y$10$hashedpassword', '93000001', '1995-07-03', 'gard', 'droit', 16, 'joueur', 2, 'valide', CURDATE()),
('Kounde', 'Jules', 'jules.b@example.com', '$2y$10$hashedpassword', '93000002', '1998-11-12', 'def', 'droit', 5, 'joueur', 2, 'valide', CURDATE()),
('Konate', 'Ibou', 'ibou.b@example.com', '$2y$10$hashedpassword', '93000003', '1999-05-25', 'def', 'droit', 24, 'joueur', 2, 'valide', CURDATE()),
('Tchouameni', 'Aurelien', 'aurel.b@example.com', '$2y$10$hashedpassword', '93000004', '2000-01-27', 'mil', 'droit', 8, 'joueur', 2, 'valide', CURDATE()),
('Camavinga', 'Eduardo', 'edu.b@example.com', '$2y$10$hashedpassword', '93000005', '2002-11-10', 'mil', 'gauche', 25, 'joueur', 2, 'valide', CURDATE()),
('Dembele', 'Ousmane', 'ous.b@example.com', '$2y$10$hashedpassword', '93000006', '1997-05-15', 'att', 'droit', 11, 'joueur', 2, 'valide', CURDATE()),
('Kolo', 'Randal', 'ran.b@example.com', '$2y$10$hashedpassword', '93000007', '1998-12-05', 'att', 'droit', 12, 'joueur', 2, 'valide', CURDATE()),
('Coman', 'Kingsley', 'king.b@example.com', '$2y$10$hashedpassword', '93000008', '1996-06-13', 'att', 'droit', 20, 'joueur', 2, 'valide', CURDATE());

-- ==========================================================
-- 4. JOUEURS EN ATTENTE (5 joueurs)
-- ==========================================================
INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, date_naissance, poste, pied_dominant, numero_maillot, role, equipe_id, statut, date_inscription) VALUES
('Petit', 'Jean', 'wait1@example.com', '$2y$10$hashedpassword', '94000001', '2005-02-10', 'mil', 'droit', NULL, 'joueur', NULL, 'en_attente', CURDATE()),
('Grand', 'Paul', 'wait2@example.com', '$2y$10$hashedpassword', '94000002', '2004-05-12', 'def', 'gauche', NULL, 'joueur', NULL, 'en_attente', CURDATE()),
('Leger', 'Marc', 'wait3@example.com', '$2y$10$hashedpassword', '94000003', '2006-11-20', 'att', 'droit', NULL, 'joueur', NULL, 'en_attente', CURDATE()),
('Vif', 'Alain', 'wait4@example.com', '$2y$10$hashedpassword', '94000004', '2005-08-15', 'gard', 'droit', NULL, 'joueur', NULL, 'en_attente', CURDATE()),
('Fort', 'Thomas', 'wait5@example.com', '$2y$10$hashedpassword', '94000005', '2003-01-30', 'def', 'droit', NULL, 'joueur', NULL, 'en_attente', CURDATE());
    'mil',
    'gauche',
    10,
    'president',
    1,
    'valide',
    CURDATE()
);
>>>>>>> bcaca1d40763a14b084b94f1e5b4b30ed20af408
