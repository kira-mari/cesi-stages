<?php
/**
 * Définition des routes de l'application
 */

use Core\Router;

// Route par défaut
$router->add('', ['controller' => 'Home', 'action' => 'index']);

// Routes d'authentification
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);
$router->add('register', ['controller' => 'Auth', 'action' => 'register']);

// Routes du tableau de bord
$router->add('dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
$router->add('dashboard/stats', ['controller' => 'Dashboard', 'action' => 'statistics']);

// Routes des entreprises
$router->add('entreprises', ['controller' => 'Entreprise', 'action' => 'index']);
$router->add('entreprises/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'show']);
$router->add('entreprises/create', ['controller' => 'Entreprise', 'action' => 'create']);
$router->add('entreprises/store', ['controller' => 'Entreprise', 'action' => 'store']);
$router->add('entreprises/show/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'show']);
$router->add('entreprises/edit/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'edit']);
$router->add('entreprises/update/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'update']);
$router->add('entreprises/delete/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'delete']);
$router->add('entreprises/evaluate/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'evaluate']);

// Routes des offres de stage
$router->add('offres', ['controller' => 'Offre', 'action' => 'index']);
$router->add('offres/{id:\d+}', ['controller' => 'Offre', 'action' => 'show']);
$router->add('offres/create', ['controller' => 'Offre', 'action' => 'create']);
$router->add('offres/store', ['controller' => 'Offre', 'action' => 'store']);
$router->add('offres/show/{id:\d+}', ['controller' => 'Offre', 'action' => 'show']);
$router->add('offres/edit/{id:\d+}', ['controller' => 'Offre', 'action' => 'edit']);
$router->add('offres/update/{id:\d+}', ['controller' => 'Offre', 'action' => 'update']);
$router->add('offres/delete/{id:\d+}', ['controller' => 'Offre', 'action' => 'delete']);
$router->add('offres/postuler/{id:\d+}', ['controller' => 'Offre', 'action' => 'postuler']);
$router->add('offres/addToWishlist/{id:\d+}', ['controller' => 'Offre', 'action' => 'addToWishlist']);
$router->add('offres/removeFromWishlist/{id:\d+}', ['controller' => 'Offre', 'action' => 'removeFromWishlist']);

// Routes des étudiants
$router->add('etudiants', ['controller' => 'Etudiant', 'action' => 'index']);
$router->add('etudiants/{id:\d+}', ['controller' => 'Etudiant', 'action' => 'show']);
$router->add('etudiants/create', ['controller' => 'Etudiant', 'action' => 'create']);
$router->add('etudiants/store', ['controller' => 'Etudiant', 'action' => 'store']);
$router->add('etudiants/show/{id:\d+}', ['controller' => 'Etudiant', 'action' => 'show']);
$router->add('etudiants/edit/{id:\d+}', ['controller' => 'Etudiant', 'action' => 'edit']);
$router->add('etudiants/update/{id:\d+}', ['controller' => 'Etudiant', 'action' => 'update']);
$router->add('etudiants/delete/{id:\d+}', ['controller' => 'Etudiant', 'action' => 'delete']);

// Routes des pilotes
$router->add('pilotes', ['controller' => 'Pilote', 'action' => 'index']);
$router->add('pilotes/create', ['controller' => 'Pilote', 'action' => 'create']);
$router->add('pilotes/store', ['controller' => 'Pilote', 'action' => 'store']);
$router->add('pilotes/show/{id:\d+}', ['controller' => 'Pilote', 'action' => 'show']);
$router->add('pilotes/edit/{id:\d+}', ['controller' => 'Pilote', 'action' => 'edit']);
$router->add('pilotes/update/{id:\d+}', ['controller' => 'Pilote', 'action' => 'update']);
$router->add('pilotes/delete/{id:\d+}', ['controller' => 'Pilote', 'action' => 'delete']);

// Routes des candidatures
$router->add('candidatures', ['controller' => 'Candidature', 'action' => 'index']);
$router->add('candidatures/etudiant', ['controller' => 'Candidature', 'action' => 'mesCandidatures']);
$router->add('candidatures/pilote', ['controller' => 'Candidature', 'action' => 'candidaturesPilote']);

// Routes de la wishlist
$router->add('wishlist', ['controller' => 'Wishlist', 'action' => 'index']);

// Route des mentions légales
$router->add('mentions-legales', ['controller' => 'Page', 'action' => 'mentionsLegales']);

// Route du chatbot (API JSON)
$router->add('chatbot/ask', ['controller' => 'Chatbot', 'action' => 'ask']);

// Route 404
$router->add('404', ['controller' => 'Error', 'action' => 'notFound']);
