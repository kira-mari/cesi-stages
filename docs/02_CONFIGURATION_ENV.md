# 02 - Configuration des variables (.env)

Le projet utilise un fichier `.env` pour stocker les informations sensibles. Ce fichier ne doit **jamais** être partagé publiquement.

## 1. Création du fichier
À la racine du projet, dupliquez le fichier `.env.example` et renommez-le `.env` :

```bash
cp .env.example .env
# Ou manuellement via l'explorateur de fichiers
```

## 2. Configuration de la Base de Données
Modifiez les lignes suivantes dans `.env` pour correspondre à votre installation MySQL locale :

```ini
DB_HOST=localhost
DB_NAME=cesi_stages
DB_USER=root
DB_PASS=          # Laissez vide si vous n'avez pas de mot de passe root sur XAMPP
DB_CHARSET=utf8mb4
```

---
👉 **Étape suivante :** [Configuration Brevo (Emails)](03_CONFIGURATION_BREVO.md)
