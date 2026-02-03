<?php
/**
 * Configuration de l'application
 */

// Charger les variables d'environnement depuis .env.local
function loadEnvFile($filePath) {
    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            // Chercher le signe "="
            if (strpos($line, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Ignorer les variables vides
            if (empty($key) || empty($value)) {
                continue;
            }
            
            // Charger en tant que variable d'environnement si elle n'existe pas
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Charger les variables depuis .env.local si le fichier existe
if (defined('ROOT_PATH')) {
    loadEnvFile(ROOT_PATH . '/.env.local');
}

// Configuration de la base de données
const DB_HOST = 'localhost';
const DB_NAME = 'cesi_stages';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// Configuration de l'application
const APP_NAME = 'CesiStages';
const APP_VERSION = '1.0.0';
const APP_ENV = 'development'; // development, production

// URLs
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Redirection automatique : localhost → cesi-site.local (pour cohérence du domaine)
// Ne pas effectuer de redirection en CLI ou si REQUEST_URI est absent (ex: tests)
if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI']) && $host !== 'cesi-site.local' && strpos($host, 'localhost') !== false) {
    $uri = str_replace('/cesi-stages', '', $_SERVER['REQUEST_URI']);
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
const GOOGLE_OAUTH_ENABLED = true; // mettre à true après configuration
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
}
// Par défaut on utilise localhost pour le développement local
if (!defined('GOOGLE_REDIRECT')) {
    define('GOOGLE_REDIRECT', 'http://localhost/cesi-stages/auth/google-callback');
}

// Sécurité
const SESSION_LIFETIME = 3600; // 1 heure
const CSRF_TOKEN_NAME = 'csrf_token';

// Pagination
const ITEMS_PER_PAGE = 9;

// Upload
const UPLOAD_MAX_SIZE = 5 * 1024 * 1024; // 5 Mo
const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx'];
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');
}

// Mail (Resend)
if (!defined('RESEND_API_ENABLED')) {
    define('RESEND_API_ENABLED', getenv('RESEND_API_ENABLED') === '1');
}
if (!defined('RESEND_API_KEY')) {
    define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: '');
}
if (!defined('RESEND_FROM')) {
    define('RESEND_FROM', getenv('RESEND_FROM') ?: 'noreply@cesi-stages.local');
}

// Gestion des erreurs
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
