<?php
/**
 * Configuration de l'application
 */

// Configuration de la base de données
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'cesi_stages');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// Configuration de l'application
const APP_NAME = 'CesiStages';
const APP_VERSION = '1.0.0';
const APP_ENV = 'development'; // development, production

// URLs
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Redirection automatique : localhost → cesi-site.local (pour cohérence du domaine)
// Uniquement si on n'est pas en ligne de commande (CLI)
if (php_sapi_name() !== 'cli' && $host !== 'cesi-site.local' && strpos($host, 'localhost') !== false) {
    // Vérification de l'existence de REQUEST_URI pour éviter les erreurs
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = str_replace('/cesi-stages', '', $requestUri);
    header('Location: ' . $protocol . '://cesi-site.local' . $uri);
    exit;
}

// Configuration spécifique pour les Virtual Hosts
if ($host === 'cesi-site.local') {
    define('BASE_URL', $protocol . '://cesi-site.local');
    
    // En développement local avec HTTPS, les navigateurs bloquent souvent les ressources d'un autre domaine (CORS/Certificat)
    // On utilise donc le même domaine pour les assets en HTTPS pour éviter que le style saute
    if ($protocol === 'https') {
        define('ASSETS_URL', BASE_URL . '/assets');
    } else {
        define('ASSETS_URL', $protocol . '://cesi-static.local/assets');
    }
} 
// Configuration par défaut (Localhost / IP)
else {
    define('BASE_URL', $protocol . '://' . $host . '/cesi-stages');
    define('ASSETS_URL', BASE_URL . '/assets');
}

// Google OAuth (SSO)
define('GOOGLE_OAUTH_ENABLED', true); // mettre à true après configuration
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
// Par défaut on utilise localhost pour le développement local
if (!defined('GOOGLE_REDIRECT')) {
    define('GOOGLE_REDIRECT', 'http://localhost/cesi-stages/auth/google-callback');
}

// Sécurité
const SESSION_LIFETIME = 3600; // 1 heure
const CSRF_TOKEN_NAME = 'csrf_token';

// Email (Brevo / SMTP)
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@cesistages.fr');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'CESI Stages');

// Pagination
const ITEMS_PER_PAGE = 9;

// Upload
const UPLOAD_MAX_SIZE = 5 * 1024 * 1024; // 5 Mo
const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx'];
const UPLOAD_PATH = ROOT_PATH . '/public/uploads';

// Gestion des erreurs
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
