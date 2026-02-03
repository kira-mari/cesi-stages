# 03 - Configuration Brevo (SMTP)

L'application utilise **Brevo** (anciennement Sendinblue) pour l'envoi d'emails transactionnels (oubli de mot de passe, vérification de compte).

## 1. Création de compte
1. Créez un compte gratuit sur [Brevo.com](https://www.brevo.com).
2. Validez votre adresse email expéditeur.

## 2. Récupération des accès SMTP
1. Allez dans **Profil > SMTP & API**.
2. Cliquez sur l'onglet **SMTP**.
3. Générez une nouvelle clé SMTP si nécessaire.

## 3. Configuration dans le projet
Ouvrez votre fichier `.env` et remplissez la section MAIL avec les informations fournies par Brevo :

```ini
# Configuration des emails (Brevo SMTP)
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=votre-email-login@exemple.com
SMTP_PASS=votre-cle-smtp-master
SMTP_FROM_EMAIL=no-reply@cesi-stages.fr
SMTP_FROM_NAME=CesiStages
```

> ⚠️ **Note :** `SMTP_PASS` est la clé SMTP secrète, **pas** votre mot de passe de connexion Brevo.

---
👉 **Étape suivante :** [Configuration Google SSO](04_CONFIGURATION_GOOGLE.md)
