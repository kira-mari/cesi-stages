<?php
/**
 * Script pour créer la table messages
 * Exécuter une seule fois: php run_messages_migration.php
 */

// Définir ROOT_PATH comme le fait public/index.php
define('ROOT_PATH', __DIR__);

require_once __DIR__ . '/config/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Connexion à la base de données réussie.\n";

    // Créer la table messages
    $sql = "CREATE TABLE IF NOT EXISTS messages (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "Table 'messages' créée avec succès.\n";

    echo "\n=== Migration terminée avec succès! ===\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
