<?php
/**
 * Script de migration pour ajouter les colonnes de validation email
 */

// Définir ROOT_PATH avant d'inclure config
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

require_once __DIR__ . '/../config/config.php';

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "✓ Connexion à la base de données établie.\n";

    // Vérifier si les colonnes existent déjà
    $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'validation_code'");
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo "✓ Les colonnes de validation existent déjà.\n";
        exit(0);
    }

    // Exécuter la migration
    $queries = [
        "ALTER TABLE users ADD COLUMN validation_code VARCHAR(6) NULL COMMENT 'Code de validation d\'email (6 chiffres)'",
        "ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE COMMENT 'Email vérifié ou non'",
        "ALTER TABLE users ADD COLUMN validation_attempts INT DEFAULT 0 COMMENT 'Nombre de tentatives de validation'",
        "ALTER TABLE users ADD COLUMN validation_expires_at DATETIME NULL COMMENT 'Expiration du code de validation'",
        "CREATE INDEX idx_validation_code ON users(validation_code)"
    ];

    foreach ($queries as $query) {
        $db->exec($query);
        echo "✓ Exécuté : " . substr($query, 0, 50) . "...\n";
    }

    echo "\n✓ Migration terminée avec succès !\n";
} catch (PDOException $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
