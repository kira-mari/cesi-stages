-- ============================================
-- Migration: Ajout du rôle recruteur
-- ============================================

USE cesi_stages;

-- Modifier l'ENUM pour ajouter 'recruteur'
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pilote', 'etudiant', 'recruteur') NOT NULL DEFAULT 'etudiant';

-- Ajouter l'utilisateur recruteur de démo
-- Mot de passe: recruteur123 (hash bcrypt de 'password')
INSERT INTO users (nom, prenom, email, password, role, is_verified) VALUES
('Recruteur', 'Demo', 'recruteur@cesi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruteur', 1)
ON DUPLICATE KEY UPDATE role = 'recruteur';
