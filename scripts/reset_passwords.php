<?php
// Script CLI pour réinitialiser des mots de passe d'utilisateurs spécifiques
// Usage: php scripts/reset_passwords.php

if (php_sapi_name() !== 'cli') {
    echo "Ce script doit être exécuté en CLI.\n";
    exit(1);
}

// Définir ROOT_PATH si non défini (utilisé par config.php)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

require __DIR__ . '/../config/config.php';

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

$map = [
    'admin@cesi.fr' => 'admin123',
    'pilote@cesi.fr' => 'pilote123',
    'etudiant@cesi.fr' => 'etudiant123',
];

$updateStmt = $pdo->prepare('UPDATE users SET password = :password WHERE email = :email');

$results = [];
foreach ($map as $email => $plain) {
    $hash = password_hash($plain, PASSWORD_BCRYPT);
    try {
        $updateStmt->execute([':password' => $hash, ':email' => $email]);
        $count = $updateStmt->rowCount();
        if ($count > 0) {
            $results[] = [
                'email' => $email,
                'status' => 'updated',
                'affected_rows' => $count
            ];
        } else {
            $results[] = [
                'email' => $email,
                'status' => 'not_found',
                'affected_rows' => 0
            ];
        }
    } catch (Exception $e) {
        $results[] = [
            'email' => $email,
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

// Affichage résumé
foreach ($results as $r) {
    if ($r['status'] === 'updated') {
        echo sprintf("[OK] %s — mots de passe réinitialisé (%d ligne(s) mise(s) à jour)\n", $r['email'], $r['affected_rows']);
    } elseif ($r['status'] === 'not_found') {
        echo sprintf("[WARN] %s — utilisateur introuvable (aucune ligne mise à jour)\n", $r['email']);
    } else {
        echo sprintf("[ERR] %s — erreur: %s\n", $r['email'], $r['message']);
    }
}

echo "Terminé.\n";
