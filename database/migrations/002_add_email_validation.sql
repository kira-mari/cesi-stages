-- ============================================
-- Migration: Ajouter colonnes de validation email
-- ============================================

-- Ajouter les colonnes pour la validation d'email
ALTER TABLE users
ADD COLUMN validation_code VARCHAR(6) NULL COMMENT 'Code de validation d\'email (6 chiffres)',
ADD COLUMN email_verified BOOLEAN DEFAULT FALSE COMMENT 'Email vérifié ou non',
ADD COLUMN validation_attempts INT DEFAULT 0 COMMENT 'Nombre de tentatives de validation',
ADD COLUMN validation_expires_at DATETIME NULL COMMENT 'Expiration du code de validation';

-- Créer un index pour les recherches de code de validation
CREATE INDEX idx_validation_code ON users(validation_code);
