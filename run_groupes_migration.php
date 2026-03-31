<?php
/**
 * Exécute la migration 009 - Groupes d'étudiants pour les pilotes
 */
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();
require_once ROOT_PATH . '/config/config.php';

$sql = file_get_contents(ROOT_PATH . '/database/migrations/009_add_groupes.sql');
$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER,
    DB_PASS
);
$pdo->exec($sql);
echo "Migration 009 (groupes) exécutée avec succès.\n";
