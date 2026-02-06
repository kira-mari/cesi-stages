-- ============================================
-- Migration: Table de liaison recruteur-entreprise
-- ============================================

USE cesi_stages;

-- Table de liaison entre recruteurs et entreprises
CREATE TABLE IF NOT EXISTS recruteur_entreprise (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recruteur_id INT NOT NULL,
    entreprise_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruteur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE CASCADE,
    UNIQUE KEY unique_relation (recruteur_id, entreprise_id),
    INDEX idx_recruteur (recruteur_id),
    INDEX idx_entreprise (entreprise_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
