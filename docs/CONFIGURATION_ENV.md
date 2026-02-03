# Configuration de l'environnement (.env)

Ce projet utilise un fichier `.env` pour gérer les configurations sensibles et spécifiques à chaque environnement (local, production, etc.). Ce fichier ne doit **jamais** être commité sur Git.

## 1. Initialisation du fichier

À la racine du projet, dupliquez le fichier d'exemple pour créer votre configuration locale :
1. Copiez le fichier `.env.example`.
2. Renommez la copie en `.env`.

## 2. Configuration de la Base de Données

Modifiez les valeurs suivantes dans le `.env` pour correspondre à votre serveur MySQL (XAMPP/WAMP/Laragon) :

```ini
DB_HOST=localhost
DB_NAME=cesi_stages
DB_USER=root        # Par défaut 'root' sur XAMPP
DB_PASS=            # Par défaut vide sur XAMPP
DB_CHARSET=utf8mb4
```

## 3. Configuration Google OAuth (SSO)

Pour permettre la connexion via Google ("Se connecter avec Google"), vous devez créer des identifiants sur la console Google Cloud.

### Étape A : Créer un projet Google Cloud
1. Rendez-vous sur la [Google Cloud Console](https://console.cloud.google.com/).
2. Créez un **Nouveau projet** (ex: `CesiStages-Dev`).

### Étape B : Configurer l'écran de consentement
1. Allez dans **APIs & Services > Écran de consentement OAuth**.
2. Choisissez **Externe** (pour le développement) et créés.
3. Remplissez les champs obligatoires :
   - **Nom de l'application** : `Cesi Stages`
   - **Emails** : Votre adresse email.
4. Cliquez sur "Enregistrer" jusqu'à la fin.
5. **Important** : Dans l'onglet **Utilisateurs tests**, ajoutez votre propre adresse email Gmail pour pouvoir vous connecter en phase de développement.

### Étape C : Créer les identifiants (Client ID & Secret)
1. Allez dans **APIs & Services > Identifiants**.
2. Cliquez sur **+ CRÉER DES IDENTIFIANTS** > **ID client OAuth**.
3. Type d'application : **Application Web**.
4. Nom : `Client Local`.
5. **URI de redirection autorisés** (Très important) :
   Ajoutez l'URL exacte où Google doit renvoyer l'utilisateur après connexion. Pour ce projet en local, c'est généralement :
   
   ```
   http://localhost/cesi-stages/auth/google-callback
   ```
   

6. Cliquez sur **Créer**.

### Étape D : Remplir le fichier .env
Une fenêtre s'ouvre avec vos clés. Copiez-les dans votre fichier `.env` :

```ini
GOOGLE_CLIENT_ID=votre_id_client_google...
GOOGLE_CLIENT_SECRET=votre_secret_client_google...
```

> **Note :** Si vous avez perdu le secret, retournez dans **Identifiants**, cliquez sur le nom de votre client OAuth (icône crayon), et le secret sera affiché sur la droite.
