# Guide d'Installation - CesiStages

Ce document explique comment configurer le projet CesiStages sur votre machine locale.

## Prérequis

- **XAMPP** (ou WAMP/MAMP) avec :
  - PHP 8.0 ou supérieur
  - MySQL 5.7 ou supérieur
  - Apache
- **Composer** (gestionnaire de dépendances PHP)
- **Git**

## 1. Cloner le projet

```bash
cd C:\xampp\htdocs
git clone https://github.com/votre-repo/cesi-stages.git
cd cesi-stages
```

## 2. Installer les dépendances PHP

```bash
composer install
```

## 3. Configuration de la base de données

### 3.1. Créer la base de données

1. Ouvrez **phpMyAdmin** : http://localhost/phpmyadmin
2. Créez une nouvelle base de données nommée `cesi_stages`
3. Encodage : `utf8mb4_unicode_ci`

### 3.2. Importer le schéma

Exécutez le fichier SQL de création des tables :

```bash
# Dans phpMyAdmin, importez ce fichier :
database/migrations/create_tables_full.sql
```

Ou via la ligne de commande :
```bash
mysql -u root -p cesi_stages < database/migrations/create_tables_full.sql
```

### 3.3. Importer les données de test (optionnel)

```bash
# Dans phpMyAdmin, importez ce fichier :
database/seeds/insert_data_full.sql
```

### 3.4. Exécuter les migrations supplémentaires

Si vous avez besoin des fonctionnalités récentes (recruteurs, messagerie) :

```bash
# Dans le dossier du projet
php run_recruteur_migration.php
php run_recruteur_entreprise_migration.php
php run_messages_migration.php
```

## 4. Configuration de l'environnement

### 4.1. Créer le fichier .env

Copiez le fichier exemple et modifiez-le :

```bash
copy .env.example .env
```

### 4.2. Configurer le fichier .env

Ouvrez `.env` et modifiez les valeurs :

```env
# Base de données
DB_HOST=localhost
DB_NAME=cesi_stages
DB_USER=root
DB_PASS=

# Google OAuth (optionnel)
GOOGLE_CLIENT_ID=votre_client_id
GOOGLE_CLIENT_SECRET=votre_client_secret

# SMTP pour les emails (optionnel)
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=votre_utilisateur
SMTP_PASS=votre_mot_de_passe
SMTP_FROM_EMAIL=votre_email@example.com
SMTP_FROM_NAME=CESI Stages
```

## 5. Configuration du serveur web

### Option A : Utiliser localhost (simple)

Accédez au site via :
```
http://localhost/cesi-stages/public
```

### Option B : Virtual Host (recommandé)

#### 5.1. Modifier le fichier hosts

Ouvrez `C:\Windows\System32\drivers\etc\hosts` en tant qu'administrateur et ajoutez :

```
127.0.0.1 cesi-site.local
127.0.0.1 cesi-static.local
```

#### 5.2. Configurer Apache

Ouvrez `C:\xampp\apache\conf\extra\httpd-vhosts.conf` et ajoutez :

```apache
<VirtualHost *:80>
    ServerName cesi-site.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName cesi-static.local
    DocumentRoot "C:/xampp/htdocs/cesi-stages/public"
    <Directory "C:/xampp/htdocs/cesi-stages/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### 5.3. Redémarrer Apache

Dans le panneau de contrôle XAMPP, arrêtez et redémarrez Apache.

## 6. Vérification de l'installation

1. Accédez à http://cesi-site.local (ou http://localhost/cesi-stages/public)
2. Vous devriez voir la page d'accueil de CesiStages

## 7. Comptes de test

Si vous avez importé les données de test, voici les comptes disponibles :

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@cesi.fr | password |
| Pilote | pilote@cesi.fr | password |
| Étudiant | etudiant@cesi.fr | password |
| Recruteur | recruteur@cesi.fr | recruteur123 |

## 7.1 Créer un nouveau compte

Les utilisateurs peuvent s'inscrire en choisissant leur rôle :

1. Aller sur `/register`
2. Remplir le formulaire (nom, prénom, email, mot de passe)
3. **Choisir son rôle** : Étudiant, Recruteur ou Pilote
4. Valider l'inscription

**Note pour les recruteurs** : Après inscription, ils sont automatiquement redirigés vers une page de configuration d'entreprise où ils peuvent :
- Rejoindre une entreprise existante
- Ou créer une nouvelle entreprise

## 8. Problèmes courants

### Erreur de connexion à la base de données

- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les identifiants dans `.env` ou `config/config.php`
- Vérifiez que la base de données `cesi_stages` existe

### Page blanche ou erreur 500

- Activez l'affichage des erreurs PHP dans `php.ini` :
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Vérifiez les logs Apache : `C:\xampp\apache\logs\error.log`

### Erreur "Class not found"

- Exécutez `composer install` pour installer les dépendances
- Vérifiez que l'autoloader est chargé

### Les styles ne s'affichent pas

- Vérifiez que le fichier `.htaccess` est présent dans `/public`
- Activez `mod_rewrite` dans Apache

## 9. Structure du projet

```
cesi-stages/
├── config/           # Configuration (routes, config.php)
├── core/             # Classes de base (Router, Controller, Model)
├── database/         # Migrations et seeds SQL
├── docs/             # Documentation
├── public/           # Point d'entrée (index.php, assets)
│   ├── css/
│   ├── js/
│   └── uploads/      # Fichiers uploadés (CV, etc.)
├── src/
│   ├── controllers/  # Contrôleurs
│   ├── models/       # Modèles
│   └── views/        # Vues PHP
├── vendor/           # Dépendances Composer
├── .env              # Variables d'environnement (à créer)
├── .env.example      # Exemple de configuration
└── composer.json     # Dépendances PHP
```

## 10. Aide supplémentaire

Pour toute question, contactez l'équipe de développement ou consultez les autres fichiers de documentation dans le dossier `docs/`.
