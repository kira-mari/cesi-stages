<?php
/**
 * Script de configuration complète de la base de données
 * - Détecte si la base existe, la supprime si oui et la recrée
 * - Sinon, la crée directement
 * - Exécute les migrations et seeds
 * Usage: php scripts/seed_db.php
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/vendor/autoload.php';

// Chargement de l'environnement
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

require_once ROOT_PATH . '/config/config.php';

echo "=== Configuration complète de la base de données ===\n";

try {
    // Connexion sans spécifier la base de données pour pouvoir la créer
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "Connexion au serveur MySQL réussie.\n";

    // Vérifier si la base de données existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->fetch();

    if ($dbExists) {
        echo "La base de données '" . DB_NAME . "' existe. Suppression en cours...\n";
        $pdo->exec("DROP DATABASE `" . DB_NAME . "`");
        echo "Base de données supprimée.\n";
    } else {
        echo "La base de données '" . DB_NAME . "' n'existe pas.\n";
    }

    // Créer la base de données
    echo "Création de la base de données '" . DB_NAME . "'...\n";
    $pdo->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de données créée.\n";

    // Se connecter à la base de données nouvellement créée
    $pdo->exec("USE `" . DB_NAME . "`");

    // Lire et exécuter le fichier de migration (création des tables)
    $migrationFile = ROOT_PATH . '/database/migrations/create_tables_full.sql';
    if (!file_exists($migrationFile)) {
        throw new Exception("Le fichier de migration n'existe pas : $migrationFile");
    }
    echo "Exécution de la migration...\n";
    $sqlMigration = file_get_contents($migrationFile);
    $pdo->exec($sqlMigration);

    // Lire le fichier SQL de seeds
    $sqlFile = ROOT_PATH . '/database/seeds/insert_data_full.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Le fichier SQL n'existe pas : $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // Exécuter les requêtes de seed
    echo "Importation des données depuis insert_data_full.sql...\n";
    $pdo->exec($sql);

    echo "=== Configuration de la base de données terminée avec succès ! ===\n";

} catch (PDOException $e) {
    echo "Erreur Base de Données : " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
