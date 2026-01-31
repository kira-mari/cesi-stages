# CesiStages - Plateforme de Recherche de Stages

## 📋 Description

CesiStages est une application web de gestion de recherche de stages développée pour les étudiants CESI. Elle permet de :

- Consulter et rechercher des offres de stage
- Gérer les entreprises partenaires
- Postuler à des offres avec CV et lettre de motivation
- Gérer une wishlist d'offres favorites
- Suivre les candidatures (étudiants et pilotes)
- Consulter des statistiques sur les offres

## 🚀 Installation

### Prérequis

- PHP >= 7.4
- MySQL >= 5.7 ou MariaDB >= 10.2
- Apache avec mod_rewrite activé
- Composer

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone https://github.com/web4all/cesi-stages.git
   cd cesi-stages
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   - Créer une base de données MySQL nommée `cesi_stages`
   - Importer le fichier `database/migrations/001_create_tables.sql`
   - Importer les données de test `database/seeds/001_insert_data.sql`

4. **Configurer l'application**
   - Modifier le fichier `config/config.php` avec vos paramètres de base de données
   - Modifier la constante `BASE_URL` selon votre configuration

5. **Configurer Apache**
   - Activer le module rewrite : `a2enmod rewrite`
   - Configurer un VirtualHost pointant vers le dossier `public/`
   - Ou utiliser le fichier `.htaccess` fourni

6. **Permissions**
   ```bash
   chmod 755 -R uploads/
   chmod 755 -R logs/
   ```

## 🏗️ Architecture

Le projet suit une architecture **MVC (Model-View-Controller)** :

```
cesi-stages/
├── config/                 # Configuration
│   ├── config.php         # Configuration générale
│   └── routes.php         # Définition des routes
├── core/                   # Classes de base
│   ├── Router.php         # Routeur
│   ├── Controller.php     # Contrôleur de base
│   └── Model.php          # Modèle de base
├── src/
│   ├── controllers/       # Contrôleurs
│   ├── models/            # Modèles
│   └── views/             # Vues
│       ├── layouts/       # Layouts
│       ├── partials/      # Partials
│       └── ...            # Pages
├── public/                # Point d'entrée
│   ├── index.php
│   └── .htaccess
├── assets/                # Ressources statiques
│   ├── css/
│   └── js/
├── database/              # Scripts SQL
│   ├── migrations/
│   └── seeds/
├── tests/                 # Tests unitaires
└── docs/                  # Documentation
```

## 👥 Rôles et Permissions

| Fonctionnalité | Administrateur | Pilote | Étudiant | Anonyme |
|----------------|----------------|--------|----------|---------|
| Authentification | ✅ | ✅ | ✅ | ✅ |
| Voir offres | ✅ | ✅ | ✅ | ✅ |
| Voir entreprises | ✅ | ✅ | ✅ | ✅ |
| Créer entreprise | ✅ | ✅ | ❌ | ❌ |
| Modifier entreprise | ✅ | ✅ | ❌ | ❌ |
| Évaluer entreprise | ✅ | ✅ | ❌ | ❌ |
| Créer offre | ✅ | ✅ | ❌ | ❌ |
| Modifier offre | ✅ | ✅ | ❌ | ❌ |
| Gérer pilotes | ✅ | ❌ | ❌ | ❌ |
| Gérer étudiants | ✅ | ✅ | ❌ | ❌ |
| Postuler | ❌ | ❌ | ✅ | ❌ |
| Wishlist | ❌ | ❌ | ✅ | ❌ |
| Voir statistiques | ✅ | ✅ | ✅ | ❌ |

## 🔐 Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@cesi.fr | admin123 |
| Pilote | pilote@cesi.fr | pilote123 |
| Étudiant | etudiant@cesi.fr | etudiant123 |

## 🧪 Tests

Exécuter les tests unitaires avec PHPUnit :

```bash
./vendor/bin/phpunit
```

## 🛡️ Sécurité

- Protection contre les injections SQL (requêtes préparées)
- Protection XSS (échappement des sorties)
- Protection CSRF (tokens)
- Mots de passe hashés (bcrypt)
- Sessions sécurisées (regenerate_id)
- Cookies sécurisés (httponly, secure, samesite)

## 📝 Spécifications techniques

- **Architecture** : MVC
- **Langages** : PHP 7.4+, HTML5, CSS3, JavaScript
- **Base de données** : MySQL/MariaDB
- **Serveur** : Apache
- **Tests** : PHPUnit
- **Responsive** : Mobile-first

## 📊 Fonctionnalités

### Gestion des entreprises
- CRUD complet
- Évaluation par les utilisateurs
- Recherche et filtrage

### Gestion des offres
- CRUD complet
- Filtrage par compétences
- Pagination
- Statistiques

### Gestion des candidatures
- Postulation avec CV et LM
- Suivi des candidatures
- Consultation par les pilotes

### Wishlist
- Ajout/retrait d'offres
- Consultation personnelle

### Tableau de bord
- Statistiques selon le rôle
- Actions rapides
- Dernières offres

## 📄 Licence

Ce projet est développé dans le cadre du cursus CESI. Tous droits réservés.

## 👨‍💻 Auteurs

- **Web4All** - Développement
- **CESI** - Cahier des charges
