<?php
/**
 * Script de migration pour créer la table pending_registrations
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

require_once ROOT_PATH . '/config/config.php';

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "✓ Connexion à la base de données établie.\n";

    // Vérifier si la table existe
    $stmt = $db->prepare("SHOW TABLES LIKE 'pending_registrations'");
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo "✓ Table pending_registrations existe déjà.\n";
        exit(0);
    }

    // Créer la table
    $db->exec(<<<SQL
        CREATE TABLE pending_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            validation_code VARCHAR(6) NOT NULL,
            validation_attempts INT DEFAULT 0,
            validation_expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_validation_code (validation_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);

    echo "✓ Table pending_registrations créée avec succès.\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
