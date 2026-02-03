<?php
namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Utilitaire pour envoyer des emails
 */
class Mailer
{
    /**
     * Envoie un email de validation avec code
     *
     * @param string $email Email destinataire
     * @param string $prenom Prénom de l'utilisateur
     * @param string $code Code de validation (6 chiffres)
     * @return bool
     */
    public static function sendValidationEmail($email, $prenom, $code)
    {
        $subject = 'Confirmez votre email - ' . APP_NAME;
        
        $htmlMessage = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { color: #333; text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #0066cc; }
        .content { color: #555; line-height: 1.6; }
        .code { font-size: 28px; font-weight: bold; text-align: center; letter-spacing: 3px; 
                background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px; font-family: monospace; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; }
        .warning { color: #d9534f; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmez votre email</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong>{$prenom}</strong>,</p>
            <p>Merci d'avoir créé un compte sur <strong>CesiStages</strong>. Pour finir votre inscription, veuillez utiliser le code de validation ci-dessous :</p>
            <div class="code">{$code}</div>
            <p>Ce code est valable <strong>30 minutes</strong>.</p>
            <p><strong>Important :</strong> Ne partagez ce code avec personne.</p>
        </div>
        <div class="footer">
            <p>Vous n'avez pas créé de compte ? Ignorez cet email.</p>
            <p><small class="warning">Cet email a été envoyé automatiquement, veuillez ne pas y répondre.</small></p>
        </div>
    </div>
</body>
</html>
HTML;

        // Priorité : utiliser Resend si activé
            if (defined('RESEND_API_ENABLED') && RESEND_API_ENABLED && defined('RESEND_API_KEY') && RESEND_API_KEY) {
                // En développement avec Resend en mode test, rediriger vers l'adresse autorisée
                $toEmail = $email;
                if (APP_ENV === 'development' && defined('RESEND_API_KEY') && RESEND_API_KEY) {
                    // Ajouter une note dans le message si on redirige
                    if (strpos(RESEND_API_KEY, 're_') === 0) { // API key Resend commence par 're_'
                        // Inclure l'email original dans le HTML pour référence
                        $htmlMessage .= "\n<!-- Email original: " . htmlspecialchars($email) . " -->";
                    }
                    $toEmail = 'elmomelm@gmail.com'; // Adresse autorisée par Resend en test
                }

                // Si la lib officielle est installée, on l'utilise
                if (class_exists('\Resend\Resend')) {
                    try {
                        $resend = new \Resend\Resend(RESEND_API_KEY);
                        $from = (defined('RESEND_FROM') ? RESEND_FROM : 'noreply@cesi-stages.local');
                        $params = [
                            'from' => $from,
                            'to' => [$toEmail],
                            'subject' => $subject,
                            'html' => $htmlMessage
                        ];
                        $resend->emails->send($params);
                        return true;
                    } catch (\Throwable $e) {
                        error_log('Resend client error: ' . $e->getMessage());
                        // fallback to curl below
                    }
                }

                // Fallback curl si la lib officielle n'est pas installée
                $payload = [
                    'from' => (defined('RESEND_FROM') ? RESEND_FROM : 'onboarding@resend.dev'),
                    'to' => [$toEmail],
                    'subject' => $subject,
                    'html' => $htmlMessage
                ];

                $ch = curl_init('https://api.resend.com/emails');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . RESEND_API_KEY
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErr = curl_error($ch);
                curl_close($ch);

                if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
                    return true;
                }

                error_log('Resend error (HTTP ' . $httpCode . '): ' . $curlErr . ' / Response: ' . $response);
                // Si Resend échoue, on utilisera uniquement le fallback log en dev
            }

            // Aucun PHPMailer : fallback direct vers log en développement
            if (APP_ENV === 'development') {
                return self::logValidationEmail($email, $prenom, $code);
            }

            return false;
    }

    /**
     * Log un email de validation dans un fichier (fallback pour développement)
     *
     * @param string $email Email destinataire
     * @param string $prenom Prénom de l'utilisateur
     * @param string $code Code de validation
     * @return bool
     */
    private static function logValidationEmail($email, $prenom, $code)
    {
        $logFile = ROOT_PATH . '/storage/email_logs.txt';
        $logDir = dirname($logFile);
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $message = sprintf(
            "[%s] Email de validation envoyé à %s\n  Prénom: %s\n  Code: %s\n\n",
            date('Y-m-d H:i:s'),
            $email,
            $prenom,
            $code
        );
        
        return (bool)@file_put_contents($logFile, $message, FILE_APPEND);
    }

    /**
     * Génère un code de validation aléatoire (6 chiffres)
     *
     * @return string
     */
    public static function generateValidationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
