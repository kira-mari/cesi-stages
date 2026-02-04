# 06 - Configuration HTTPS (SSL)

Pour sécuriser l'application en local et tester les fonctionnalités nécessitant HTTPS.

## 1. Certificats
### Option A : Certificats par défaut (Déconseillé)
XAMPP fournit des certificats par défaut dans `C:\xampp\apache\conf\ssl.crt\` et `ssl.key\`. 
⚠️ **Attention** : Ces certificats sont souvent expirés (date 2009-2019) et provoqueront des alertes de sécurité.

### Option B : Certificats valides avec mkcert (Recommandé)
Si vous avez l'outil `mkcert`, générez de nouveaux certificats :
1. Ouvrez un terminal administrateur (`cmd` ou `PowerShell`).
2. Exécutez : `mkcert localhost 127.0.0.1 ::1`
3. Deux fichiers seront créés (ex: `localhost+2.pem` et `localhost+2-key.pem`).
4. Renommez-les :
   - `localhost+2.pem` -> `server.crt`
   - `localhost+2-key.pem` -> `server.key`
5. Remplacez les fichiers originaux dans le dossier XAMPP :
   - Copiez `server.crt` dans `C:\xampp\apache\conf\ssl.crt\`
   - Copiez `server.key` dans `C:\xampp\apache\conf\ssl.key\`

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
