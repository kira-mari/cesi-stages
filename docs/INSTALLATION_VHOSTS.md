# Configuration des Hôtes Virtuels (Virtual Hosts)

Pour finaliser la mise en place de `cesi-site.local` et `cesi-static.local`, vous devez configurer votre environnement local (Windows/XAMPP). Je ne peux pas modifier ces fichiers système pour vous.

## Étape 1 : Modifier le fichier hosts Windows
1. Ouvrez le **Bloc-notes** en tant qu'**Administrateur**.
2. Ouvrez le fichier : `C:\Windows\System32\drivers\etc\hosts`
3. Ajoutez ces lignes à la fin du fichier :

```
127.0.0.1 cesi-site.local
127.0.0.1 cesi-static.local
```
4. Sauvegardez le fichier.

## Étape 2 : Configurer Apache (XAMPP)
1. Ouvrez le fichier de configuration des vhosts : `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
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

# Ressources Statiques (JS, Images, CSS)
<VirtualHost *:80>
    ServerName cesi-static.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Autoriser les requêtes Cross-Origin (CORS) pour que le site principal puisse charger les polices/scripts
        Header set Access-Control-Allow-Origin "*"
    </Directory>
</VirtualHost>
```

## Étape 3 : Redémarrer Apache
1. Ouvrez le panneau de contrôle XAMPP.
2. Arrêtez le module **Apache** (Stop).
3. Relancez-le (Start).

Une fois ceci fait, accédez à : http://cesi-site.local
