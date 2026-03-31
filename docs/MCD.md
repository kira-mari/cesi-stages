# 🗄️ Modèle Conceptuel de Données (MCD) - CesiStages

Ce document détaille l'architecture et la structure des données pour l'application **CesiStages**. Il sert de référence pour comprendre les entités, leurs attributs et les relations qui les unissent.

---

## 📊 Diagramme Conceptuel (Mermaid)

Ce diagramme illustre les entités et leurs relations (cardinalités). Les tables de liaison (issues des relations *Many-to-Many*) sont représentées comme des entités associatives pour plus de clarté.

```mermaid
erDiagram
    %% ==========================================
    %% ENTITÉS PRINCIPALES
    %% ==========================================
    UTILISATEUR {
        int id PK
        string nom
        string prenom
        string email UK
        string password
        int age
        string telephone
        text adresse
        text bio
        enum role "admin, pilote, etudiant, recruteur"
        boolean is_verified
        string verification_code
        string remember_token
        boolean is_approved
        datetime approval_requested_at
        datetime approved_at
        int approved_by
        datetime created_at
        datetime updated_at
    }

    ENTREPRISE {
        int id PK
        string nom
        text description
        string email
        string telephone
        text adresse
        string site_web
        string secteur
        datetime created_at
        datetime updated_at
    }

    OFFRE {
        int id PK
        int entreprise_id FK
        string titre
        text description
        json competences
        decimal remuneration
        int duree "en mois"
        date date_debut
        date date_fin
        datetime created_at
        datetime updated_at
    }

    GROUPE {
        int id PK
        int pilote_id FK
        string nom
        datetime created_at
        datetime updated_at
    }

    %% ==========================================
    %% ENTITÉS ASSOCIATIVES (Tables de liaison)
    %% ==========================================
    CANDIDATURE {
        int id PK
        int offre_id FK
        int etudiant_id FK
        text lettre_motivation
        string cv_path
        enum statut "en_attente, acceptee, refusee"
        datetime created_at
        datetime updated_at
    }

    EVALUATION {
        int id PK
        int entreprise_id FK
        int user_id FK
        int note "1 à 5"
        text commentaire
        datetime created_at
    }

    MESSAGE {
        int id PK
        int expediteur_id FK
        int destinataire_id FK
        string sujet
        text contenu
        boolean lu
        datetime lu_at
        datetime created_at
    }

    WISHLIST {
        int id PK
        int etudiant_id FK
        int offre_id FK
        datetime created_at
    }

    PILOTE_ETUDIANT {
        int id PK
        int pilote_id FK
        int etudiant_id FK
        datetime created_at
    }

    RECRUTEUR_ENTREPRISE {
        int id PK
        int recruteur_id FK
        int entreprise_id FK
        datetime created_at
    }

    GROUPE_ETUDIANT {
        int id PK
        int groupe_id FK
        int etudiant_id FK
        datetime created_at
    }

    %% ==========================================
    %% RELATIONS (Cardinalités)
    %% ==========================================
    ENTREPRISE ||--o{ OFFRE : "publie"
    OFFRE ||--o{ CANDIDATURE : "reçoit"
    UTILISATEUR ||--o{ CANDIDATURE : "soumet (étudiant)"
    ENTREPRISE ||--o{ EVALUATION : "est notée par"
    UTILISATEUR ||--o{ EVALUATION : "rédige"
    UTILISATEUR ||--o{ WISHLIST : "ajoute aux favoris (étudiant)"
    OFFRE ||--o{ WISHLIST : "est favorisée"
    UTILISATEUR ||--o{ MESSAGE : "envoie (expéditeur)"
    UTILISATEUR ||--o{ MESSAGE : "reçoit (destinataire)"
    UTILISATEUR ||--o{ PILOTE_ETUDIANT : "est assigné (pilote/étudiant)"
    UTILISATEUR ||--o{ RECRUTEUR_ENTREPRISE : "représente (recruteur)"
    ENTREPRISE ||--o{ RECRUTEUR_ENTREPRISE : "emploie"
    UTILISATEUR ||--o{ GROUPE : "crée et gère (pilote)"
    GROUPE ||--o{ GROUPE_ETUDIANT : "contient"
    UTILISATEUR ||--o{ GROUPE_ETUDIANT : "est membre (étudiant)"
```

---

## 📖 Dictionnaire des Données

### 1. Entités Principales

