  Club Manager — Documentation Complète & Réalité Footballistique Page 1
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
CLUB MANAGER
Système de gestion complet pour club de football
Documentation technique · MCD · Architecture · Plus-values · Réalité footballistique
Projet académique · April 2026
Accueil À propos
Cycle de
vie
Validation
joueur
Équipes &
MCD
Architect
ure
Plus-valu
es
Répartitio
n devs Contact
Bienvenue sur Club Manager
Club Manager est une application web PHP native conçue pour gérer un club de football de façon complète et
cohérente avec la réalité du terrain. Elle couvre l'intégralité du cycle de vie d'un club : de la validation d'un
joueur jusqu'au bilan de fin de saison, en passant par les entraînements hebdomadaires, les matchs, les
finances et la discipline.
 Organisation Joueurs,
équipes, matchs,
convocations, présences
centralisés.
 Performances Buts,
passes, classements,
récompenses calculés par
cycle hebdo.
 Finance Cotisations,
caisse, amendes
automatiques, exports PDF.
 Temps réel Notifications,
graphiques Chart.js, dark
mode, log activité.
8 devs 8 modules 15 tables SQL 10 plus-values 1 semaine
  Club Manager — Documentation Complète & Réalité Footballistique Page 2
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
À PROPOS DU PROJET
Contexte, objectifs et stack technologique
Contexte & Objectif
Projet développé par 8 étudiants en une semaine. L'architecture garantit l'indépendance totale de chaque
développeur : chaque module est un dossier autonome. 3 fichiers partagés seulement — database.php,
session.php, auth_check.php — permettent à chacun de travailler en local sans attendre les autres.
Principe fondateur : Aucun développeur ne dépend d'un autre pendant le développement. La fusion finale =
copier les dossiers ensemble.
Stack technologique
Technologie Rôle Détails
PHP 8+ natif Logique serveur PDO + prepared statements, pas de framework
MySQL 8 Base de données 15 tables, FK, contraintes d'intégrité
HTML5 Structure pages Formulaires sémantiques
Tailwind CSS CDN Design & dark mode Classes utilitaires, responsive, dark: prefix
Chart.js CDN Graphiques Barres, courbes, camemberts — plus-value 
FPDF Export PDF Rapports téléchargeables côté serveur — plus-value 
JavaScript ES6 Interactions Polling notifs, filtres live, dark toggle
Objectifs de l'application
 Organiser le club : joueurs, matchs, équipes, présences
 Suivre les performances : stats, classement, récompenses
 Gérer la discipline : règlements votés démocratiquement, sanctions automatiques
 Gérer l'argent : cotisations, caisse cohérente, exports PDF
 Offrir une expérience professionnelle : notifications, graphiques, dark mode
  Club Manager — Documentation Complète & Réalité Footballistique Page 3
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
CYCLE DE VIE RÉEL DU CLUB
La logique footballistique qui guide toute l'architecture
Le problème des projets académiques classiques
La plupart des projets partent du code — ils créent les tables, font les formulaires, et espèrent que ça ressemble
à un vrai système. La bonne logique est de partir du cycle de vie réel d'un club. Un club ne vit pas par modules
séparés, il vit par cycles.
Règle d'or : Le match n'est pas une entité isolée. C'est l'aboutissement d'une semaine entière. La convocation
dépend des présences. Les stats du match alimentent le classement. Le classement influence la prochaine
convocation. C'est une boucle, pas une liste de fonctionnalités.
Le cycle hebdomadaire — le plus important
Lundi Mardi–Jeudi Vendredi Samedi/Dim.
Bilan semaine précédente
Sanctions éventuelles
Cotisations rappel
Entraînement → présences
marquées → retards =
amende auto → perfs
enregistrées
Convocation publiée →
suggestion auto → joueurs
notifiés → équipes définies
Match joué → buts/passes
saisis → classement mis à
jour → bilan affiché
La chaîne de dépendances naturelles
Ton code doit respecter cet ordre — chaque étape ne peut exister sans la précédente :
Joueur validé Prérequis de tout. Avant validation, le joueur n'existe pas opérationnellement.
Séance planifiée Entraînement ou match créé par l'organisateur avec date et lieu.
Présence marquée Le bureau marque : présent / absent / retard / excusé.
Retard → amende auto Si type_presence = 'retard' et règlement actif → sanction créée automatiquement.
Performance enregistrée L'entraîneur saisit buts et passes. Lié à une séance ET un joueur validé.
Points calculés but × 3 + passe × 2 = points_total. Classement mis à jour.
Convocation suggérée Top 8 joueurs selon score = perf × 0.6 + présences × 0.4.
Match joué Résultat enregistré. Nouveau cycle commence le lundi suivant.
La logique financière réelle d'un club africain
La règle d'or : un seul endroit où l'argent entre dans la caisse — la table caisse. Les cotisations payées et les
amendes payées y créent des lignes automatiquement via le code, jamais manuellement.
Sources (entrées) Sorties courantes
Cotisations mensuelles (automatiques dès validation joueur) Eau pour l'entraînement
Amendes payées (retards, absences, comportements) Ballons, dossards, matériel
Dons occasionnels (saisie manuelle) Transport pour les matchs déplacés
Récompenses joueur du mois / de l'année
  Club Manager — Documentation Complète & Réalité Footballistique Page 4
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
VALIDATION D'UN JOUEUR — LOGIQUE COMPLÈTE
Le déclencheur qui active un joueur dans tout le système
Ce que validation signifie vraiment
Avant validation, un joueur n'existe pas opérationnellement dans le système. Il ne peut pas être convoqué,
présent, performant ou sanctionné. La validation est le déclencheur unique qui active tous ces droits.
Le parcours complet du nouveau joueur
Étape 1 : Inscription statut =
'en_attente' Accès page d'accueil uniquement. Le bureau reçoit une notification.
Étape 2 : Examen
bureau
Vérification des
infos
Nom complet, date de naissance, téléphone, photo, poste, pied dominant
— tout doit être renseigné.
Étape 3a :
ACCEPTÉ statut = 'valide' Affectation à une équipe. Cotisation du mois générée auto. Notification
envoyée. Accès complet.
Étape 3b : REFUSÉ statut = 'refuse' Accès bloqué définitivement. Notification avec motif envoyée au joueur.
Champs obligatoires avant validation (table utilisateur)
Champ Type SQL Pourquoi obligatoire
nom, prenom VARCHAR(100) NOT NULL Identité — affiché partout
date_naissance DATE NOT NULL Vérification âge minimum du club
telephone VARCHAR(20) NOT NULL Contact d'urgence
photo_profil VARCHAR(255) NOT NULL Identité visuelle, fiche 360°
poste ENUM gardien|def|mil|att Calcul de stats par poste (gardien ≠ attaquant)
pied_dominant ENUM droit|gauche|les_deux Info utile pour composer les équipes
numero_maillot INT UNIQUE NULL Assigné après validation, unique dans le club
equipe_id FK → equipe NULL Affecté lors de la validation
Ce que la validation débloque — PHP
function valider_joueur($pdo, $joueur_id, $equipe_id, $validateur_id) {
// 1. Changer le statut et affecter l'équipe
$pdo->prepare("UPDATE utilisateur SET statut='valide', equipe_id=? WHERE id=?")->execute([$equipe_id, $joueur_id]);
// 2. Générer la cotisation du mois en cours AUTOMATIQUEMENT
$montant = get_montant_cotisation($pdo);
$pdo->prepare("INSERT INTO cotisation (joueur_id, montant, mois, annee, statut)
VALUES (?, ?, ?, ?, 'non_paye')")->execute([$joueur_id, $montant, date('m'), date('Y')]);
// 3. Enregistrer dans l'historique d'équipe
$pdo->prepare("INSERT INTO historique_equipe (joueur_id, equipe_id, date_entree, modifie_par)
VALUES (?, ?, CURDATE(), ?)")->execute([$joueur_id, $equipe_id, $validateur_id]);
// 4. Notifier le joueur
creer_notification($pdo, $joueur_id, 'validation',
  Club Manager — Documentation Complète & Réalité Footballistique
'Félicitations ! Ton compte a été validé. Bienvenue dans le club.');
// 5. Logger l'action
log_activite($pdo, $validateur_id, 'joueur_valide',
"Joueur #$joueur_id validé → équipe #$equipe_id");
}
Ce qui est bloqué pour un joueur non validé
 Être convoqué pour un match
 Apparaître dans la feuille de présence
 Voter sur un règlement
 Avoir des stats enregistrées
 Recevoir une cotisation (elle n'est pas encore générée)
 Recevoir une sanction officielle
 Apparaître dans le classement
Page 5
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
  Club Manager — Documentation Complète & Réalité Footballistique Page 6
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
GESTION DES ÉQUIPES — TABLE EQUIPE OBLIGATOIRE
Pourquoi un ENUM A/B ne suffit pas
Oui, un club peut avoir plusieurs équipes
Même dans un petit club amateur, il existe au minimum deux équipes pour les matchs internes. Un club
structuré en a trois ou plus. La table EQUIPE est indispensable pour une architecture solide.
Comparaison des deux approches
ENUM 'A','B' dans utilisateur TABLE equipe séparée ← recommandée
Noms personnalisés Non Oui — 'Aigle FC', 'Réserve', 'U18'
3ème équipe ou plus Impossible sans modifier le code Ajouter une ligne en base suffit
Historique des transferts Aucun Table historique_equipe
Couleur d'équipe Non Champ couleur → affichage visuel
Catégorie (senior/U18) Non Champ categorie
Stats par équipe Difficile Naturel via FK
SQL complet des 3 tables liées aux équipes
Table EQUIPE :
CREATE TABLE equipe (
id INT PRIMARY KEY AUTO_INCREMENT,
nom VARCHAR(100) NOT NULL, -- "Équipe A", "Réserve", "U18"
couleur VARCHAR(7) DEFAULT '#16a34a', -- code hex pour affichage visuel
categorie ENUM('senior','reserve','jeune') DEFAULT 'senior',
actif BOOLEAN DEFAULT 1,
date_creation DATE DEFAULT (CURRENT_DATE)
);-- Données initiales
INSERT INTO equipe (nom, couleur) VALUES ('Équipe A','#16a34a'),('Équipe B','#0ea5e9');
Table HISTORIQUE_EQUIPE :
CREATE TABLE historique_equipe (
id INT PRIMARY KEY AUTO_INCREMENT,
joueur_id INT NOT NULL,
equipe_id INT NOT NULL,
date_entree DATE NOT NULL,
date_sortie DATE NULL, -- NULL = membre actuel de cette équipe
motif VARCHAR(200), -- "Transfert", "Rééquilibrage effectif"
modifie_par INT NOT NULL, -- ID du membre bureau qui a fait le changement
FOREIGN KEY (joueur_id) REFERENCES utilisateur(id),
FOREIGN KEY (equipe_id) REFERENCES equipe(id),
FOREIGN KEY (modifie_par) REFERENCES utilisateur(id)
);
Équipe dans une convocation (indépendante de l'appartenance permanente) :-- Dans la table CONVOCATION :
equipe_match ENUM('A','B') NOT NULL, -- équipe pour CE match uniquement-- L'équipe_match peut différer de l'equipe_id permanent du joueur-- Cela donne la flexibilité de composer les équipes librement pour chaque match
UNIQUE KEY uq_match_joueur (match_id, joueur_id) -- un joueur = une équipe par match
Table RESULTAT_MATCH — souvent oubliée
  Club Manager — Documentation Complète & Réalité Footballistique
CREATE TABLE resultat_match (
id INT PRIMARY KEY AUTO_INCREMENT,
match_id INT NOT NULL UNIQUE,
buts_equipe_a INT DEFAULT 0,
buts_equipe_b INT DEFAULT 0,
equipe_gagnante ENUM('A','B','nul') GENERATED ALWAYS AS (
CASE WHEN buts_equipe_a > buts_equipe_b THEN 'A'
WHEN buts_equipe_b > buts_equipe_a THEN 'B'
ELSE 'nul' END
) STORED, -- calculé automatiquement par MySQL
saisie_par INT NOT NULL,
FOREIGN KEY (match_id) REFERENCES match_seance(id),
FOREIGN KEY (saisie_par) REFERENCES utilisateur(id)
);
Page 7
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
  Club Manager — Documentation Complète & Réalité Footballistique Page 8
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
MODÈLE CONCEPTUEL DE DONNÉES — MCD COMPLET
15 entités basées sur la réalité footballistique
Chaque entité correspond à une table MySQL. Les étoiles ( ) indiquent les tables ajoutées pour les plus-values
ou la réalité footballistique. Les FK sont toutes indexées pour les performances.
 UTILISATEUR
Champ Type SQL Remarque
id INT PK AUTO_INCREMENT Identifiant unique
nom, prenom VARCHAR(100) NOT NULL Identité
email VARCHAR(150) UNIQUE Login
mot_de_passe VARCHAR(255) Hash bcrypt
telephone VARCHAR(20) Contact
photo_profil VARCHAR(255) Chemin fichier
date_naissance DATE NOT NULL Vérification âge
poste ENUM gard|def|mil|att Pour stats par poste
pied_dominant ENUM droit|gauche|2 Info composition
numero_maillot INT UNIQUE NULL Assigné à la validation
role ENUM 5 valeurs joueur|president|censeur|organisateur|entraineur
equipe_id FK → equipe NULL Appartenance permanente
statut ENUM 3 valeurs en_attente | valide | refuse
date_inscription DATE
 EQUIPE 
Champ Type SQL Remarque
id INT PK AUTO_INCREMENT
nom VARCHAR(100) NOT NULL 'Équipe A', 'Réserve', 'U18'
couleur VARCHAR(7) Code hex pour affichage
categorie ENUM senior|reserve|jeune
actif BOOLEAN DEFAULT 1
date_creation DATE
 HISTORIQUE_EQUIPE 
Champ Type SQL Remarque
id INT PK
joueur_id FK → utilisateur
equipe_id FK → equipe
date_entree DATE NOT NULL
  Club Manager — Documentation Complète & Réalité Footballistique Page 9
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
date_sortie DATE NULL NULL = membre actuel
motif VARCHAR(200) Transfert, rééquilibrage...
modifie_par FK → utilisateur Membre bureau
 MATCH_SEANCE
Champ Type SQL Remarque
id INT PK
type ENUM match|entr
date DATE NOT NULL
lieu VARCHAR(200)
description TEXT
statut ENUM 3 valeurs planifie|publie|termine
createur_id FK → utilisateur
 CONVOCATION
Champ Type SQL Remarque
id INT PK
match_id FK → match_seance
joueur_id FK → utilisateur
equipe_match ENUM A|B Équipe pour CE match (indép. appartenance)
est_capitaine BOOLEAN DEFAULT 0
numero_maillot INT NULL Peut changer par match
UNIQUE (match_id, joueur_id) Un joueur = une seule équipe par match
 RESULTAT_MATCH 
Champ Type SQL Remarque
id INT PK
match_id FK UNIQUE Un résultat par match
buts_equipe_a INT DEFAULT 0
buts_equipe_b INT DEFAULT 0
equipe_gagnante ENUM A|B|nul GENERATED Calculé automatiquement par MySQL
saisie_par FK → utilisateur
 PRESENCE
Champ Type SQL Remarque
id INT PK
seance_id FK → match_seance
joueur_id FK → utilisateur
type_presence ENUM 4 valeurs present|absent|retard|excuse
  Club Manager — Documentation Complète & Réalité Footballistique Page 10
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
marque_par FK → utilisateur Membre bureau
date_marquage DATE
note VARCHAR(200) NULL Motif si excusé ou retard
 PERFORMANCE
Champ Type SQL Remarque
id INT PK
seance_id FK → match_seance
joueur_id FK → utilisateur
buts INT DEFAULT 0 × 3 points
passes INT DEFAULT 0 × 2 points
points_total INT GENERATED buts×3 + passes×2 — calculé par MySQL
date_enregistrement DATE
 REGLEMENT
Champ Type SQL Remarque
id INT PK
titre VARCHAR(200)
description TEXT
montant_amende INT En FCFA
type_infraction ENUM 4 valeurs retard|absence|comportement|autre
statut ENUM 3 valeurs reflexion|actif|rejete
propose_par FK → utilisateur
date_creation DATE
 VOTE
Champ Type SQL Remarque
id INT PK
reglement_id FK → reglement
joueur_id FK → utilisateur
choix ENUM oui|non
date_vote DATE
UNIQUE (reglement_id, joueur_id) Un vote par joueur par règlement
 SANCTION
Champ Type SQL Remarque
id INT PK
joueur_id FK → utilisateur
reglement_id FK → reglement Règlement appliqué
  Club Manager — Documentation Complète & Réalité Footballistique Page 11
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
presence_id FK → presence NULL Lien à la présence qui a déclenché l'amende
applique_par FK → utilisateur Censeur
montant INT Copié depuis reglement.montant_amende
motif TEXT
statut ENUM 2 valeurs en_attente|payee
date_sanction DATE
 COTISATION
Champ Type SQL Remarque
id INT PK
joueur_id FK → utilisateur
montant INT Même pour tous les joueurs
mois VARCHAR(20) Ex: Janvier
annee INT
statut ENUM 2 valeurs paye|non_paye
date_paiement DATE NULL NULL si non payé
 CAISSE
Champ Type SQL Remarque
id INT PK
type ENUM entree|sortie
libelle VARCHAR(200)
montant INT En FCFA
categorie ENUM 5 valeurs cotisation|don|sanction|depense|autre
reference_id INT NULL ID de la cotisation ou sanction liée
enregistre_par FK → utilisateur
date_transaction DATE
RULE Solde = SUM(entrées) 
SUM(sorties) Ne jamais stocker le solde directement
 NOTIFICATIONS 
Champ Type SQL Remarque
id INT PK
destinataire_id FK → utilisateur
type ENUM 5 valeurs match_publie|sanction|vote|reglement|cotisation
message TEXT
lien VARCHAR(255) URL de redirection au clic
lu BOOLEAN DEFAULT 0 false = badge rouge
date_creation DATETIME
  Club Manager — Documentation Complète & Réalité Footballistique Page 12
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
 ACTIVITE_LOG 
Champ Type SQL Remarque
id INT PK
auteur_id FK → utilisateur
type_action VARCHAR(100) joueur_valide|match_cree|sanction_appliquee...
description TEXT 'Dupont a marqué 2 buts'
lien VARCHAR(255) NULL URL vers la ressource
date_action DATETIME Horodatage précis
 RECOMPENSE
Champ Type SQL Remarque
id INT PK
joueur_id FK → utilisateur
type ENUM mois|annee
periode VARCHAR(10) Ex: '2025-04' ou '2025'
total_points INT
date_attribution DATE
  Club Manager — Documentation Complète & Réalité Footballistique Page 13
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
RELATIONS ENTRE ENTITÉS
Cardinalités et dépendances
Entité source Entité cible Cardinalité Description
UTILISATEUR EQUIPE N → 1 Appartenance permanente via equipe_id
UTILISATEUR HISTORIQUE_EQUIPE 1 → N Traçabilité de tous les changements d'équipe
MATCH_SEANCE CONVOCATION 1 → N Un match contient plusieurs convocations (max 8+8)
UTILISATEUR CONVOCATION 1 → N Un joueur peut être convoqué plusieurs fois
MATCH_SEANCE RESULTAT_MATCH 1 → 1 Un match a un seul résultat final
MATCH_SEANCE PERFORMANCE 1 → N Un match génère des performances par joueur
UTILISATEUR PERFORMANCE 1 → N Un joueur a plusieurs performances
MATCH_SEANCE PRESENCE 1 → N Une séance a une feuille de présence
PRESENCE SANCTION 1 → 0/1 Un retard peut générer une sanction auto
REGLEMENT VOTE 1 → N Un règlement reçoit plusieurs votes
REGLEMENT SANCTION 1 → N Un règlement actif génère des sanctions
UTILISATEUR COTISATION 1 → N Une cotisation par mois par joueur
COTISATION CAISSE 1 → 0/1 Cotisation payée → entrée auto dans caisse
SANCTION CAISSE 1 → 0/1 Amende payée → entrée auto dans caisse
MATCH_SEANCE GALERIE 1 → N Photos liées à une séance
UTILISATEUR RECOMPENSE 1 → N Palmarès joueur
UTILISATEUR NOTIFICATIONS 1 → N Notifications reçues
UTILISATEUR ACTIVITE_LOG 1 → N Actions effectuées dans le système
Règles de cohérence importantes
 Un joueur ne peut être convoqué que si statut = 'valide'
 Une performance ne peut être saisie que pour une séance existante et un joueur valide
 Une sanction ne peut être créée que depuis un règlement au statut 'actif'
 La caisse ne doit jamais stocker le solde directement — toujours SUM(entrées) - SUM(sorties)
 Un vote ne peut être enregistré qu'une fois par joueur par règlement (UNIQUE KEY)
 Une cotisation est générée automatiquement à la validation — jamais manuellement pour le premier mois
 Quand une cotisation passe à 'payee', une ligne doit être créée dans la caisse automatiquement
 Quand une sanction passe à 'payee', idem — une ligne caisse est créée automatiquement
 Le résultat du match (equipe_gagnante) est calculé par MySQL — jamais stocké à la main
  Club Manager — Documentation Complète & Réalité Footballistique Page 14
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
ARCHITECTURE & STRUCTURE DU PROJET
Arborescence complète
club-football/
 config/
  database.php ← connexion PDO partagée ($pdo)
  session.php ← session_start(), constantes globales
 includes/
  header.php ← <head>, CDN Tailwind, Chart.js
  footer.php ← fermeture body, scripts JS
  navbar.php ← nav responsive + cloche notifications
  auth_check.php ← vérif session + rôle autorisé
  helpers.php ← log_activite(), creer_notification(), valider_joueur()
 assets/
  css/custom.css
  js/app.js ← dark mode, polling notifs, filtres live, flash
  uploads/ ← galerie photos (.webp uniquement)
 auth/ ← DEV 1
 users/ ← DEV 1 (inclut fiche 360°)
 matches/ ← DEV 2 (inclut suggestion auto convocation)
 stats/ ← DEV 3 (inclut api_points.php pour Chart.js)
 attendance/ ← DEV 4 (inclut export PDF présences)
 rules/ ← DEV 5
 sanctions/ ← DEV 6
 finance/ ← DEV 7 (inclut api_finance.php + export PDF)
 gallery/ ← DEV 8
 dashboard/ ← DEV 8 (inclut palmarès + fil activité)
 notifications/ ← DEV 8 (list.php + count.php pour polling)
 admin/ ← DEV 8 (paramètres club, président uniquement)
 index.php ← redirige → dashboard ou login
 schema.sql ← script SQL complet (15 tables + données initiales)
Convention Day 1 — obligatoire pour tous :
 Chaque fichier PHP : require '../config/database.php'; require '../includes/auth_check.php';
 Sessions standardisées : $_SESSION['user_id'], $_SESSION['role'], $_SESSION['equipe_id']
 Tous les inputs : htmlspecialchars() + prepared statements PDO
 Après chaque action importante : appel à log_activite() depuis helpers.php
Ordre de développement logique sur 7 jours
Jour Devs Tâche Pourquoi cet ordre
J1 matin Tous Valider schema.sql ensemble Tout le monde démarre avec la même base
J1 après
midi Dev 1 Auth + validation joueur Tout le reste dépend d'avoir des joueurs validés
J2 Dev 2 +
Dev 4 Matchs/séances + Présences Les présences dépendent des séances
J3 Dev 3 +
Dev 5
Performances +
Règlements/Votes Performances dépendent des séances
J4 Dev 6 +
Dev 7 Sanctions + Finances Sanctions dépendent des règlements actifs
J5 Dev 8 Dashboard, Notifs, Galerie, Dark
mode Agrège tout — doit venir en dernier
J6 Tous Plus-values + tests croisés Chaque dev teste le module voisin
  Club Manager — Documentation Complète & Réalité Footballistique
J7
Tous
Fusion, démo scénario complet,
PDF
Page 15
Test du cycle lundi→dimanche entier
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
  Club Manager — Documentation Complète & Réalité Footballistique Page 16
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
PLUS-VALUES — CE QUI VOUS DISTINGUE
10 fonctionnalités avancées absentes des projets classiques
Stratégie : Ces plus-values sont réparties entre les 8 devs. Chacun ajoute 1 à 2 fonctionnalités avancées
sans sortir de son module. Les 3 plus impactantes visuellement : graphiques, notifications, export PDF.
#1 Notifications internes temps réel
Cloche + badge rouge dans la navbar. Polling JS toutes les 30s. Table notifications.
 match_publie → tous les convoqués notifiés
 sanction appliquée → joueur concerné notifié
 règlement passé en vote → tous notifiés
 cotisation impayée après le 5 du mois → rappel auto
#2 Graphiques interactifs Chart.js
Courbe évolution des points, camembert présences, barres bilan financier.
 stats/api_points.php → JSON pour courbe mensuelle
 attendance/api_presence.php → JSON pour camembert
 finance/api_finance.php → JSON pour barres entrées/sorties
 stats/joueur.php → buts vs passes en barres groupées
#3 Export PDF des rapports
Rapports téléchargeables en 1 clic via FPDF côté serveur.
 Feuille de présence mensuelle (Dev 4)
 Classement mensuel des joueurs (Dev 3)
 Bilan financier mensuel/annuel (Dev 7)
 Fiche sanction individuelle par joueur (Dev 6)
#4 Mode sombre (Dark Mode)
Toggle JS + classes Tailwind dark:. Préférence sauvegardée en localStorage.
 Ajouter dark: devant chaque classe Tailwind
 Toggle met class='dark' sur
 localStorage.setItem('theme', 'dark') pour persistance
 Icône lune/soleil dans la navbar
#5 Recherche & filtres live
Filtrage instantané sans rechargement sur toutes les listes.
 Liste joueurs : nom, équipe, statut, poste
 Liste matchs : date, lieu, statut
 Transactions caisse : libellé, catégorie
 Galerie : filtrage par séance ou type
#6 Historique d'activité (log)
Fil d'actualité dans le dashboard. Table activite_log tracée partout.
 Fonction helper log_activite() dans includes/helpers.php
 Appelée après chaque action importante par chaque dev
 Dashboard affiche les 10 dernières actions
 Filtrables par type et par auteur (pour le bureau)
  Club Manager — Documentation Complète & Réalité Footballistique Page 17
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
#7 Convocation intelligente
Suggestion auto des 8 meilleurs joueurs selon score présence + perfs.
 score = perf_points × 0.6 + nb_presences × 0.4
 Requête SQL avec LEFT JOIN performance + presence
 Liste proposée mais modifiable avant publication
 Indicateur visuel sur chaque joueur suggéré
#8 Fiche joueur complète 360°
Page profil qui regroupe toutes les infos d'un joueur en un seul endroit.
 Photo, poste, équipe, numéro de maillot
 Stats du mois + graphique Chart.js annuel
 Historique présences + taux mensuel
 Sanctions reçues + cotisations mois par mois
#9 Système de saison avec palmarès
Gestion de l'année civile. Archivage stats au 1er janvier. Palmarès permanent.
 Calcul meilleur joueur de l'année le 1er janvier
 Table recompense type='annee' periode='2025'
 Page palmarès accessible par tous
 Compteurs repartent à 0 — historique BDD conservé
#10 Messages flash + validation robuste
Alertes succès/erreur après chaque action. Validation serveur stricte sur tous les inputs.
 $_SESSION['flash'] = ['type'=>'success', 'msg'=>'...']
 4 types : success, error, info, warning
 Validation : htmlspecialchars(), filter_var(), intval()
 Vérification rôle dans auth_check.php pour chaque page
  Club Manager — Documentation Complète & Réalité Footballistique Page 18
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
PLUS-VALUES — IMPLÉMENTATIONS CLÉS
Code prêt à utiliser
 Notifications — polling JS
// Dans navbar.php — polling toutes les 30 secondes
setInterval(() => {
fetch('../notifications/count.php')
.then(r => r.json())
.then(d => {
if (d.count > 0) {
document.getElementById('notif-badge').textContent = d.count;
document.getElementById('notif-badge').classList.remove('hidden');
}
});
}, 30000);
 Chart.js — courbe évolution des points
// stats/api_points.php → retourne JSON
// stats/classement.php — dans le <script>
fetch('api_points.php?joueur_id=<?= $id ?>')
.then(r => r.json())
.then(data => {
new Chart(document.getElementById('chartPoints'), {
type: 'line',
data: {
labels: data.map(d => d.mois),
datasets: [{ label: 'Points', data: data.map(d => d.points),
borderColor: '#16a34a', tension: 0.4, fill: true }]
}
});
});
 Convocation intelligente — requête SQL-- Top 8 joueurs à convoquer (mois en cours)
SELECT u.id, u.nom, u.prenom, u.poste, e.nom AS equipe,
COALESCE(SUM(p.points_total), 0) AS pts_perf,
COUNT(CASE WHEN pr.type_presence = 'present' THEN 1 END) AS nb_present,
ROUND(
COALESCE(SUM(p.points_total), 0) * 0.6 +
COUNT(CASE WHEN pr.type_presence = 'present' THEN 1 END) * 0.4
, 2) AS score
FROM utilisateur u
LEFT JOIN equipe e ON e.id = u.equipe_id
LEFT JOIN performance p
ON p.joueur_id = u.id AND MONTH(p.date_enregistrement) = MONTH(NOW())
LEFT JOIN presence pr
ON pr.joueur_id = u.id AND MONTH(pr.date_marquage) = MONTH(NOW())
WHERE u.statut = 'valide' AND u.role = 'joueur'
GROUP BY u.id
ORDER BY score DESC
LIMIT 8;
Chaîne automatique retard → amende → caisse
// Dans attendance/marquer.php — après INSERT dans presence
if ($type_presence === 'retard') {
// Chercher un règlement actif de type 'retard'
$regl = $pdo->query("SELECT id, montant_amende FROM reglement
WHERE statut='actif' AND type_infraction='retard'
LIMIT 1")->fetch();
if ($regl) {
  Club Manager — Documentation Complète & Réalité Footballistique
// Créer la sanction automatiquement
$pdo->prepare("INSERT INTO sanction
(joueur_id, reglement_id, presence_id, applique_par, montant, motif, statut)
VALUES (?, ?, ?, ?, ?, 'Retard automatique', 'en_attente')")->execute([$joueur_id, $regl['id'], $presence_id,
$_SESSION['user_id'], $regl['montant_amende']]);
// Notifier le joueur
creer_notification($pdo, $joueur_id, 'sanction',
"Amende de {$regl['montant_amende']} FCFA pour retard à l'entraînement.");
}
}
Page 19
Cotisation payée → caisse automatique
// Dans finance/cotisations.php — après UPDATE cotisation SET statut='paye'
$pdo->prepare("INSERT INTO caisse (type, libelle, montant, categorie, reference_id, enregistre_par)
VALUES ('entree', ?, ?, 'cotisation', ?, ?)")->execute(["Cotisation $mois $annee — $nom_joueur", $montant,
$cotisation_id, $_SESSION['user_id']]);
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
  Club Manager — Documentation Complète & Réalité Footballistique Page 20
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
RÉPARTITION DES 8 DÉVELOPPEURS
Chaque dev est autonome · 1 module + 1 à 2 plus-values assignées
DEV 1 Auth & Utilisateurs
Connexion, rôles, validation complète, fiche 360°
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.486275,.22
7451,.929412,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Inscription joueur avec upload photo
obligatoire
→ Connexion / déconnexion + gestion
$_SESSION
→ Validation bureau : accepter, refuser,
affecter équipe
→ Génération automatique cotisation à la
validation
→ Modification rôle et équipe par le
président
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.486275,.22
7451,.929412,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
auth/login.php
auth/register.php
auth/logout.php
users/validation.php
users/profile.php (360°)
users/list.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.486275
,.227451,.929412,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Messages flash sur toutes les actions
  Fiche joueur 360° (stats, présences,
sanctions, cotisations)
DEV 2 Matchs & Séances
Création, convocations intelligentes, résultat
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.054902,.64
7059,.913725,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Créer / modifier / publier un match ou
entraînement
→ Sélectionner joueurs convoqués
(équipe_match A ou B)
→ Désigner les capitaines, assigner les
numéros de maillot
→ Saisir le résultat après le match
→ Page index.php : prochain match +
historique
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.054902,.64
7059,.913725,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
matches/create.php
matches/edit.php
matches/convocations.php
matches/detail.php
matches/list.php
index.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.054902
,.647059,.913725,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Suggestion auto des 8 meilleurs
joueurs (SQL score)
  Filtres live sur la liste des matchs
  Club Manager — Documentation Complète & Réalité Footballistique Page 21
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
DEV 3 Performances & Stats
Buts, passes, classements, récompenses
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.85098,.466
667,.023529,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Saisie buts et passes par l'entraîneur
après chaque séance
→ Calcul auto points (but=3, passe=2) —
GENERATED en SQL
→ Classement mensuel et annuel
→ Meilleur joueur du mois (auto) + de
l'année (1er jan.)
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.85098,.466
667,.023529,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
stats/saisie.php
stats/classement.php
stats/joueur.php
stats/recompenses.php
stats/api_points.php (JSON)
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.85098,.
466667,.023529,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Graphique Chart.js courbe évolution
points
  Graphique barres buts vs passes par
séance
DEV 4 Présences
Feuille hebdo avec retard/excuse, export PDF
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches',
textColor=Color(.862745,.14902,.14902,1),
us_lines=[])] 'style': 'bulletText': None
'debug': 0 ) #Paragraph
→ Formulaire 4 statuts : présent / absent /
retard / excusé
→ Retard → déclenche amende
automatique si règlement actif
→ Verrouillage si présence déjà marquée
pour cette séance
→ Historique et stats mensuelles par
joueur
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers',
textColor=Color(.862745,.14902,.14902,1),
us_lines=[])] 'style': 'bulletText': None
'debug': 0 ) #Paragraph
attendance/marquer.php
attendance/historique.php
attendance/stats.php
attendance/export_pdf.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values',
textColor=Color(.862745,.14902,.14902,1),
us_lines=[])] 'style': 'bulletText': None
'debug': 0 ) #Paragraph
  Export PDF feuille de présence
