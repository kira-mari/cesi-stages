# Configuration de l'envoi d'emails

## 🔧 Configuration SMTP

Le système utilise **PHPMailer** pour envoyer les emails de validation.

### Option recommandée : Resend (service transactional)

Resend est un service d'envoi d'emails performant (API REST). Pour l'utiliser, définissez les variables d'environnement suivantes :

- `RESEND_API_ENABLED=1`
- `RESEND_API_KEY=your_resend_api_key`
- `RESEND_FROM=you@yourdomain.com` (optionnel)

La configuration est lue automatiquement par `config/config.php` et préférera Resend avant le fallback SMTP.

### En production (SMTP réel)

Modifiez le fichier `core/Mailer.php` dans la méthode `sendValidationEmail()` :

```php
$mail->Host = 'votre-serveur-smtp.com';
$mail->Port = 587; // ou 25, 465 selon votre serveur
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ou ENCRYPTION_SMTPS
$mail->SMTPAuth = true;
$mail->Username = 'votre-email@domaine.com';
$mail->Password = 'votre-mot-de-passe';
```

### En développement

**Option 1 : Fichier de log (recommandé)**
- Les codes sont sauvegardés dans `/storage/email_logs.txt`
- Idéal pour tester en local sans serveur SMTP

**Option 2 : Utiliser MailHog (Docker)**

```bash
docker pull mailhog/mailhog
docker run -p 1025:1025 -p 8025:8025 mailhog/mailhog
```

Puis modifier `core/Mailer.php` :
```php
$mail->Host = 'localhost';
$mail->Port = 1025;
```

Accédez à l'interface web : http://localhost:8025

**Option 3 : Utiliser Gmail / SendGrid / autre service**

Remplacez les paramètres SMTP par ceux du service.

## 📋 Fichier de log

En mode développement, consultez les codes de validation dans :

```
/storage/email_logs.txt
```

Format du log :
```
[2026-02-02 15:30:45] Email de validation envoyé à user@example.com
  Prénom: Jean
  Code: 123456

```

## ✅ Test

Pour tester le système :

1. Créez un nouveau compte via la page d'inscription
2. En développement, consultez `/storage/email_logs.txt` pour le code
3. En production, vérifiez votre boîte mail pour l'email avec le code

## 🔒 Configuration de sécurité

- Les codes expirent après **30 minutes**
- Limite de **3 tentatives** pour saisir le code
- Le compte est supprimé après 3 erreurs consécutives
