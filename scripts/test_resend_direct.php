<?php
/**
 * Test simple d'envoi d'email via Resend
 * Sans chargement de la config pour éviter les conflits
 */

// Charger l'autoloader PUIS la config
define('ROOT_PATH', realpath(__DIR__ . '/..'));
require_once ROOT_PATH . '/vendor/autoload.php';

// Charger la config APRES l'autoloader
require_once ROOT_PATH . '/config/config.php';

echo "Configuration chargée:\n";
echo "  RESEND_API_ENABLED: " . (RESEND_API_ENABLED ? "1" : "0") . "\n";
echo "  RESEND_API_KEY: " . (RESEND_API_KEY ? substr(RESEND_API_KEY, 0, 10) . "..." : "VIDE") . "\n";
echo "  RESEND_FROM: " . RESEND_FROM . "\n\n";

if (!RESEND_API_ENABLED || !RESEND_API_KEY) {
    echo "❌ Resend non configuré\n";
    exit(1);
}

try {
    echo "Tentative d'envoi...\n";
    
    // Utiliser l'API directement via cURL
    $payload = [
        'from' => RESEND_FROM,
        'to' => ['elmomelm@gmail.com'], // Adresse de test Resend
        'subject' => 'Test CesiStages',
        'html' => '<p>Test email</p>'
    ];

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . RESEND_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour développement seulement
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Pour développement seulement

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    echo "HTTP Code: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";

    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✓ Email envoyé avec succès!\n";
    } else {
        echo "❌ Erreur d'envoi\n";
        if ($curlErr) {
            echo "cURL Error: " . $curlErr . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
