# Rôles et Fonctionnalités - CesiStages

Ce document décrit les différents rôles utilisateur et leurs fonctionnalités.

## Vue d'ensemble des rôles

| Rôle | Description |
|------|-------------|
| **Admin** | Administrateur avec accès complet |
| **Pilote** | Encadrant qui gère un groupe d'étudiants |
| **Étudiant** | Cherche des stages et postule aux offres |
| **Recruteur** | Représentant d'entreprise qui gère les candidatures |

---

## 1. Administrateur (Admin)

### Accès
- Email : `admin@cesi.fr`
- Mot de passe : `password`

### Fonctionnalités

| Fonctionnalité | Description |
|----------------|-------------|
| **Gestion des utilisateurs** | Créer, modifier, supprimer tous les utilisateurs |
| **Gestion des pilotes** | Créer des pilotes et leur assigner des étudiants |
| **Gestion des recruteurs** | Créer des recruteurs et leur assigner des entreprises |
| **Approbation des comptes** | Valider ou refuser les inscriptions pilote/recruteur |
| **Gestion des entreprises** | CRUD complet sur les entreprises |
| **Gestion des offres** | CRUD complet sur les offres de stage |
| **Statistiques globales** | Voir toutes les statistiques de la plateforme |
| **Messagerie** | Contacter tous les utilisateurs |

### Navigation
- Dashboard
- Entreprises
- Offres
- Étudiants
- Pilotes
- Recruteurs
- **Approbations** (demandes en attente)
- Messagerie

---

## 2. Pilote

### Accès
- Email : `pilote@cesi.fr`
- Mot de passe : `password`

### Fonctionnalités

| Fonctionnalité | Description |
|----------------|-------------|
| **Mes étudiants** | Voir et gérer les étudiants qui lui sont assignés |
| **Suivi des candidatures** | Voir les candidatures de ses étudiants |
| **Gestion des entreprises** | Créer et évaluer des entreprises |
| **Gestion des offres** | Créer des offres de stage |
| **Statistiques** | Statistiques sur ses étudiants |
| **Messagerie** | Contacter ses étudiants |

### Navigation
- Dashboard
- Mes Étudiants
- Entreprises
- Offres
- Messagerie

---

## 3. Étudiant

### Accès
- Email : `etudiant@cesi.fr`
- Mot de passe : `password`

### Fonctionnalités

| Fonctionnalité | Description |
|----------------|-------------|
| **Recherche d'offres** | Rechercher et filtrer les offres de stage |
| **Wishlist** | Sauvegarder des offres pour plus tard |
| **Candidatures** | Postuler aux offres avec CV et lettre de motivation |
| **Suivi** | Voir le statut de ses candidatures |
| **Profil** | Modifier ses informations personnelles |
| **Messagerie** | Répondre aux messages reçus |

### Navigation
- Dashboard
- Offres
- Wishlist
- Mes Candidatures
- Messagerie

---

## 4. Recruteur

### Accès
- Email : `recruteur@cesi.fr`
- Mot de passe : `recruteur123`

### Inscription
Lors de l'inscription, un nouveau recruteur :
1. Crée son compte en sélectionnant le rôle "Recruteur"
2. Est automatiquement redirigé vers la page de configuration d'entreprise
3. Peut soit rejoindre une entreprise existante, soit en créer une nouvelle
4. Une fois l'entreprise configurée, peut publier des offres

### Fonctionnalités

| Fonctionnalité | Description |
|----------------|-------------|
| **Configurer entreprise** | Associer ou créer son entreprise après inscription |
| **Publier une offre** | Créer des offres de stage pour ses entreprises |
| **Mes entreprises** | Voir les entreprises qui lui sont assignées |
| **Candidatures reçues** | Voir toutes les candidatures pour ses entreprises |
| **Gestion des candidatures** | Accepter, refuser ou mettre en attente |
| **Voir les CV** | Consulter les CV et lettres de motivation |
| **Messagerie** | Contacter les étudiants qui ont candidaté |

### Navigation
- Dashboard
- Publier une offre
- Candidatures
- Mes Entreprises
- Messagerie

