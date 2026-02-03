<?php
/**
 * Script de test pour envoyer un email via Resend
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

require_once ROOT_PATH . '/config/config.php';

echo "=== TEST D'ENVOI EMAIL VIA RESEND ===\n\n";

// Afficher la configuration
echo "Configuration:\n";
echo "  RESEND_API_ENABLED: " . (defined('RESEND_API_ENABLED') && RESEND_API_ENABLED ? "1 (activé)" : "0 (désactivé)") . "\n";
echo "  RESEND_API_KEY: " . (defined('RESEND_API_KEY') && RESEND_API_KEY ? substr(RESEND_API_KEY, 0, 10) . "..." : "non défini") . "\n";
echo "  RESEND_FROM: " . (defined('RESEND_FROM') && RESEND_FROM ? RESEND_FROM : "non défini") . "\n";
echo "  APP_ENV: " . APP_ENV . "\n\n";

if (!defined('RESEND_API_ENABLED') || !RESEND_API_ENABLED) {
    echo "❌ RESEND_API_ENABLED n'est pas activé (RESEND_API_ENABLED=1 dans .env.local)\n";
    exit(1);
}

if (!defined('RESEND_API_KEY') || !RESEND_API_KEY) {
    echo "❌ RESEND_API_KEY n'est pas défini\n";
    exit(1);
}

echo "✓ Configuration OK\n\n";

// Charger l'autoloader APRÈS config
require_once ROOT_PATH . '/vendor/autoload.php';

// Test 1 : Vérifier que la classe Resend est chargée
echo "Test 1: Vérifier la classe Resend...\n";
if (!class_exists('\Resend\Resend')) {
    echo "❌ Classe Resend\\Resend non trouvée\n";
    exit(1);
}
echo "✓ Classe Resend\\Resend chargée\n\n";

// Test 2 : Instancier Resend et essayer d'envoyer un email
echo "Test 2: Envoyer un email de test...\n";
try {
    $resend = new \Resend\Resend(RESEND_API_KEY);
    
    $params = [
        'from' => RESEND_FROM,
        'to' => ['test@cesi-stages.local'],
        'subject' => 'Test Resend - CesiStages',
        'html' => '<strong>Test d\'envoi</strong><p>Si vous recevez cet email, Resend fonctionne!</p>'
    ];
    
    echo "Paramètres envoyés à Resend:\n";
    echo "  from: " . $params['from'] . "\n";
    echo "  to: " . implode(', ', $params['to']) . "\n";
    echo "  subject: " . $params['subject'] . "\n\n";
    
    $result = $resend->emails->send($params);
    
    echo "✓ Réponse de Resend reçue\n";
    echo "Résultat:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($result && isset($result['id'])) {
        echo "✓ Email envoyé avec succès! ID: " . $result['id'] . "\n";
    } else {
        echo "⚠ Réponse reçue mais pas d'ID d'email\n";
    }
    
} catch (\Throwable $e) {
    echo "❌ Erreur lors de l'envoi:\n";
    echo "  Type: " . get_class($e) . "\n";
    echo "  Message: " . $e->getMessage() . "\n";
    echo "  Code: " . $e->getCode() . "\n";
    if (method_exists($e, 'getResponse')) {
        echo "  Réponse: " . $e->getResponse() . "\n";
    }
    exit(1);
}

echo "\n=== TEST TERMINÉ ===\n";
