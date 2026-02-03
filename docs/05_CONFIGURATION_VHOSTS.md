# 05 - Configuration Virtual Hosts (VHosts)

Utiliser des VHosts permet d'avoir une URL propre (`http://cesi-site.local`) au lieu de `localhost/cesi-stages/public`.

## 1. Modifier le fichier HOSTS Windows
1. Ouvrez le **Bloc-notes en tant qu'administrateur**.
2. Ouvrez le fichier `C:\Windows\System32\drivers\etc\hosts`.
3. Ajoutez la ligne suivante à la fin :
   ```
   127.0.0.1 cesi-site.local
   127.0.0.1 cesi-static.local
   ```
4. Sauvegardez.

## 2. Configurer Apache (XAMPP)
1. Ouvrez `C:\xampp\apache\conf\extra\httpd-vhosts.conf`.
2. Ajoutez la configuration suivante à la fin du fichier :

```apache
# Site Principal
<VirtualHost *:80>
    ServerName cesi-site.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Domaine statique (optionnel pour les assets)
<VirtualHost *:80>
    ServerName cesi-static.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        Header set Access-Control-Allow-Origin "*"
    </Directory>
</VirtualHost>
```

3. **Redémarrez Apache** via le panneau XAMPP.

---
👉 **Étape suivante :** [Configuration HTTPS (SSL)](06_CONFIGURATION_HTTPS.md)
