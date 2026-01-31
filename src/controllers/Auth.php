<?php
namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Contrôleur d'authentification
 */
class Auth extends Controller
{
    /**
     * Page de connexion
     *
     * @return void
     */
    public function login()
    {
        // Si déjà connecté, rediriger vers le tableau de bord
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors[] = "Veuillez remplir tous les champs.";
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];

                    // Régénération de l'ID de session pour la sécurité
                    session_regenerate_id(true);

                    $_SESSION['flash_success'] = "Connexion réussie ! Bienvenue, " . $user['prenom'] . ".";
                    $this->redirect('dashboard');
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            }
        }

        $this->render('auth/login', [
            'title' => 'Connexion - ' . APP_NAME,
            'errors' => $errors,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Déconnexion
     *
     * @return void
     */
    public function logout()
    {
        // Suppression de toutes les variables de session
        $_SESSION = [];

        // Destruction du cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        // Destruction de la session
        session_destroy();

        // Redirection vers la page d'accueil
        header("Location: " . BASE_URL);
        exit;
    }

    /**
     * Page d'inscription (Publique)
     *
     * @return void
     */
    public function register()
    {
        // Si déjà connecté, rediriger
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $errors[] = "Token de sécurité invalide.";
            } else {
                $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
                $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                // Par défaut, l'inscription publique crée un étudiant
                $role = 'etudiant';

                // Validation
                if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                    $errors[] = "Veuillez remplir tous les champs obligatoires.";
                }

                if ($password !== $confirm_password) {
                    $errors[] = "Les mots de passe ne correspondent pas.";
                }

                if (strlen($password) < 8) {
                    $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
                }

                if (empty($errors)) {
                    $userModel = new User();

                    // Vérification si l'email existe déjà
                    if ($userModel->findByEmail($email)) {
                        $errors[] = "Cet email est déjà utilisé.";
                    } else {
                        // Création de l'utilisateur
                        $userId = $userModel->create([
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'email' => $email,
                            'password' => password_hash($password, PASSWORD_BCRYPT),
                            'role' => $role
                        ]);

                        if ($userId) {
                            $_SESSION['flash_success'] = "Compte créé avec succès ! Connectez-vous.";
                            $this->redirect('login');
                        } else {
                            $errors[] = "Une erreur s'est produite lors de la création de l'utilisateur.";
                        }
                    }
                }
            }
        }

        $this->render('auth/register', [
            'title' => 'Créer un compte - ' . APP_NAME,
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
}
