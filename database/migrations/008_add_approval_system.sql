-- ============================================
-- Migration: Système d'approbation des comptes
-- ============================================
-- Les pilotes et recruteurs doivent être approuvés par un admin
-- avant d'avoir accès à leurs fonctionnalités

USE cesi_stages;

-- Ajouter le champ is_approved (NULL = pas besoin d'approbation, 0 = en attente, 1 = approuvé)
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT NULL;

-- Ajouter la date de demande d'approbation
ALTER TABLE users ADD COLUMN IF NOT EXISTS approval_requested_at DATETIME DEFAULT NULL;

-- Ajouter la date d'approbation
ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_at DATETIME DEFAULT NULL;

-- Ajouter l'ID de l'admin qui a approuvé
ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_by INT DEFAULT NULL;

-- Index pour les requêtes d'approbation
CREATE INDEX IF NOT EXISTS idx_is_approved ON users(is_approved);

-- Les utilisateurs existants (pilote/recruteur) sont automatiquement approuvés
UPDATE users SET is_approved = 1, approved_at = NOW() WHERE role IN ('pilote', 'recruteur') AND is_approved IS NULL;

-- Les étudiants et admins n'ont pas besoin d'approbation (reste NULL)
