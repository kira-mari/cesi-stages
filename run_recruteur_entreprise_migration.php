<?php
/**
 * Script pour créer la table recruteur_entreprise
 */

define('ROOT_PATH', __DIR__);
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

echo "<pre>\n";
echo "=== Migration: Table recruteur_entreprise ===\n\n";

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Créer la table
    echo "1. Création de la table recruteur_entreprise...\n";
    $db->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Table créée avec succès\n\n";
    
    // Assigner quelques entreprises au recruteur de démo (ID trouvé dans la migration précédente)
    echo "2. Assignation d'entreprises au recruteur de démo...\n";
    $stmt = $db->prepare("SELECT id FROM users WHERE email = 'recruteur@cesi.fr'");
    $stmt->execute();
    $recruteur = $stmt->fetch();
    
    if ($recruteur) {
        $recruteurId = $recruteur['id'];
        
        // Assigner les 3 premières entreprises au recruteur de démo
        $stmt = $db->prepare("SELECT id FROM entreprises LIMIT 3");
        $stmt->execute();
        $entreprises = $stmt->fetchAll();
        
        foreach ($entreprises as $e) {
            try {
                $stmt = $db->prepare("INSERT IGNORE INTO recruteur_entreprise (recruteur_id, entreprise_id) VALUES (?, ?)");
                $stmt->execute([$recruteurId, $e['id']]);
                echo "   ✓ Entreprise ID {$e['id']} assignée au recruteur\n";
            } catch (Exception $ex) {
                echo "   - Entreprise ID {$e['id']} déjà assignée\n";
            }
        }
    }
    
    echo "\n=== Migration terminée avec succès ===\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
