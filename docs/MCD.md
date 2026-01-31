# Modèle Conceptuel de Données (MCD)

## Entités et Attributs

### 1. USER (Utilisateur)
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| nom | VARCHAR(100) | Nom de l'utilisateur |
| prenom | VARCHAR(100) | Prénom de l'utilisateur |
| email | VARCHAR(255) | Adresse email (unique) |
| password | VARCHAR(255) | Mot de passe hashé |
| role | ENUM | Rôle : admin, pilote, etudiant |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Date de mise à jour |

### 2. ENTREPRISE
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| nom | VARCHAR(255) | Nom de l'entreprise |
| description | TEXT | Description de l'entreprise |
| email | VARCHAR(255) | Email de contact |
| telephone | VARCHAR(20) | Téléphone de contact |
| adresse | TEXT | Adresse postale |
| secteur | VARCHAR(100) | Secteur d'activité |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Date de mise à jour |

### 3. OFFRE (Offre de stage)
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| entreprise_id | INT (FK) | Référence vers l'entreprise |
| titre | VARCHAR(255) | Titre de l'offre |
| description | TEXT | Description du stage |
| competences | JSON | Liste des compétences requises |
| remuneration | DECIMAL | Rémunération mensuelle |
| duree | INT | Durée en mois |
| date_debut | DATE | Date de début |
| date_fin | DATE | Date de fin |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Date de mise à jour |

### 4. EVALUATION
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| entreprise_id | INT (FK) | Référence vers l'entreprise |
| user_id | INT (FK) | Référence vers l'utilisateur |
| note | INT | Note de 1 à 5 |
| commentaire | TEXT | Commentaire libre |
| created_at | DATETIME | Date de création |

### 5. CANDIDATURE
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| offre_id | INT (FK) | Référence vers l'offre |
| etudiant_id | INT (FK) | Référence vers l'étudiant |
| lettre_motivation | TEXT | Lettre de motivation |
| cv_path | VARCHAR(255) | Chemin vers le CV |
| statut | ENUM | Statut : en_attente, acceptee, refusee |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Date de mise à jour |

### 6. WISHLIST
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| etudiant_id | INT (FK) | Référence vers l'étudiant |
| offre_id | INT (FK) | Référence vers l'offre |
| created_at | DATETIME | Date d'ajout |

### 7. PILOTE_ETUDIANT (Relation)
| Attribut | Type | Description |
|----------|------|-------------|
| id | INT (PK) | Identifiant unique |
| pilote_id | INT (FK) | Référence vers le pilote |
| etudiant_id | INT (FK) | Référence vers l'étudiant |
| created_at | DATETIME | Date de création |

## Relations

```
USER (1) ----< (N) CANDIDATURE >---- (1) OFFRE
                    
USER (1) ----< (N) WISHLIST >---- (1) OFFRE

USER (1) ----< (N) EVALUATION >---- (1) ENTREPRISE

ENTREPRISE (1) ----< (N) OFFRE

USER (1) ----< (N) PILOTE_ETUDIANT >---- (N) USER
```

## Cardinalités

- **USER - CANDIDATURE** : Un utilisateur peut avoir plusieurs candidatures (1,N)
- **OFFRE - CANDIDATURE** : Une offre peut avoir plusieurs candidatures (1,N)
- **USER - WISHLIST** : Un étudiant peut avoir plusieurs offres en wishlist (1,N)
- **OFFRE - WISHLIST** : Une offre peut être dans plusieurs wishlists (1,N)
- **ENTREPRISE - OFFRE** : Une entreprise peut avoir plusieurs offres (1,N)
- **ENTREPRISE - EVALUATION** : Une entreprise peut avoir plusieurs évaluations (1,N)
- **USER - EVALUATION** : Un utilisateur peut évaluer plusieurs entreprises (1,N)
- **USER - PILOTE_ETUDIANT** : Un pilote peut gérer plusieurs étudiants (1,N)

## Schéma visuel du MCD

```
┌─────────────────┐         ┌─────────────────┐
│     USER        │         │   ENTREPRISE    │
├─────────────────┤         ├─────────────────┤
│ PK id           │         │ PK id           │
│ nom             │         │ nom             │
│ prenom          │         │ description     │
│ email           │         │ email           │
│ password        │         │ telephone       │
│ role            │         │ adresse         │
│ created_at      │         │ secteur         │
│ updated_at      │         │ created_at      │
└─────────────────┘         │ updated_at      │
         │                  └─────────────────┘
         │                           │
         │ 1                         │ 1
         │                           │
         N│                           │N
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│   CANDIDATURE   │         │     OFFRE       │
├─────────────────┤         ├─────────────────┤
│ PK id           │         │ PK id           │
│ FK offre_id     │◄────────│ FK entreprise_id│
│ FK etudiant_id  │         │ titre           │
│ lettre_motiv    │         │ description     │
│ cv_path         │         │ competences     │
│ statut          │         │ remuneration    │
│ created_at      │         │ duree           │
│ updated_at      │         │ date_debut      │
└─────────────────┘         │ date_fin        │
                            │ created_at      │
                            │ updated_at      │
                            └─────────────────┘
                                     ▲
                                     │N
                            ┌────────┴────────┐
                            │    WISHLIST     │
                            ├─────────────────┤
                            │ PK id           │
                            │ FK etudiant_id  │
                            │ FK offre_id     │
                            │ created_at      │
                            └─────────────────┘

┌─────────────────┐         ┌─────────────────┐
│   EVALUATION    │         │ PILOTE_ETUDIANT │
├─────────────────┤         ├─────────────────┤
│ PK id           │         │ PK id           │
│ FK entreprise_id│         │ FK pilote_id    │
│ FK user_id      │         │ FK etudiant_id  │
│ note            │         │ created_at      │
│ commentaire     │         └─────────────────┘
│ created_at      │
└─────────────────┘
```
