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
$router->add('profile', ['controller' => 'Auth', 'action' => 'profile']);
$router->add('profile/edit', ['controller' => 'Auth', 'action' => 'editProfile']);
$router->add('profile/update', ['controller' => 'Auth', 'action' => 'updateProfile']);
$router->add('profile/delete', ['controller' => 'Auth', 'action' => 'deleteAccount']);
// Verification
$router->add('verify', ['controller' => 'Auth', 'action' => 'verify']);
$router->add('verify/submit', ['controller' => 'Auth', 'action' => 'verifyCode']);
$router->add('verify/resend', ['controller' => 'Auth', 'action' => 'resendCode']);

// Password Reset
$router->add('forgot-password', ['controller' => 'Auth', 'action' => 'forgotPassword']);
$router->add('forgot-password/verify', ['controller' => 'Auth', 'action' => 'verifyResetCodePage']);
$router->add('forgot-password/verify-code', ['controller' => 'Auth', 'action' => 'verifyResetCode']);
$router->add('forgot-password/reset', ['controller' => 'Auth', 'action' => 'resetPasswordPage']);
$router->add('forgot-password/update', ['controller' => 'Auth', 'action' => 'resetPassword']);

// Google SSO
$router->add('auth/google', ['controller' => 'Auth', 'action' => 'googleRedirect']);
$router->add('auth/google-callback', ['controller' => 'Auth', 'action' => 'googleCallback']);

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
$router->add('entreprises/delete-evaluation/{id:\d+}', ['controller' => 'Entreprise', 'action' => 'deleteEvaluation']);

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

// Routes des recruteurs (gestion admin)
$router->add('recruteurs', ['controller' => 'Recruteur', 'action' => 'index']);
$router->add('recruteurs/show/{id:\d+}', ['controller' => 'Recruteur', 'action' => 'show']);
$router->add('recruteurs/delete/{id:\d+}', ['controller' => 'Recruteur', 'action' => 'delete']);
$router->add('recruteurs/assign-entreprise/{id:\d+}', ['controller' => 'Recruteur', 'action' => 'assignEntreprise']);
$router->add('recruteurs/remove-entreprise/{id:\d+}/{eid:\d+}', ['controller' => 'Recruteur', 'action' => 'removeEntreprise']);
$router->add('recruteurs/approve-request', ['controller' => 'Recruteur', 'action' => 'approveRequest']);
$router->add('recruteurs/reject-request', ['controller' => 'Recruteur', 'action' => 'rejectRequest']);

// Routes recruteur (espace recruteur)
$router->add('recruteur/configurer-entreprise', ['controller' => 'Recruteur', 'action' => 'configurerEntreprise']);
$router->add('recruteur/candidatures', ['controller' => 'Recruteur', 'action' => 'candidatures']);
$router->add('recruteur/candidature/{id:\d+}', ['controller' => 'Recruteur', 'action' => 'showCandidature']);
$router->add('recruteur/candidature/update/{id:\d+}', ['controller' => 'Recruteur', 'action' => 'updateCandidature']);

// Routes des candidatures
$router->add('candidatures', ['controller' => 'Candidature', 'action' => 'index']);
$router->add('candidatures/etudiant', ['controller' => 'Candidature', 'action' => 'mesCandidatures']);
$router->add('candidatures/pilote', ['controller' => 'Candidature', 'action' => 'candidaturesPilote']);

// Routes de la wishlist
$router->add('wishlist', ['controller' => 'Wishlist', 'action' => 'index']);

// Route des mentions légales
$router->add('mentions-legales', ['controller' => 'Page', 'action' => 'mentionsLegales']);

// Route de contact
$router->add('contact', ['controller' => 'Page', 'action' => 'contact']);

// Routes de la messagerie
$router->add('messages', ['controller' => 'Message', 'action' => 'index']);
$router->add('messages/envoyes', ['controller' => 'Message', 'action' => 'envoyes']);
$router->add('messages/nouveau', ['controller' => 'Message', 'action' => 'nouveau']);
$router->add('messages/envoyer', ['controller' => 'Message', 'action' => 'envoyer']);
$router->add('messages/show/{id:\d+}', ['controller' => 'Message', 'action' => 'show']);
$router->add('messages/supprimer/{id:\d+}', ['controller' => 'Message', 'action' => 'supprimer']);
$router->add('messages/count', ['controller' => 'Message', 'action' => 'countNonLus']);

// Routes des notifications
$router->add('notifications/count', ['controller' => 'Notification', 'action' => 'count']);
$router->add('notifications/list', ['controller' => 'Notification', 'action' => 'list']);
$router->add('notifications/mark-all-read', ['controller' => 'Notification', 'action' => 'markAllRead']);

// Routes des approbations (admin)
$router->add('approbations', ['controller' => 'Approbation', 'action' => 'index']);
$router->add('approbations/approve/{id:\d+}', ['controller' => 'Approbation', 'action' => 'approve']);
$router->add('approbations/reject/{id:\d+}', ['controller' => 'Approbation', 'action' => 'reject']);

// Route du chatbot (API JSON)
$router->add('chatbot/ask', ['controller' => 'Chatbot', 'action' => 'ask']);

// Route 404
$router->add('404', ['controller' => 'Error', 'action' => 'notFound']);
