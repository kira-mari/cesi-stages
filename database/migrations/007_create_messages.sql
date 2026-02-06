-- ============================================
-- Migration: Système de messagerie interne
-- ============================================

USE cesi_stages;

-- Table des messages
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    lu_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expediteur (expediteur_id),
    INDEX idx_destinataire (destinataire_id),
    INDEX idx_lu (lu),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
