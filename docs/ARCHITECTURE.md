# Architecture du projet - CesiStages

Ce document décrit l'architecture technique du projet.

## Pattern MVC

Le projet suit le pattern **Model-View-Controller** (MVC) :

```
┌─────────────────────────────────────────────────────────┐
│                      Requête HTTP                        │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    public/index.php                      │
│                    (Point d'entrée)                      │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                      core/Router.php                     │
│                  (Routage des URLs)                      │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                 src/controllers/*.php                    │
│              (Logique métier, traitement)                │
└──────────┬──────────────────────────────┬───────────────┘
           ▼                              ▼
┌──────────────────────┐    ┌─────────────────────────────┐
│   src/models/*.php   │    │      src/views/*.php         │
│   (Accès données)    │    │     (Affichage HTML)         │
└──────────────────────┘    └─────────────────────────────┘
```

---

## Structure des dossiers

```
cesi-stages/
│
├── config/                     # Configuration
│   ├── config.php              # Variables de configuration
│   └── routes.php              # Définition des routes
│
├── core/                       # Classes de base (framework)
│   ├── Controller.php          # Contrôleur de base
│   ├── Model.php               # Modèle de base (ORM simple)
│   └── Router.php              # Gestionnaire de routes
│
├── database/                   # Base de données
│   ├── migrations/             # Scripts de création de tables
│   └── seeds/                  # Données de test
│
├── docs/                       # Documentation
│
├── public/                     # Fichiers publics (accessible via web)
│   ├── index.php               # Point d'entrée unique
│   ├── .htaccess               # Réécriture d'URL Apache
│   ├── css/                    # Styles CSS
│   ├── js/                     # JavaScript
│   ├── assets/                 # Images, fonts, etc.
│   └── uploads/                # Fichiers uploadés (CV, etc.)
│
├── src/                        # Code source de l'application
│   ├── controllers/            # Contrôleurs
│   │   ├── Auth.php            # Authentification (login, register, logout)
│   │   ├── Dashboard.php       # Tableau de bord (stats par rôle)
│   │   ├── Entreprise.php      # CRUD entreprises
│   │   ├── Offre.php           # CRUD offres de stage
│   │   ├── Etudiant.php        # Gestion étudiants
│   │   ├── Pilote.php          # Gestion pilotes
│   │   ├── Recruteur.php       # Gestion recruteurs + config entreprise
│   │   ├── Message.php         # Messagerie interne
│   │   ├── Chatbot.php         # API Chatbot intelligent
│   │   ├── Candidature.php     # Gestion candidatures
│   │   └── ...
│   │
│   ├── models/                 # Modèles (accès BDD)
│   │   ├── User.php
│   │   ├── Entreprise.php
│   │   ├── Offre.php
│   │   ├── Candidature.php
│   │   ├── Message.php
│   │   └── ...
│   │
│   └── views/                  # Vues (templates PHP)
│       ├── layouts/            # Layouts (main.php)
│       ├── partials/           # Composants réutilisables
│       ├── auth/               # Pages d'authentification
│       ├── dashboard/          # Tableau de bord
│       ├── entreprises/        # Pages entreprises
│       ├── offres/             # Pages offres
│       ├── etudiants/          # Pages étudiants
│       ├── recruteurs/         # Pages recruteurs
│       ├── messages/           # Pages messagerie
│       └── ...
│
├── vendor/                     # Dépendances Composer
├── .env                        # Variables d'environnement
├── .env.example                # Exemple de configuration
├── .gitignore                  # Fichiers ignorés par Git
└── composer.json               # Dépendances PHP
```

---

## Classes de base

### Router (core/Router.php)

Gère le routage des URLs vers les contrôleurs.

```php
// Définir une route
$router->add('offres/{id:\d+}', [
    'controller' => 'Offre',
    'action' => 'show'
]);

// Les paramètres sont accessibles via $this->routeParams
```

### Controller (core/Controller.php)

Classe de base pour tous les contrôleurs.

```php
class MonController extends Controller
{
    public function index()
    {
        // Vérifier l'authentification
        $this->requireRole(['admin', 'pilote']);
        
        // Récupérer un paramètre de route
        $id = $this->routeParams['id'];
        
        // Rendre une vue
        $this->render('ma-vue', [
            'title' => 'Titre',
            'data' => $data
        ]);
        
        // Rediriger
        $this->redirect('autre-page');
        
        // Réponse JSON
        $this->json(['success' => true]);
    }
}
```

### Model (core/Model.php)

Classe de base pour l'accès à la base de données.

```php
class MonModel extends Model
{
    protected $table = 'ma_table';
    
    // Méthodes héritées :
    // $this->all()           - Tous les enregistrements
    // $this->find($id)       - Un enregistrement par ID
    // $this->where($col, $val) - Filtrer
    // $this->create($data)   - Créer
    // $this->update($id, $data) - Modifier
    // $this->delete($id)     - Supprimer
    // $this->count()         - Compter
    
    // Requêtes personnalisées :
    public function maMethode()
    {
        $stmt = self::getDB()->prepare("SELECT * FROM ...");
        $stmt->execute([':param' => $value]);
        return $stmt->fetchAll();
    }
}
```

