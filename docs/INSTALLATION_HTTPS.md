# Configuration HTTPS pour XAMPP (Local)

Ce guide vous explique comment activer le HTTPS pour vos hôtes virtuels `cesi-site.local` et `cesi-static.local`.

## Étape 1 : Générer un certificat SSL auto-signé

XAMPP fournit un outil pour générer des certificats, mais le plus simple est de créer un certificat "wildcard" (pour tous les domaines) ou un certificat spécifique.

### Méthode simple (fichier unique)
1. Ouvrez un terminal dans `C:\xampp\apache\conf`.
2. Nous allons utiliser les clés par défaut de XAMPP pour simplifier (server.crt et server.key), ou en créer de nouvelles.

Pour une configuration propre, nous allons éditer le fichier de configuration SSL.

## Étape 2 : Configurer Apache pour le SSL

1. Ouvrez le fichier : `C:\xampp\apache\conf\extra\httpd-vhosts.conf`.
2. Ajoutez les blocs suivants (en dessous de vos blocs port 80 existants) :

```apache
# Site Principal (HTTPS)
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

# Ressources Statiques (HTTPS)
<VirtualHost *:443>
    ServerName cesi-static.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    
    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"
    
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        Header set Access-Control-Allow-Origin "*"
    </Directory>
</VirtualHost>
```

## Étape 3 : Configurer l'application

Votre fichier `config/config.php` détecte déjà automatiquement le HTTPS.
Cependant, pour forcer la redirection HTTP vers HTTPS, vous pouvez ajouter ceci dans votre fichier `public/.htaccess` :

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Étape 4 : Redémarrer Apache

Redémarrez Apache via le panneau de contrôle XAMPP.

## ⚠️ Attention (Navigateur)
Comme le certificat est "auto-signé", votre navigateur va afficher une alerte de sécurité ("Votre connexion n'est pas privée").
- Cliquez sur **Avancé** > **Continuer vers le site (non sécurisé)**.
- C'est normal en développement local.
