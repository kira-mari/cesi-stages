<?php
/**
 * Script pour ajouter le système d'approbation des comptes
 * Exécuter une seule fois: php run_approval_migration.php
 */

define('ROOT_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Connexion à la base de données réussie.\n";

    // Vérifier si la colonne existe déjà
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'is_approved'");
    if ($stmt->rowCount() > 0) {
        echo "La colonne 'is_approved' existe déjà.\n";
    } else {
        // Ajouter les colonnes
        $db->exec("ALTER TABLE users ADD COLUMN is_approved TINYINT(1) DEFAULT NULL");
        echo "Colonne 'is_approved' ajoutée.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'approval_requested_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN approval_requested_at DATETIME DEFAULT NULL");
        echo "Colonne 'approval_requested_at' ajoutée.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'approved_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN approved_at DATETIME DEFAULT NULL");
        echo "Colonne 'approved_at' ajoutée.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'approved_by'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN approved_by INT DEFAULT NULL");
        echo "Colonne 'approved_by' ajoutée.\n";
    }

    // Approuver automatiquement les pilotes/recruteurs existants
    $stmt = $db->prepare("UPDATE users SET is_approved = 1, approved_at = NOW() WHERE role IN ('pilote', 'recruteur') AND is_approved IS NULL");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "$count utilisateur(s) existant(s) approuvé(s) automatiquement.\n";

    echo "\n=== Migration terminée avec succès! ===\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