---

## Système de routes

Les routes sont définies dans `config/routes.php` :

```php
// Route simple
$router->add('offres', [
    'controller' => 'Offre',
    'action' => 'index'
]);

// Route avec paramètre
$router->add('offres/show/{id:\d+}', [
    'controller' => 'Offre',
    'action' => 'show'
]);

// {id:\d+} = paramètre "id" qui doit être un nombre
```

---

## Authentification

### Session

Les informations de l'utilisateur connecté sont stockées en session :

```php
$_SESSION['user_id']      // ID de l'utilisateur
$_SESSION['user_email']   // Email
$_SESSION['user_role']    // Rôle (admin, pilote, etudiant, recruteur)
$_SESSION['user_nom']     // Nom
$_SESSION['user_prenom']  // Prénom
```

### Vérification des permissions

```php
// Dans un contrôleur
$this->requireRole(['admin', 'pilote']); // Redirige si non autorisé

// Vérifier sans redirection
if ($this->hasRole('admin')) {
    // ...
}

// Vérifier si connecté
if ($this->isAuthenticated()) {
    // ...
}
```

---

## Vues

### Layout principal

Le layout `src/views/layouts/main.php` encapsule toutes les pages :

```php
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    <?php require 'partials/header.php'; ?>
    <main><?= $content ?></main>
    <?php require 'partials/footer.php'; ?>
</body>
</html>
```

### Rendre une vue

```php
$this->render('dossier/fichier', [
    'title' => 'Titre de la page',
    'variable' => $valeur
]);
```

Les variables passées sont accessibles directement dans la vue.

---

## Base de données

### Schéma principal

```sql
-- Utilisateurs (tous les rôles)
users (id, nom, prenom, email, password, role, ...)

-- Entreprises
entreprises (id, nom, adresse, email, ...)

-- Offres de stage
offres (id, entreprise_id, titre, description, competences, ...)

-- Candidatures
candidatures (id, offre_id, etudiant_id, lettre_motivation, cv_path, statut, ...)

-- Relations
pilote_etudiant (pilote_id, etudiant_id)
recruteur_entreprise (recruteur_id, entreprise_id)

-- Messagerie
messages (id, expediteur_id, destinataire_id, sujet, contenu, lu, ...)
```

---

## Bonnes pratiques

1. **Toujours échapper les données** : `htmlspecialchars($data)`
2. **Utiliser les requêtes préparées** : Jamais de SQL brut avec des variables
3. **Valider les entrées** : Vérifier les données POST avant utilisation
4. **CSRF Token** : Inclure dans tous les formulaires POST
5. **Vérifier les permissions** : Utiliser `requireRole()` au début des actions

---

## Parcours utilisateur - Inscription Recruteur

Le processus d'inscription d'un recruteur est particulier :

```
┌─────────────────────────────────────────────────────────┐
│                    /register                             │
│           (Choix du rôle : Recruteur)                   │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│            Auth::register()                              │
│     Création du compte avec role='recruteur'            │
│     Connexion automatique + redirection                 │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│        /recruteur/configurer-entreprise                  │
│                                                          │
│  Option A: Rejoindre une entreprise existante           │
│      → Sélection dans la liste                          │
│      → Création relation recruteur_entreprise           │
│                                                          │
│  Option B: Créer une nouvelle entreprise                │
│      → Formulaire (nom, adresse, secteur, etc.)         │
│      → Si entreprise existe déjà → association          │
│      → Sinon création + association                     │
└─────────────────────────┬───────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    /dashboard                            │
│     Le recruteur peut maintenant publier des offres     │
└─────────────────────────────────────────────────────────┘
```

---

## Ajouter une nouvelle fonctionnalité

### 1. Créer la migration SQL (si nécessaire)

```sql
-- database/migrations/XXX_ma_migration.sql
CREATE TABLE ma_table (...);
```

### 2. Créer le modèle

```php
// src/models/MonModele.php
namespace Models;
use Core\Model;

class MonModele extends Model
{
    protected $table = 'ma_table';
}
```

### 3. Créer le contrôleur

```php
// src/controllers/MonControleur.php
namespace Controllers;
use Core\Controller;
use Models\MonModele;

class MonControleur extends Controller
{
    public function index()
    {
        $model = new MonModele();
        $data = $model->all();
        $this->render('mon-dossier/index', ['data' => $data]);
    }
}
```

### 4. Créer la vue

```php
<!-- src/views/mon-dossier/index.php -->
<div class="container">
    <?php foreach ($data as $item): ?>
        <p><?= htmlspecialchars($item['nom']) ?></p>
    <?php endforeach; ?>
</div>
```

### 5. Ajouter la route

```php
// config/routes.php
$router->add('ma-route', ['controller' => 'MonControleur', 'action' => 'index']);
```
