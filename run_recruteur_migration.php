<?php
/**
 * Script pour ajouter le rôle recruteur et créer le compte de démo
 * Exécuter une seule fois : php run_recruteur_migration.php
 * Ou via le navigateur : http://localhost/cesi-stages/run_recruteur_migration.php
 */

define('ROOT_PATH', __DIR__);
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

echo "<pre>\n";
echo "=== Migration: Ajout du rôle Recruteur ===\n\n";

try {
    // Connexion directe à la base de données
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // 1. Modifier l'ENUM pour ajouter 'recruteur'
    echo "1. Modification de l'ENUM role...\n";
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pilote', 'etudiant', 'recruteur') NOT NULL DEFAULT 'etudiant'");
    echo "   ✓ ENUM modifié avec succès\n\n";
    
    // 2. Vérifier si l'utilisateur existe déjà
    echo "2. Vérification de l'utilisateur recruteur@cesi.fr...\n";
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => 'recruteur@cesi.fr']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mettre à jour le rôle et le mot de passe
        echo "   L'utilisateur existe déjà (ID: {$existing['id']}). Mise à jour...\n";
        $hashedPassword = password_hash('recruteur123', PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET role = 'recruteur', password = :password, is_verified = 1 WHERE email = 'recruteur@cesi.fr'");
        $stmt->execute([':password' => $hashedPassword]);
        echo "   ✓ Utilisateur mis à jour\n\n";
    } else {
        // Créer l'utilisateur
        echo "   Création de l'utilisateur...\n";
        $hashedPassword = password_hash('recruteur123', PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            "INSERT INTO users (nom, prenom, email, password, role, is_verified) 
             VALUES ('Recruteur', 'Demo', 'recruteur@cesi.fr', :password, 'recruteur', 1)"
        );
        $stmt->execute([':password' => $hashedPassword]);
        echo "   ✓ Utilisateur créé avec succès (ID: " . $db->lastInsertId() . ")\n\n";
    }
    
    echo "=== Migration terminée avec succès ===\n\n";
    echo "Compte recruteur disponible:\n";
    echo "  Email: recruteur@cesi.fr\n";
    echo "  Mot de passe: recruteur123\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
