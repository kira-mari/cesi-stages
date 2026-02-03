<?php
/**
 * Point d'entrée principal de l'application
 * CesiStages - Plateforme de recherche de stages
 */

// Définition des constantes
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', __DIR__);

// Chargement de l'autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

// Chargement de la configuration
require_once CONFIG_PATH . '/config.php';

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialisation du routeur
$router = new Core\Router();

// Chargement des routes
require_once CONFIG_PATH . '/routes.php';

// Dispatch de la requête
$router->dispatch();
