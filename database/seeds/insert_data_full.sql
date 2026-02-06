-- ============================================
-- CesiStages - Données de test (Mise à jour v2)
-- ============================================

USE cesi_stages;

-- ============================================
-- Insertion des utilisateurs
-- ============================================

-- 1. Administrateur
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Admin', 'CESI', 'admin@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- 1bis. Recruteur de démo
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Recruteur', 'Demo', 'recruteur@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruteur', 1);

-- 2-5. Pilotes (Smail, Myriam, Rim, Sonia)
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Ben Hamed', 'Smail', 'smail@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1),
('El Khomri', 'Myriam', 'myriam@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1),
('Amrani', 'Rim', 'rim@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1),
('Dubois', 'Sonia', 'sonia@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1);

-- 7-15. Étudiants (Ahmed, Paul, Lionel, Lilian, Mohamed, Lucas, Enzo, Saleh, Victor)
INSERT INTO users (nom, prenom, email, password, role, is_verified, age, telephone, adresse, bio) VALUES
('Benali', 'Ahmed', 'ahmed@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 22, '0610203040', '12 Rue des Lilas, Paris', 'Passionné par le développement Fullstack et l\'IA.'),
('Durand', 'Paul', 'paul@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 21, '0610203041', '15 Avenue Jean Jaurès, Lyon', 'À la recherche d\'un stage en cybersécurité.'),
('Messi', 'Lionel', 'lionel@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 23, '0610203042', '4 Place de la République, Lille', 'Expert en base de données et backend.'),
('Thuram', 'Lilian', 'lilian@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 22, '0610203043', '8 Boulevard Haussmann, Bordeaux', 'Intéressé par le Big Data et le Cloud.'),
('Salah', 'Mohamed', 'mohamed@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 24, '0610203044', '22 Rue Victor Hugo, Marseille', 'Développeur mobile Android et iOS.'),
('Hernandez', 'Lucas', 'lucas@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 20, '0610203045', '7 Allée des Roses, Toulouse', 'Curieux et motivé, je suis fan de ReactJS.'),
('Ferrari', 'Enzo', 'enzo@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 21, '0610203046', '35 Rue de Rome, Nice', 'Futur ingénieur système embarqué.'),
('Mahrez', 'Saleh', 'saleh@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 22, '0610203047', '10 Quai des Chartrons, Nantes', 'Spécialisé en réseaux et télécoms.'),
('Hugo', 'Victor', 'victor@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 23, '0610203048', '1 Rue Notre-Dame, Strasbourg', 'Poète du code, j\'aime le code propre.');

-- ============================================
-- Insertion des entreprises
-- ============================================
INSERT INTO entreprises (nom, description, email, telephone, adresse, site_web, secteur) VALUES
('Capgemini', 'Leader mondial du conseil, de la transformation numérique, des services technologiques et de l''ingénierie.', 'contact@capgemini.com', '01 47 54 50 00', '11 Rue de Tilsitt, 75017 Paris', 'https://www.capgemini.com', 'Informatique'),
('Criteo', 'Entreprise technologique mondiale de commerce média.', 'jobs@criteo.com', '01 40 40 50 50', '32 Rue Blanche, 75009 Paris', 'https://www.criteo.com', 'Data Science'),
('Publicis Sapient', 'Partenaire de transformation numérique.', 'careers@publicissapient.fr', '01 44 43 70 00', '94 Avenue Gambetta, 75020 Paris', 'https://www.publicissapient.com', 'Web Design'),
('OVHcloud', 'Acteur mondial du cloud computing et leader européen.', 'jobs@ovhcloud.com', '09 72 10 10 07', '2 Rue Kellermann, 59100 Roubaix', 'https://www.ovhcloud.com', 'Cloud Computing'),
('Thales', 'Groupe d''électronique spécialisé dans l''aérospatiale, la défense, la sécurité.', 'recrutement@thalesgroup.com', '01 57 77 80 00', '31 Place des Corolles, 92400 Courbevoie', 'https://www.thalesgroup.com', 'Cybersécurité'),
('Ubisoft', 'Un des leaders mondiaux de la création de jeux vidéo.', 'stages@ubisoft.com', '01 48 18 50 00', '2 Avenue Pasteur, 94160 Saint-Mandé', 'https://www.ubisoft.com', 'Développement'),
('Dassault Systèmes', 'L''entreprise 3DEXPERIENCE.', 'contact@3ds.com', '01 61 62 61 62', '10 Rue Marcel Dassault, 78140 Vélizy-Villacoublay', 'https://www.3ds.com', 'Intelligence Artificielle'),
('Airbus', 'Pionnier international de l''industrie aérospatiale.', 'careers@airbus.com', '05 61 93 33 33', '2 Rond-Point Emile Dewoitine, 31700 Blagnac', 'https://www.airbus.com', 'Réseaux'),
('Atos', 'Leader international de la transformation digitale.', 'jobs@atos.net', '01 73 26 00 00', '80 Quai Voltaire, 95870 Bezons', 'https://atos.net', 'Cybersécurité'),
('Sopra Steria', 'Leader européen du conseil, des services numériques et de l''édition de logiciels.', 'contact@soprasteria.com', '01 40 67 29 29', '6 Avenue Kleber, 75116 Paris', 'https://www.soprasteria.com', 'Consulting'),
('Michelin', 'Leader mondial des pneumatiques et de la mobilité durable.', 'recrutement@michelin.com', '04 73 32 20 00', '23 Place des Carmes Dechaux, 63000 Clermont-Ferrand', 'https://www.michelin.com', 'Industrie 4.0'),
('Sanofi', 'Entreprise biopharmaceutique mondiale centrée sur la santé humaine.', 'careers@sanofi.com', '01 53 77 40 00', '54 Rue La Boétie, 75008 Paris', 'https://www.sanofi.com', 'Santé / Data'),
('Orange', 'L''un des principaux opérateurs de télécommunications dans le monde.', 'stages@orange.com', '01 44 44 22 22', '111 Quai du Président Roosevelt, 92130 Issy-les-Moulineaux', 'https://www.orange.com', 'Télécoms');

-- ============================================
-- Insertion des offres de stage (Plus d'offres)
-- ============================================
INSERT INTO offres (entreprise_id, titre, description, competences, remuneration, duree, date_debut, date_fin) VALUES
-- Capgemini (1)
(1, 'Développeur Web Full Stack', 'Rejoignez notre équipe pour développer des applications web modernes.', '["PHP", "JavaScript", "React"]', 1200.00, 6, '2025-03-01', '2025-08-31'),
(1, 'Consultant Junior ERP', 'Accompagnement de nos clients grands comptes sur SAP.', '["SAP", "ERP", "Consulting"]', 1300.00, 6, '2025-04-01', '2025-09-30'),
(1, 'Développeur Java Spring', 'Développement de microservices en Java.', '["Java", "Spring Boot", "Docker"]', 1250.00, 5, '2025-03-15', '2025-08-15'),

-- Criteo (2)
(2, 'Data Analyst Junior', 'Analyse de grands volumes de données publicitaires.', '["Python", "SQL", "Tableau"]', 1400.00, 6, '2025-03-01', '2025-08-31'),
(2, 'Machine Learning Engineer', 'Optimisation des algorithmes de recommandation.', '["Python", "TensorFlow", "Spark"]', 1600.00, 6, '2025-02-01', '2025-07-31'),

-- Publicis Sapient (3)
(3, 'Designer UI/UX', 'Conception d\'interfaces utilisateurs pour le e-commerce.', '["Figma", "Adobe XD", "CSS"]', 900.00, 3, '2025-05-01', '2025-07-31'),
(3, 'Intégrateur Web', 'Intégration de maquettes pixel-perfect.', '["HTML", "SASS", "JS"]', 1000.00, 4, '2025-04-01', '2025-07-31'),

-- OVHcloud (4)
(4, 'Ingénieur Cloud', 'Déploiement et gestion d\'infrastructures cloud.', '["AWS", "OpenStack", "Linux"]', 1500.00, 6, '2025-03-01', '2025-08-31'),
(4, 'DevOps Junior', 'Automatisation CI/CD et monitoring.', '["GitLab", "Ansible", "Grafana"]', 1400.00, 6, '2025-03-01', '2025-08-31'),

-- Thales (5)
(5, 'Analyste Cybersécurité', 'Audit de sécurité des systèmes critiques.', '["Pentest", "Python", "Linux"]', 1300.00, 6, '2025-03-15', '2025-09-15'),
(5, 'Ingénieur Système Embarqué', 'Développement sur cartes électroniques temps réel.', '["C", "C++", "RTOS"]', 1400.00, 6, '2025-04-01', '2025-09-30'),

-- Ubisoft (6)
(6, 'Développeur Gameplay', 'Implémentation de mécaniques de jeu sur moteur propriétaire.', '["C++", "Maths", "Game Design"]', 1100.00, 6, '2025-03-01', '2025-08-31'),
(6, 'Level Designer Junior', 'Création de niveaux de jeu immersifs.', '["Unity", "Unreal", "Creativité"]', 1000.00, 4, '2025-04-01', '2025-07-31'),

-- Dassault Systèmes (7)
(7, 'Ingénieur R&D 3D', 'Recherche sur les moteurs de rendu 3D.', '["OpenGL", "Vulkan", "C++"]', 1500.00, 6, '2025-02-01', '2025-07-31'),

-- Airbus (8)
(8, 'Ingénieur Réseaux Aéronautiques', 'Maintenance des réseaux de communication bord-sol.', '["Réseaux", "SatCom", "Anglais"]', 1250.00, 6, '2025-03-01', '2025-08-31'),
(8, 'Data Scientist Aviation', 'Analyse prédictive de maintenance.', '["Python", "Big Data", "Spark"]', 1450.00, 6, '2025-03-01', '2025-08-31'),

-- Atos (9)
(9, 'Consultant Digital Workplace', 'Accompagnement au changement pour la suite Office 365.', '["Office 365", "Formation", "Gestion de projet"]', 1100.00, 6, '2025-04-01', '2025-09-30'),
(9, 'Administrateur Système', 'Maintien en condition opérationnelle des serveurs.', '["Windows Server", "Linux", "VMware"]', 1150.00, 6, '2025-03-01', '2025-08-31'),

-- Sopra Steria (10)
(10, 'Business Analyst', 'Rédaction de spécifications fonctionnelles.', '["UML", "Agile", "Jira"]', 1200.00, 6, '2025-04-01', '2025-09-30'),

-- Michelin (11)
(11, 'Ingénieur Industrie 4.0', 'Digitalisation des lignes de production.', '["IoT", "Python", "Automatisme"]', 1350.00, 6, '2025-03-01', '2025-08-31'),

-- Sanofi (12)
(12, 'Bio-Informaticien', 'Traitement de données génomiques.', '["R", "Python", "Biologie"]', 1400.00, 6, '2025-02-01', '2025-07-31'),

-- Orange (13)
(13, 'Ingénieur Fibre Optique', 'Planification du déploiement FTTH.', '["SIG", "Excel", "Terrain"]', 1100.00, 4, '2025-05-01', '2025-08-31'),
(13, 'Développeur 5G', 'Tests et validation des antennes 5G.', '["Radio", "Matlab", "Python"]', 1300.00, 6, '2025-03-01', '2025-08-31');

-- ============================================
-- Insertion des candidatures
-- ============================================
INSERT INTO candidatures (offre_id, etudiant_id, lettre_motivation, statut) VALUES
(1, 7, 'Votre offre correspond exactement à mes compétences.', 'en_attente'), -- Ahmed -> Capgemini
(4, 9, 'Passionné par la data, je veux rejoindre Criteo.', 'acceptee'),     -- Lionel -> Criteo
(8, 10, 'DevOps est mon avenir, je suis super motivé.', 'refusee'),       -- Lilian -> OVH
(10, 8, 'La sécu c''est ma vie.', 'en_attente'),                        -- Paul -> Thales
(12, 12, 'Je veux créer des jeux chez vous !', 'en_attente'),            -- Lucas -> Ubisoft
(15, 13, 'L''aérospatial me fait rêver.', 'en_attente'),                 -- Enzo -> Airbus
(5, 14, 'Le machine learning m''intéresse beaucoup.', 'acceptee');       -- Saleh -> Criteo

-- ============================================
-- Insertion de la Wishlist
-- ============================================
INSERT INTO wishlist (etudiant_id, offre_id) VALUES
(7, 1), (7, 3), -- Ahmed
(8, 10), (8, 11), -- Paul
(9, 4), (9, 5), -- Lionel
(12, 12), (12, 13), -- Lucas
(15, 2), (15, 18); -- Victor

-- ============================================
-- Insertion des Évaluations (Avis)
-- ============================================
-- Entreprise_id, User_id (Etudiant), Note, Commentaire
INSERT INTO evaluations (entreprise_id, user_id, note, commentaire, created_at) VALUES
(1, 7, 5, 'Super ambiance chez Capgemini, j''ai beaucoup appris sur React. Les tuteurs sont très pédagogues et l''équipe est jeune.', '2024-09-15 10:00:00'),
(1, 9, 4, 'Bonne expérience globale, mais beaucoup de travail. Le salaire est correct pour le marché.', '2024-10-02 14:30:00'),
(2, 8, 5, 'Incroyable stage chez Criteo. Les locaux sont magnifiques (salle de sport, cantine bio) et les projets data sont passionnants.', '2024-08-20 09:15:00'),
(3, 12, 3, 'Un peu déçu par le management chez Publicis, mais les missions étaient intéressantes graphiquement.', '2024-11-05 16:45:00'),
(4, 10, 5, 'OVHcloud c''est le top pour apprendre le Cloud et le DevOps. On nous donne de vraies responsabilités dès le début.', '2024-12-01 11:20:00'),
(6, 12, 5, 'Travailler sur un AAA chez Ubisoft c''était mon rêve. Pression intense en fin de projet (crunch) mais super fier du résultat.', '2024-07-30 18:00:00'),
(5, 11, 4, 'Thales est une école de rigueur. Très formateur pour la cybersécurité, même si les process sont un peu lourds.', '2024-09-10 13:40:00'),
(8, 13, 5, 'Airbus offre un cadre de travail exceptionnel à Toulouse. Projets internationaux et technos de pointe.', '2024-10-15 08:50:00'),
(11, 14, 4, 'Stage très terrain chez Michelin, j''ai adoré voir les lignes de production. L''équipe industrie 4.0 est top.', '2025-01-10 15:30:00'),
(13, 15, 2, 'Stage un peu répétitif chez Orange sur le déploiement fibre, mais bonne ambiance dans l''équipe technique.', '2024-11-20 10:10:00');

-- ============================================
-- Insertion des Assignations Pilotes-Etudiants
-- ============================================
-- Smail (2) suit Ahmed (7), Mohamed (11), Victor (15)
INSERT INTO pilote_etudiant (pilote_id, etudiant_id) VALUES (2, 7), (2, 11), (2, 15);

-- Myriam (3) suit Paul (8), Lucas (12)
INSERT INTO pilote_etudiant (pilote_id, etudiant_id) VALUES (3, 8), (3, 12);

-- Rim (4) suit Lionel (9), Enzo (13)
INSERT INTO pilote_etudiant (pilote_id, etudiant_id) VALUES (4, 9), (4, 13);

-- Sonia (5) suit Lilian (10), Saleh (14)
INSERT INTO pilote_etudiant (pilote_id, etudiant_id) VALUES (5, 10), (5, 14);
