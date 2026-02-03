-- ============================================
-- CesiStages - Données de test (Mise à jour)
-- ============================================

USE cesi_stages;

-- ============================================
-- Insertion des utilisateurs
-- ============================================

-- Administrateur
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Admin', 'CESI', 'admin@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Pilotes
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Dupont', 'Marie', 'pilote@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1),
('Martin', 'Jean', 'jean.martin@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', 1);

-- Étudiants
INSERT INTO users (nom, prenom, email, password, role, is_verified, age, telephone, adresse, bio) VALUES
('Doe', 'John', 'etudiant@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 22, '0612345678', '1 rue de l''Exemple, Paris', 'Etudiant motivé en informatique.'),
('Smith', 'Alice', 'alice.smith@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 21, '0698765432', '10 avenue des Champs, Lyon', 'Passionnée par la Data Science.'),
('Bernard', 'Lucas', 'lucas.bernard@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 23, NULL, NULL, NULL),
('Petit', 'Emma', 'emma.petit@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 20, NULL, NULL, NULL),
('Robert', 'Hugo', 'hugo.robert@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, 24, NULL, NULL, NULL);

-- Note: Le mot de passe hashé correspond à "password"

-- ============================================
-- Insertion des entreprises
-- ============================================
INSERT INTO entreprises (nom, description, email, telephone, adresse, secteur) VALUES
('TechCorp', 'Entreprise leader dans le développement de solutions technologiques innovantes.', 'contact@techcorp.fr', '01 23 45 67 89', '123 Avenue de la Technologie, 75001 Paris', 'Informatique'),
('DataSolutions', 'Spécialiste en analyse de données et business intelligence.', 'recrutement@datasolutions.fr', '01 98 76 54 32', '45 Rue des Données, 69002 Lyon', 'Data Science'),
('WebAgency', 'Agence web créative spécialisée dans le design et le développement.', 'jobs@webagency.fr', '01 11 22 33 44', '8 Boulevard du Web, 33000 Bordeaux', 'Web Design'),
('CloudSystems', 'Expert en solutions cloud et infrastructure IT.', 'careers@cloudsystems.fr', '01 55 66 77 88', '12 Avenue du Cloud, 75008 Paris', 'Cloud Computing'),
('CyberSecur', 'Société de cybersécurité et protection des données.', 'stage@cybersecur.fr', '01 33 44 55 66', '25 Rue de la Sécurité, 75015 Paris', 'Cybersécurité'),
('DevStudio', 'Studio de développement de jeux vidéo et applications mobiles.', 'hello@devstudio.fr', '01 77 88 99 00', '56 Rue du Jeu, 69007 Lyon', 'Développement'),
('AIInnovation', 'Startup spécialisée en intelligence artificielle et machine learning.', 'contact@aiinnovation.fr', '01 22 33 44 55', '78 Avenue de l\'IA, 75009 Paris', 'Intelligence Artificielle'),
('NetServices', 'Fournisseur de services réseaux et télécommunications.', 'rh@netservices.fr', '01 66 77 88 99', '34 Boulevard des Réseaux, 44000 Nantes', 'Réseaux');

-- ============================================
-- Insertion des offres de stage
-- ============================================
INSERT INTO offres (entreprise_id, titre, description, competences, remuneration, duree, date_debut, date_fin) VALUES
(1, 'Développeur Web Full Stack', 
 'Nous recherchons un stagiaire développeur web passionné pour rejoindre notre équipe. Vous participerez au développement de nouvelles fonctionnalités et à la maintenance de nos applications web.',
 '["PHP", "JavaScript", "HTML/CSS", "MySQL", "React"]',
 1200.00, 6, '2025-03-01', '2025-08-31'),

(1, 'Développeur Backend PHP', 
 'Stage en développement backend avec PHP et Symfony. Vous travaillerez sur des API REST et des services web.',
 '["PHP", "Symfony", "MySQL", "API REST", "Git"]',
 1000.00, 4, '2025-04-01', '2025-07-31'),

(2, 'Data Analyst Junior', 
 'Rejoignez notre équipe data pour analyser des jeux de données complexes et créer des tableaux de bord interactifs.',
 '["Python", "SQL", "Power BI", "Excel", "Statistiques"]',
 1300.00, 6, '2025-03-15', '2025-09-15'),

(2, 'Data Scientist', 
 'Stage en data science avec focus sur le machine learning et les modèles prédictifs.',
 '["Python", "Machine Learning", "Pandas", "Scikit-learn", "SQL"]',
 1400.00, 6, '2025-02-01', '2025-07-31'),

(3, 'Designer UI/UX', 
 'Stage en design d\'interface utilisateur et expérience utilisateur. Vous travaillerez sur des projets web et mobile.',
 '["Figma", "Adobe XD", "UI Design", "UX Research", "Prototypage"]',
 900.00, 3, '2025-03-01', '2025-05-31'),

(3, 'Développeur Frontend', 
 'Nous cherchons un stagiaire frontend pour travailler sur des projets React et Vue.js.',
 '["JavaScript", "React", "Vue.js", "CSS", "HTML"]',
 1100.00, 4, '2025-04-01', '2025-07-31'),

(4, 'Ingénieur Cloud', 
 'Stage en architecture cloud et déploiement d\'infrastructure AWS/Azure.',
 '["AWS", "Azure", "Docker", "Kubernetes", "Terraform"]',
 1500.00, 6, '2025-03-01', '2025-08-31'),

(4, 'DevOps Engineer', 
 'Rejoignez notre équipe DevOps pour automatiser les déploiements et optimiser nos pipelines CI/CD.',
 '["Docker", "Jenkins", "GitLab CI", "Linux", "Scripting"]',
 1400.00, 6, '2025-02-15', '2025-08-15'),

(5, 'Analyste Cybersécurité', 
 'Stage en analyse de sécurité et audit de vulnérabilités.',
 '["Sécurité", "Penetration Testing", "Wireshark", "Linux", "Réseaux"]',
 1300.00, 6, '2025-03-01', '2025-08-31'),

(6, 'Développeur Jeux Vidéo', 
 'Stage en développement de jeux vidéo avec Unity ou Unreal Engine.',
 '["C#", "Unity", "Unreal Engine", "Game Design", "3D Modeling"]',
 1000.00, 4, '2025-04-01', '2025-07-31'),

(7, 'Ingénieur IA', 
 'Stage en recherche et développement en intelligence artificielle.',
 '["Python", "TensorFlow", "PyTorch", "NLP", "Deep Learning"]',
 1600.00, 6, '2025-03-01', '2025-08-31');

-- ============================================
-- Insertion des candidatures
-- ============================================
-- Note: les IDs des étudiants dépendent de l'ordre d'insertion
-- Admin=1, Pilotes=2,3, Etudiants=4,5,6,7,8

INSERT INTO candidatures (offre_id, etudiant_id, lettre_motivation, statut) VALUES
(1, 4, 'Je suis très intéressé par cette offre de développeur web. Mes compétences en PHP et JavaScript correspondent parfaitement à vos besoins.', 'en_attente'),
(2, 4, 'Passionné par le backend, je souhaite approfondir mes connaissances en Symfony.', 'en_attente'),
(3, 5, 'Les data m\'ont toujours fasciné. Je souhaite mettre mes compétences en Python au service de votre équipe.', 'acceptee'),
(6, 6, 'Créatif et passionné par le frontend, je serais ravi de rejoindre votre équipe.', 'en_attente'),
(7, 7, 'Intéressé par le cloud computing et les technologies DevOps.', 'refusee'),
(9, 8, 'La cybersécurité est mon domaine de prédilection. Je souhaite approfondir mes connaissances.', 'en_attente');

-- ============================================
-- Insertion des wishlists
-- ============================================
INSERT INTO wishlist (etudiant_id, offre_id) VALUES
(4, 1),
(4, 6),
(5, 3),
(5, 4),
(6, 6),
(6, 10),
(7, 7),
(8, 9);