mensuelle
  Graphique camembert taux de
présence (Chart.js)
DEV 5 Règlements & Votes
Propositions démocratiques, activation auto
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.031373,.56
8627,.698039,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Proposer un règlement (statut initial =
réflexion)
→ Vote oui/non — UNIQUE KEY empêche
le double vote
→ Passage auto à 'actif' si majorité atteinte
→ 4 types d'infraction : retard, absence,
comportement, autre
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.031373,.56
8627,.698039,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
rules/propose.php
rules/vote.php
rules/list.php
rules/detail.php
rules/activation.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.031373
,.568627,.698039,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Log activité : chaque vote et activation
enregistrés
  Validation robuste des formulaires
  Club Manager — Documentation Complète & Réalité Footballistique Page 22
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
DEV 6 Sanctions & Amendes
Amendes manuelles et auto, suivi paiements
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.086275,.63
9216,.290196,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Appliquer une sanction manuelle (liée à
un règlement actif)
→ Les sanctions automatiques (retard)
sont créées par le module présences
→ Marquer payée → crée
automatiquement une entrée dans la
caisse
→ Historique et total amendes par joueur
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.086275,.63
9216,.290196,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
sanctions/appliquer.php
sanctions/list.php
sanctions/marquer_payee.php
sanctions/historique_joueur.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.086275
,.639216,.290196,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Notification automatique au joueur
sanctionné
  Filtres live sur la liste (statut, joueur,
montant)
DEV 7 Finances
Cotisations, caisse cohérente, exports PDF
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.858824,.15
2941,.466667,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Marquer cotisation payée → entrée auto
dans la caisse
→ Ajouter dons et dépenses manuellement
→ Solde calculé en temps réel :
SUM(entrées) - SUM(sorties)
→ Rapport global annuel par joueur + total
club
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.858824,.15
2941,.466667,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
finance/cotisations.php
finance/caisse.php
finance/ajouter_operation.php
finance/rapport.php
finance/api_finance.php (JSON)
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.858824
,.152941,.466667,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Export PDF bilan financier mensuel et
annuel
  Graphique Chart.js entrées vs sorties