**UTILISATEUR (`users`)**
| Attribut | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Identifiant unique |
| `nom` / `prenom` | VARCHAR(100) | Not Null | Identité de l'utilisateur |
| `email` | VARCHAR(255) | Unique, Not Null | Adresse email (sert d'identifiant de connexion) |
| `password` | VARCHAR(255) | Not Null | Mot de passe hashé |
| `age` | INT | Nullable | Âge de l'utilisateur |
| `telephone` | VARCHAR(20) | Nullable | Numéro de téléphone |
| `adresse` | TEXT | Nullable | Adresse postale |
| `bio` | TEXT | Nullable | Courte biographie/présentation |
| `role` | ENUM | Défaut: 'etudiant' | Rôle (`admin`, `pilote`, `etudiant`, `recruteur`) |
| `is_verified` / `verification_code` | BOOL / VARCHAR | - | Gestion de la vérification de l'email |
| `is_approved` / `approved_at` | TINYINT / DATETIME| - | Validation manuelle du compte (par un admin/pilote) |

**ENTREPRISE (`entreprises`)**
| Attribut | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Identifiant unique |
| `nom` | VARCHAR(255) | Not Null | Raison sociale de l'entreprise |
| `description` | TEXT | Nullable | Description de l'entreprise |
| `email` / `telephone` | VARCHAR | Nullable | Coordonnées de contact génériques |
| `adresse` | TEXT | Nullable | Adresse du siège ou de l'agence |
| `site_web` | VARCHAR(255) | Nullable | URL du site internet |
| `secteur` | VARCHAR(100) | Nullable | Secteur d'activité principal |

**OFFRE (`offres`)**
| Attribut | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Identifiant unique |
| `entreprise_id` | INT | Foreign Key | Lien vers l'entreprise émettrice |
| `titre` | VARCHAR(255) | Not Null | Intitulé du stage |
| `description` | TEXT | Not Null | Détail des missions |
| `competences` | JSON | Nullable | Liste structurée des compétences requises |
| `remuneration` | DECIMAL(10,2) | Défaut: 0 | Gratification mensuelle |
| `duree` | INT | Nullable | Durée du stage en mois |
| `date_debut` / `date_fin` | DATE | Nullable | Période du stage |

**GROUPE (`groupes`)**
| Attribut | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Identifiant unique |
| `pilote_id` | INT | Foreign Key | Pilote responsable du groupe |
| `nom` | VARCHAR(255) | Not Null | Nom de la promotion ou du groupe |

---

### 2. Entités Associatives (Tables de liaison)

| Entité / Table | Description des champs clés et du rôle |
|---|---|
| **CANDIDATURE** (`candidatures`) | Lie un `Etudiant` à une `Offre`. Stocke la `lettre_motivation`, le `cv_path` et le `statut` (`en_attente`, `acceptee`, `refusee`). *Unicité : un étudiant ne peut postuler qu'une fois à une offre.* |
| **EVALUATION** (`evaluations`) | Lie un `Utilisateur` (étudiant/pilote) à une `Entreprise`. Stocke une `note` (1 à 5) et un `commentaire`. *Unicité : un utilisateur ne note une entreprise qu'une fois.* |
| **MESSAGE** (`messages`) | Gère la messagerie interne. Lie deux `Utilisateurs` (`expediteur_id`, `destinataire_id`). Contient `sujet`, `contenu`, et l'état de lecture (`lu`, `lu_at`). |
| **WISHLIST** (`wishlist`) | Système de favoris. Lie un `Etudiant` à une `Offre`. *Unicité : une offre ne peut être qu'une fois dans la liste d'un même étudiant.* |
| **PILOTE_ETUDIANT** (`pilote_etudiant`) | Lie un `Pilote` à un `Etudiant` pour définir qui encadre qui en dehors d'une logique de groupe. |
| **RECRUTEUR_ENTREPRISE** (`recruteur_entreprise`) | Lie un profil `Recruteur` à une ou plusieurs `Entreprises`. Permet de gérer les droits d'édition des offres. |
| **GROUPE_ETUDIANT** (`groupe_etudiant`) | Assigne un `Etudiant` à un `Groupe`. *Unicité : un étudiant ne peut être qu'une fois dans un groupe.* |

---

## 🔗 Règles de gestion et Cardinalités

### Gestion des Utilisateurs et Rôles
* **Encadrement** : Un `Pilote` peut encadrer plusieurs `Étudiants` (1,N). Un `Étudiant` peut être encadré par plusieurs `Pilotes` (1,N).
* **Groupes** : Un `Pilote` crée et gère un ou plusieurs `Groupes` (1,N). Un `Groupe` est composé de plusieurs `Étudiants` (1,N).
* **Recrutement** : Un `Recruteur` peut être rattaché à une ou plusieurs `Entreprises` (1,N). Une `Entreprise` peut avoir plusieurs `Recruteurs` (1,N).

### Offres et Candidatures
* **Publication** : Une `Entreprise` peut publier plusieurs `Offres` (1,N). Une `Offre` appartient obligatoirement à une seule `Entreprise` (1,1).
* **Candidature** : Un `Étudiant` peut soumettre plusieurs `Candidatures` (0,N). Une `Offre` peut recevoir plusieurs `Candidatures` (0,N). L'unicité garantit qu'un étudiant ne postule pas deux fois à la même offre.
* **Favoris (Wishlist)** : Un `Étudiant` peut mettre plusieurs `Offres` en favoris (0,N).

### Évaluations et Communication
* **Évaluation** : Un `Utilisateur` peut évaluer plusieurs `Entreprises` (0,N). Une `Entreprise` peut recevoir plusieurs `Évaluations` (0,N).
* **Messagerie** : Un `Utilisateur` peut envoyer (0,N) et recevoir (0,N) des messages vers/depuis d'autres `Utilisateurs`.


