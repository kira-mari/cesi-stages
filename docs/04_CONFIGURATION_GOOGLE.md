# 04 - Configuration Google SSO

Cette étape permet aux utilisateurs de se connecter via leur compte Google.

## 1. Console Google Cloud
1. Rendez-vous sur [Google Cloud Console](https://console.cloud.google.com/).
2. Créez un **Nouveau Projet**.
3. Allez dans **API et services > Écran d'accord OAuth**.
   - Type d'utilisateur : **Externe**.
   - Remplissez les infos obligatoires (Nom de l'app, emails support).
4. Allez dans **API et services > Identifiants**.
   - Cliquez sur **+ CRÉER DES IDENTIFIANTS** > **ID client OAuth**.
   - Type d'application : **Application Web**.
   - Nom : `CesiStages Local`.

## 2. URIs autorisés
Dans la configuration de l'ID Client, ajoutez :

- **Origines JavaScript autorisées** :
  ```
  http://localhost
  http://cesi-site.local
  ```

- **URI de redirection autorisés** :
  ```
  http://localhost/cesi-stages/public/auth/google/callback
  http://cesi-site.local/auth/google/callback
  ```
> *Adaptez selon si vous utilisez vhosts ou localhost direct.*

## 3. Configuration dans le projet
Copiez l'ID Client et le Code Secret affichés par Google dans votre fichier `.env` :

```ini
# Configuration Google OAuth
GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre-code-secret-google
GOOGLE_REDIRECT_URI=http://cesi-site.local/auth/google/callback
```

---
👉 **Étape suivante :** [Configuration des Virtual Hosts](05_CONFIGURATION_VHOSTS.md)