---

## Processus d'inscription

### Page d'inscription (`/register`)

L'utilisateur peut choisir son rôle lors de l'inscription :

| Rôle sélectionnable | Processus après inscription |
|---------------------|----------------------------|
| **Étudiant** | Redirigé vers la page de connexion (accès immédiat) |
| **Pilote** | Redirigé vers la page de connexion (en attente d'approbation) |
| **Recruteur** | Connecté automatiquement + redirigé vers la configuration d'entreprise (en attente d'approbation) |

**Note** : Le rôle "Admin" ne peut pas être choisi à l'inscription.

### Système d'approbation des comptes

Les comptes **Pilote** et **Recruteur** nécessitent une validation par un administrateur :

1. **Inscription** : L'utilisateur s'inscrit avec le rôle pilote ou recruteur
2. **Notification admin** : Les administrateurs reçoivent une notification de la nouvelle demande
3. **En attente** : L'utilisateur peut se connecter mais avec des accès limités (comme un étudiant)
4. **Validation** : L'administrateur approuve ou refuse la demande depuis la page "Approbations"
5. **Notification utilisateur** : L'utilisateur reçoit un message l'informant de la décision
6. **Accès complet** : Si approuvé, l'utilisateur a accès à toutes les fonctionnalités de son rôle

### Pourquoi ce système ?

- **Sécurité** : Évite que n'importe qui puisse se créer un compte pilote/recruteur
- **Contrôle** : L'admin vérifie l'identité des utilisateurs avec des rôles privilégiés
- **Flexibilité** : L'utilisateur en attente peut quand même naviguer sur la plateforme

### Configuration entreprise (Recruteurs uniquement)

Après inscription, le recruteur doit configurer son entreprise :

1. **Rejoindre une entreprise existante** :
   - Sélectionner une entreprise dans la liste déroulante
   - Cliquer sur "Rejoindre"

2. **Créer une nouvelle entreprise** :
   - Remplir le formulaire (nom obligatoire)
   - Informations optionnelles : secteur, adresse, email, téléphone, site web, description
   - Si le nom existe déjà, le recruteur est associé à l'entreprise existante

### Alerte Dashboard

Si un recruteur n'a pas encore d'entreprise associée, un bandeau d'alerte s'affiche sur son dashboard avec un lien vers la page de configuration.

---

## Système de messagerie

### Qui peut contacter qui ?

| Expéditeur | Peut contacter |
|------------|----------------|
| **Admin** | Tous les utilisateurs |
| **Pilote** | Ses étudiants assignés |
| **Recruteur** | Les étudiants qui ont candidaté à ses offres |
| **Étudiant** | Répondre aux messages reçus |

### Fonctionnalités de messagerie
- Boîte de réception
- Messages envoyés
- Composer un nouveau message
- Répondre à un message
- Badge de notification (messages non lus)
- Supprimer un message

---

## Chatbot

Le chatbot est disponible pour tous les utilisateurs (connectés ou non) et fournit des réponses adaptées au rôle :

### Non connecté
- Informations sur les offres
- Comment s'inscrire
- Comment postuler

### Étudiant
- Recherche d'offres
- Statut des candidatures
- Conseils pour postuler

### Pilote
- Statistiques sur ses étudiants
- Candidatures de ses étudiants

### Recruteur
- Candidatures reçues
- Comment publier une offre

### Admin
- Statistiques globales
- Gestion de la plateforme

---

## Base de données - Tables principales

| Table | Description |
|-------|-------------|
| `users` | Tous les utilisateurs (admin, pilote, étudiant, recruteur) |
| `entreprises` | Les entreprises partenaires |
| `offres` | Les offres de stage |
| `candidatures` | Les candidatures des étudiants |
| `wishlist` | Les offres sauvegardées par les étudiants |
| `pilote_etudiant` | Relation pilote-étudiant |
| `recruteur_entreprise` | Relation recruteur-entreprise |
| `messages` | Messages internes entre utilisateurs |
| `evaluations` | Évaluations des entreprises |
