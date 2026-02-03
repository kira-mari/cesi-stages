# 07 - Lancement et Vérification

Tout est configuré ! Voici comment lancer et vérifier votre projet.

## 1. Démarrage
1. Assurez-vous que **Apache** et **MySQL** sont lancés dans XAMPP.
2. Ouvrez votre navigateur.

## 2. Accès
- Via VHost (Recommandé) : [https://cesi-site.local](https://cesi-site.local)
- Via Localhost standard : [http://localhost/cesi-stages/public](http://localhost/cesi-stages/public)

## 3. Comptes de Test
Utilisez ces comptes pré-insérés pour tester les différents rôles :

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Admin** | `admin@cesi.fr` | `password` |
| **Pilote** | `pilote@cesi.fr` | `password` |
| **Étudiant** | `etudiant@cesi.fr` | `password` |

## 4. Check-list de vérification
- [ ] Le site charge sans erreur.
- [ ] La connexion fonctionne avec les comptes ci-dessus.
- [ ] L'envoi de mail de "Mot de passe oublié" fonctionne (vérifiez votre compte Brevo ou les logs).
- [ ] Le Chatbot garde l'historique pendant la session mais s'efface à la déconnexion.