DEV 8 Galerie, Dashboard & Admin
Photos, tableau de bord, notifications, dark mode
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Tâches' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Tâches', textColor=Color(.278431,.33
3333,.411765,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
→ Upload photos .webp (validation type +
taille + renommage auto)
→ Dashboard : 10 widgets, fil activité,
palmarès, top joueur
→ Paramètres club : nom, logo, slogan
(président uniquement)
→ Galerie filtrée par séance ou événement
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Fichiers' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Fichiers', textColor=Color(.278431,.33
3333,.411765,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
gallery/upload.php
gallery/list.php
dashboard/index.php
notifications/list.php
notifications/count.php
admin/settings.php
Paragraph( 'caseSensitive': 1 'encoding':
'utf8' 'text': 'Plus-values' 'frags':
[ParaFrag(__tag__='b', bold=1,
fontName='Helvetica-Bold', fontSize=7.5,
greek=0, italic=0, link=[], rise=0,
text='Plus-values', textColor=Color(.278431
,.333333,.411765,1), us_lines=[])] 'style':
'bulletText': None 'debug': 0 ) #Paragraph
  Notifications (cloche + polling JS +
page liste)
  Mode sombre complet (Tailwind dark:
+ toggle localStorage)
  Page palmarès des saisons passées
  Club Manager — Documentation Complète & Réalité Footballistique Page 23
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
TABLEAU DE BORD & GESTION DES RÔLES
Widgets du dashboard (page centrale)
Widget Données Visible par
Prochain match Date, lieu, équipes, statut publication Tous
Top joueur du mois Photo + nom + points du mois en cours Tous
Solde de la caisse SUM(entrées) - SUM(sorties) en FCFA Bureau seul
Taux de présence Moyenne du mois — camembert Chart.js Tous
Sanctions en attente Nb d'amendes non payées dans le club Bureau seul
Règlements en vote Règlements au statut 'réflexion' + lien vote Tous
Cotisations impayées Joueurs n'ayant pas payé ce mois Bureau seul
Classement rapide Top 5 joueurs par points — mois en cours Tous
Fil d'activité 10 dernières actions du log Bureau seul
Palmarès saisons Meilleur joueur de chaque année Tous
Matrice des accès par rôle
Rôle Accès complet Lecture seule Bloqué
Joueur Profil perso, Vote règlements,
Stats perso, Galerie
Matchs, Classement,
Règlements actifs
Finances, Sanctions (gestion),
Paramètres, Validation
Entraîneur Saisie performances,
Convocations
Matchs, Stats, Classement Finances, Sanctions, Paramètres
Organisateur Matchs (CRUD), Présences,
Galerie (upload)
Stats, Classement,
Finances (vue)
Sanctions, Paramètres club
Censeur Sanctions (CRUD), Règlements,
Finances (lecture)
Tout le reste Paramètres club, Supprimer
utilisateurs
Président Tout — accès complet à tous les
modules
— Aucune restriction
Le test de validation final — scénario complet
Avant de rendre le projet, testez ce scénario de bout en bout :
 Le président crée 10 comptes joueurs et les valide → cotisations générées auto
 L'organisateur crée un entraînement jeudi
 Le bureau marque : 8 présents, 1 absent, 1 en retard → amende auto créée
 L'entraîneur saisit 2 buts et 1 passe pour un joueur → points mis à jour
 Le vendredi, le bureau crée un match → système suggère les 8 meilleurs joueurs
 Après le match, résultat saisi → classement mis à jour → top joueur du mois calculé
 Le joueur en retard paie son amende → 200 FCFA entrent dans la caisse automatiquement
 Le dashboard reflète tout en temps réel — si ce scénario fonctionne, le projet est solide.
  Club Manager — Documentation Complète & Réalité Footballistique Page 24
© 2026  Club Manager  ·  Projet académique  ·  Document confidentiel
CONTACTEZ-NOUS
Responsables des modules et informations du projet
Responsables des modules
Module Dev Plus-values Contact
Auth & Utilisateurs Dev 1 Flash ·  Fiche 360° dev1@clubmanager.bj
Matchs & Séances Dev 2 Convocation auto ·  Filtres dev2@clubmanager.bj
Performances & Stats Dev 3 Graphiques Chart.js dev3@clubmanager.bj
Présences Dev 4 Export PDF ·  Graphique dev4@clubmanager.bj
Règlements & Votes Dev 5 Log activité ·  Validation dev5@clubmanager.bj
Sanctions & Amendes Dev 6 Notifications ·  Filtres dev6@clubmanager.bj
Finances Dev 7 Export PDF ·  Graphiques dev7@clubmanager.bj
Galerie, Dashboard, Admin Dev 8 Notifs ·  Dark ·  Palmarès dev8@clubmanager.bj
Informations du projet
Nom du projet Club Manager
Type Application web — PHP 8 natif + MySQL 8 + Tailwind CSS + Chart.js
Durée de développement 1 semaine — projet académique
Équipe 8 développeurs, 1 module chacun, totalement indépendants
Tables SQL 15 tables (dont notifications, activite_log, historique_equipe, resultat_match)
Plus-values intégrées 10 fonctionnalités avancées (voir pages 10-11)
Dépôt Git github.com/votre-groupe/club-manager ← à compléter
Contact chef de projet chef@clubmanager.bj ← à compléter
Établissement À compléter par l'équipe
Année académique 2026
Date du document 22 April 2026
Rappel final : Chaque module doit contenir un README.md décrivant les endpoints créés, les choix
techniques et les instructions de test en local. La fonction log_activite() doit être appelée après chaque action
importante. La chaîne retard → sanction → notification → caisse doit fonctionner en une seule action
bureau pour que le projet soit digne de la réalité footballistique.
Club Manager v3.0 · 22 April 2026 · Documentation complète basée sur la réalité footballistique