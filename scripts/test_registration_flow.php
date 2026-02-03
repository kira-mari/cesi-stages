<?php
/**
 * Test complet du flux d'inscription avec envoi d'email
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/vendor/autoload.php';

use Models\PendingRegistration;
use Core\Mailer;

echo "=== TEST FLUX D'INSCRIPTION AVEC VALIDATION EMAIL ===\n\n";

// Test d'envoi d'un email de validation
echo "1. Test d'envoi d'email de validation\n";
echo "   Prénom: Jean\n";
echo "   Email: test@example.com (sera redirigé à elmomelm@gmail.com)\n";

$code = Mailer::generateValidationCode();
echo "   Code généré: " . $code . "\n";

if (Mailer::sendValidationEmail('test@example.com', 'Jean', $code)) {
    echo "   ✓ Email envoyé avec succès!\n\n";
} else {
    echo "   ❌ Erreur lors de l'envoi de l'email\n\n";
}

// Test d'enregistrement en attente
echo "2. Test de création d'inscription en attente\n";

$pendingModel = new PendingRegistration();

// Nettoyer les anciens tests
$pendingModel->deleteByEmail('test@example.com');

$pendingId = $pendingModel->create([
    'nom' => 'Dupont',
    'prenom' => 'Jean',
    'email' => 'test@example.com',
    'password' => password_hash('password123', PASSWORD_BCRYPT),
    'validation_code' => $code,
    'validation_expires_at' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
]);

if ($pendingId) {
    echo "   ✓ Inscription en attente créée (ID: " . $pendingId . ")\n\n";
    
    // Test de recherche par email
    echo "3. Test de recherche par email\n";
    $pending = $pendingModel->findByEmail('test@example.com');
    if ($pending) {
        echo "   ✓ Inscription trouvée\n";
        echo "     - Email: " . $pending['email'] . "\n";
        echo "     - Code: " . $pending['validation_code'] . "\n";
        echo "     - Créée le: " . $pending['created_at'] . "\n\n";
    } else {
        echo "   ❌ Inscription non trouvée\n\n";
    }
    
    // Test de recherche par code
    echo "4. Test de recherche par code de validation\n";
    $pendingByCode = $pendingModel->findByCode($code);
    if ($pendingByCode) {
        echo "   ✓ Inscription trouvée par code\n";
        echo "     - Email: " . $pendingByCode['email'] . "\n";
        echo "     - Prénom: " . $pendingByCode['prenom'] . "\n\n";
    } else {
        echo "   ❌ Inscription non trouvée par code\n\n";
    }
} else {
    echo "   ❌ Erreur lors de la création de l'inscription\n\n";
}

echo "=== TEST TERMINÉ ===\n";
echo "\n✓ Vous devriez avoir reçu un email de validation à elmomelm@gmail.com\n";
