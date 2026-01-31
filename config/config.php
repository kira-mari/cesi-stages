<?php
/**
 * Configuration de l'application
 */

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
const BASE_URL = 'http://localhost/cesi-stages';
const ASSETS_URL = BASE_URL . '/assets';

// Sécurité
const SESSION_LIFETIME = 3600; // 1 heure
const CSRF_TOKEN_NAME = 'csrf_token';

// Pagination
const ITEMS_PER_PAGE = 10;

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
