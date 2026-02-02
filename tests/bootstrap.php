<?php
// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Définir le chemin racine
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Charger la configuration
require_once __DIR__ . '/../config/config.php';

// Définir le chemin de l'application s'il n'est pas déjà défini
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__) . '/src');
}
