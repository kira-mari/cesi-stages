# Guide : Avoir le HTTPS vert (Sécurisé) en local avec mkcert

Pour ne plus avoir l'alerte "Votre connexion n'est pas privée", il faut que votre ordinateur "fasse confiance" au certificat. La méthode la plus simple et professionnelle est d'utiliser l'outil **mkcert**.

## Étape 1 : Télécharger mkcert
1. Allez sur la page des releases de mkcert : [https://github.com/FiloSottile/mkcert/releases](https://github.com/FiloSottile/mkcert/releases)
2. Téléchargez le fichier pour Windows (ex: `mkcert-v1.4.4-windows-amd64.exe`).
3. Renommez le fichier téléchargé en `` pour simplifier.
4. Placez ce fichier dans un dossier (par exemple `C:\xampp\certs\`).

## Étape 2 : Créer l'autorité de certification (CA)
Ouvrez votre terminal (PowerShell ou CMD) **en tant qu'administrateur**, allez dans le dossier où vous avez mis l'outil et lancez :

```powershell
cd C:\xampp\certs\
.\mkcert.exe -install
```
> Une fenêtre Windows va s'ouvrir vous demandant de confirmer l'ajout du certificat racine. Cliquez sur **Oui**.

## Étape 3 : Générer les certificats pour votre site
Toujours dans le terminal, générez un certificat unique qui couvrira tous vos noms de domaine locaux :

```powershell
.\mkcert.exe cesi-site.local cesi-static.local localhost 127.0.0.1
```

Cela va créer deux fichiers dans le dossier :
- `cesi-site.local+3.pem` (Le certificat)
- `cesi-site.local+3-key.pem` (La clé privée)

Renommez-les pour simplifier la configuration :
- `cesi-site.local+3.pem` -> **server.crt**
- `cesi-site.local+3-key.pem` -> **server.key**

## Étape 4 : Installer les certificats dans Apache
1. Copiez vos deux nouveaux fichiers (`server.crt` et `server.key`).
2. Allez dans le dossier de configuration d'Apache : `C:\xampp\apache\conf\`.
3. Créez un dossier nommé `local-certs`.
4. Collez les fichiers dedans.

Vous devriez avoir :
- `C:\xampp\apache\conf\local-certs\server.crt`
- `C:\xampp\apache\conf\local-certs\server.key`

## Étape 5 : Mettre à jour la configuration VirtualHost
Modifiez votre fichier `C:\xampp\apache\conf\extra\httpd-vhosts.conf` pour pointer vers ces nouveaux fichiers :

```apache
# Site Principal (HTTPS)
<VirtualHost *:443>
    ServerName cesi-site.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    
    SSLEngine on
    # CHEMINS MODIFIÉS ICI :
    SSLCertificateFile "conf/local-certs/server.crt"
    SSLCertificateKeyFile "conf/local-certs/server.key"
    
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
    # CHEMINS MODIFIÉS ICI :
    SSLCertificateFile "conf/local-certs/server.crt"
    SSLCertificateKeyFile "conf/local-certs/server.key"
    
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        Header set Access-Control-Allow-Origin "*"
    </Directory>
</VirtualHost>
```

## Étape 6 : Redémarrer
1. Redémarrez Apache via XAMPP.
2. Redémarrez votre navigateur (important pour vider le cache des certificats).
3. Accédez à `https://cesi-site.local`. Le cadenas devrait être vert/fermé sans avertissement.

## 🐛 Dépannage : "L'ancien certificat persiste"

Si vous voyez toujours "Non sécurisé" ou l'ancien certificat après avoir tout configuré :

1.  **Testez en Navigation Privée** : Les navigateurs gardent les certificats en cache. Ouvrez une fenêtre `Incognito` ou `Privée` et réessayez. Si ça marche, c'est juste le cache de votre navigateur.
2.  **Vérifiez le chemin dans Apache** : Assurez-vous que votre fichier `httpd-vhosts.conf` pointe bien vers le **dossier** où vous avez mis les nouveaux certificats (exemple : `conf/local-certs/server.crt`) et **pas** vers les anciens (`conf/ssl.crt/...`).
3.  **Redémarrez Vraiment Apache** : Parfois le bouton "Stop/Start" de XAMPP ne tue pas tous les processus.
    *   Fermez complètement XAMPP.
    *   Ouvrez le Gestionnaire des tâches (Ctrl+Shift+Esc).
    *   Cherchez `httpd.exe` et forcez la fin de tâche s'il est encore là.
    *   Relancez XAMPP et Apache.

