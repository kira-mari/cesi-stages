-- ============================================
-- Groupes d'étudiants par pilote
-- ============================================

-- Table: groupes (créés par les pilotes)
CREATE TABLE IF NOT EXISTS groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pilote_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pilote_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pilote (pilote_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: groupe_etudiant (étudiants dans chaque groupe)
CREATE TABLE IF NOT EXISTS groupe_etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    groupe_id INT NOT NULL,
    etudiant_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (groupe_id) REFERENCES groupes(id) ON DELETE CASCADE,
    FOREIGN KEY (etudiant_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_groupe_etudiant (groupe_id, etudiant_id),
    -- Un étudiant ne peut être que dans un seul groupe par pilote (groupe appartient à un pilote)
    INDEX idx_groupe (groupe_id),
    INDEX idx_etudiant (etudiant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
