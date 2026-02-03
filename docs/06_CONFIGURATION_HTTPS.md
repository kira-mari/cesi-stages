# 06 - Configuration HTTPS (SSL)

Pour sécuriser l'application en local et tester les fonctionnalités nécessitant HTTPS.

## 1. Certificats
XAMPP fournit des certificats par défaut dans `C:\xampp\apache\conf\ssl.crt\` et `ssl.key\`. Nous allons utiliser ceux-ci (`server.crt` et `server.key`).

## 2. Configurer Apache pour le SSL
1. Ouvrez à nouveau `C:\xampp\apache\conf\extra\httpd-vhosts.conf`.
2. Ajoutez la version HTTPS de votre VHost (en dessous de la version port 80) :

```apache
<VirtualHost *:443>
    ServerName cesi-site.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    
    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"
    
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 3. Validation
1. **Redémarrez Apache**.
2. Accédez à `https://cesi-site.local`.
3. Le navigateur affichera une alerte de sécurité (car le certificat est auto-signé). Cliquez sur **Avancé > Continuer vers le site**.

---
👉 **Étape suivante :** [Lancement et Tests](07_DEMARRAGE.md)
