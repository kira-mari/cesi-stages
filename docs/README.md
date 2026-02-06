# Documentation CesiStages

Bienvenue dans la documentation du projet CesiStages, une plateforme de gestion de stages pour le CESI.

## Guides disponibles

| Document | Description |
|----------|-------------|
| [INSTALLATION.md](INSTALLATION.md) | Guide d'installation et configuration |
| [ROLES_ET_FONCTIONNALITES.md](ROLES_ET_FONCTIONNALITES.md) | Description des rôles et fonctionnalités |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Architecture technique du projet |

## Fonctionnalités principales

- **4 rôles** : Admin, Pilote, Étudiant, Recruteur
- **Gestion des offres** : Création, recherche, filtres par compétences/localisation
- **Candidatures** : Postuler avec CV et lettre de motivation
- **Wishlist** : Sauvegarder des offres favorites
- **Messagerie interne** : Communication entre les utilisateurs
- **Chatbot** : Assistant virtuel adapté à chaque rôle
- **Inscription dynamique** : Choix du rôle à l'inscription
- **Configuration entreprise** : Les recruteurs peuvent créer/rejoindre une entreprise

## Démarrage rapide

### 1. Prérequis
- XAMPP (PHP 8+, MySQL, Apache)
- Composer
- Git

### 2. Installation
```bash
git clone https://github.com/votre-repo/cesi-stages.git
cd cesi-stages
composer install
```

### 3. Base de données
1. Créer la base `cesi_stages` dans phpMyAdmin
2. Importer `database/migrations/create_tables_full.sql`
3. Importer `database/seeds/insert_data_full.sql` (données de test)
4. Exécuter les migrations supplémentaires si nécessaire :
   ```bash
   php run_recruteur_migration.php
   php run_recruteur_entreprise_migration.php
   php run_messages_migration.php
   ```

### 4. Configuration
```bash
copy .env.example .env
# Modifier .env avec vos paramètres
```

### 5. Accès
- URL : http://localhost/cesi-stages/public
- Admin : admin@cesi.fr / password

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@cesi.fr | password |
| Pilote | pilote@cesi.fr | password |
| Étudiant | etudiant@cesi.fr | password |
| Recruteur | recruteur@cesi.fr | recruteur123 |

## Parcours utilisateur

### Inscription d'un nouveau recruteur
1. Aller sur `/register`
2. Remplir les informations personnelles
3. Sélectionner le rôle "Recruteur"
4. Après inscription, configuration de l'entreprise :
   - Rejoindre une entreprise existante
   - Ou créer une nouvelle entreprise
5. Publier des offres de stage

### Inscription d'un étudiant
1. Aller sur `/register`
2. Sélectionner le rôle "Étudiant"
3. Rechercher des offres
4. Postuler avec CV et lettre de motivation

## Technologies utilisées

- **Backend** : PHP 8, PDO, MVC custom
- **Frontend** : HTML5, CSS3, Bootstrap 5, JavaScript
- **Base de données** : MySQL
- **Outils** : Composer, Git

## Structure du projet

```
cesi-stages/
├── config/           # Configuration (routes, config.php)
├── core/             # Classes de base (Router, Controller, Model)
├── database/         # Migrations et seeds SQL
├── docs/             # Documentation
├── public/           # Point d'entrée (index.php, assets)
├── src/
│   ├── controllers/  # Contrôleurs
│   ├── models/       # Modèles
│   └── views/        # Vues PHP
└── vendor/           # Dépendances Composer
```

## Support

Pour toute question, contactez l'équipe de développement.
