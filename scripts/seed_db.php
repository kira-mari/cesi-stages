<?php
/**
 * Script de peuplement de la base de données
 * Usage: php scripts/seed_db.php
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/vendor/autoload.php';

// Chargement de l'environnement
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

require_once ROOT_PATH . '/config/config.php';

echo "=== Initialisation du peuplement de la base de données ===\n";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "Connexion à la base de données réussie.\n";

    // Désactiver les contraintes de clés étrangères
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Liste des tables à supprimer (ordre inverse des dépendances)
    $tables = ['wishlist', 'candidatures', 'pilote_etudiant', 'evaluations', 'offres', 'entreprises', 'users'];
    
    foreach ($tables as $table) {
        echo "Suppression de la table $table...\n";
        $pdo->exec("DROP TABLE IF EXISTS $table");
    }

    // Lire et executer le fichier de migration (création des tables)
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

    // Exécuter les requêtes
    // Note: PDO ne permet pas toujours d'exécuter plusieurs requêtes d'un coup de manière fiable avec exec()
    // On va nettoyer le SQL des commentaires et le diviser si nécessaire, 
    // ou simplement utiliser exec() qui supporte souvent le multi-statement si configuré (MySQL le supporte par défaut via PDO quand emulate prepares est off ou avec config spécifique)
    
    // Pour être sûr, on execute le gros bloc. Si ça échoue, on découpera.
    // Le fichier contient "USE cesi_stages;", cela peut poser problème si la DB est différente, on va le retirer ou l'ignorer.
    
    echo "Importation des données depuis insert_data_full.sql...\n";
    $pdo->exec($sql);

    // Réactiver les contraintes
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "=== Peuplement terminé avec succès ! ===\n";

} catch (PDOException $e) {
    echo "Erreur Base de Données : " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
