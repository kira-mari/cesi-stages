# 01 - Prérequis & Installation de base

## 1. Prérequis Système
Avant de commencer, assurez-vous d'avoir installé :
- **XAMPP** (ou WAMP/MAMP) avec PHP 8.0+.
- **Composer** (Gestionnaire de dépendances PHP).
- **Git** (Pour cloner le projet).

## 2. Récupération du projet
Ouvrez un terminal dans votre dossier `htdocs` (ex: `C:\xampp\htdocs`) :

```bash
git clone https://github.com/web4all/cesi-stages.git
cd cesi-stages
```

## 3. Installation des dépendances
Installez les bibliothèques PHP nécessaires via Composer :

```bash
composer install
```

## 4. Base de Données
1. Lancez **XAMPP** et démarrez **MySQL**.
2. Accédez à **phpMyAdmin** (http://localhost/phpmyadmin).
3. Créez une nouvelle base de données nommée `cesi_stages` (encodage `utf8mb4_unicode_ci`).
4. **Importez la structure** :
   - Onglet "Importer".
   - Choisissez le fichier : `database/migrations/create_tables_full.sql`.
5. **Importez les données de test** (optionnel) :
   - Choisissez le fichier : `database/seeds/insert_data_full.sql`.
6. **Exécutez les migrations supplémentaires** (dans le terminal) :
   ```bash
   php run_messages_migration.php
   php run_approval_migration.php
   ```
   Ou importez manuellement depuis phpMyAdmin :
   - `database/migrations/007_add_messages_table.sql`
   - `database/migrations/008_add_approval_system.sql`

---
👉 **Étape suivante :** [Configuration des variables d'environnement](02_CONFIGURATION_ENV.md)
