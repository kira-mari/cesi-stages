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
     * Démarre le flux OAuth Google (redirection vers Google)
     */
    public function googleRedirect()
    {
        if (!GOOGLE_OAUTH_ENABLED) {
            http_response_code(404);
            echo 'Google SSO non configuré.';
            exit;
        }

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'select_account'
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Callback Google OAuth: échange le code, récupère les infos utilisateur
     */
    public function googleCallback()
    {
        if (!GOOGLE_OAUTH_ENABLED) {
            http_response_code(404);
            echo 'Google SSO non configuré.';
            exit;
        }

        $code = $_GET['code'] ?? null;
        if (!$code) {
            $_SESSION['flash_error'] = 'Authentification Google annulée.';
            $this->redirect('login');
        }

        // Échange du code contre un token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $post = http_build_query([
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT,
            'grant_type' => 'authorization_code'
        ]);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $post,
                'timeout' => 10
            ]
        ];

        $response = @file_get_contents($tokenUrl, false, stream_context_create($opts));
        if ($response === false) {
            $_SESSION['flash_error'] = 'Erreur lors de la récupération du token Google.';
            $this->redirect('login');
        }

        $data = json_decode($response, true);
        if (empty($data['access_token'])) {
            $_SESSION['flash_error'] = 'Token Google invalide.';
            $this->redirect('login');
        }

        // Récupération des informations utilisateur
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer " . $data['access_token'] . "\r\n",
                'timeout' => 10
            ]
        ];

        $userInfo = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, stream_context_create($opts));
        if ($userInfo === false) {
            $_SESSION['flash_error'] = 'Impossible de récupérer les informations Google.';
            $this->redirect('login');
        }

        $u = json_decode($userInfo, true);
        $email = $u['email'] ?? null;
        $prenom = $u['given_name'] ?? ($u['name'] ?? '');
        $nom = $u['family_name'] ?? '';

        if (!$email) {
            $_SESSION['flash_error'] = 'Email Google introuvable.';
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            // Création d'un compte utilisateur par défaut (rôle étudiant)
            $password = bin2hex(random_bytes(8));
            $userId = $userModel->create([
                'nom' => $nom ?: 'Google',
                'prenom' => $prenom ?: 'User',
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'etudiant'
            ]);

            if ($userId) {
                $user = $userModel->findByEmail($email);
            }
        }

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            session_regenerate_id(true);
            $_SESSION['flash_success'] = "Connexion via Google réussie. Bienvenue, " . ($user['prenom'] ?? '') . ".";
            $this->redirect('dashboard');
        }

        $_SESSION['flash_error'] = 'Impossible de créer ou trouver l\'utilisateur.';
        $this->redirect('login');
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

    /**
     * Page de profil utilisateur
     */
    public function profile()
    {
        // Vérification si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        if (!$user) {
            // Si l'utilisateur n'existe pas en base, on le déconnecte
            session_destroy();
            $this->redirect('login');
        }

        $this->render('auth/profile', [
            'title' => 'Mon Profil - ' . APP_NAME,
            'user' => $user
        ]);
    }

    /**
     * Affiche le formulaire d'édition du profil
     */
    public function editProfile()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        $this->render('auth/edit-profile', [
            'title' => 'Modifier mon profil - ' . APP_NAME,
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Traite la mise à jour du profil
     */
    public function updateProfile()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erreur de sécurité CSRF");
            }

            $userModel = new User();
            $data = [
                'nom' => htmlspecialchars($_POST['nom']),
                'prenom' => htmlspecialchars($_POST['prenom']),
                'telephone' => htmlspecialchars($_POST['telephone']),
                'age' => !empty($_POST['age']) ? intval($_POST['age']) : null,
                'adresse' => htmlspecialchars($_POST['adresse']),
                'bio' => htmlspecialchars($_POST['bio'])
            ];

            // Mise à jour (le mot de passe n'est pas modifié ici pour simplifier)
            // Pour modifier le mot de passe, il faudrait une méthode dédiée
            
            if ($userModel->update($_SESSION['user_id'], $data)) {
                // Mise à jour de la session
                $_SESSION['user_nom'] = $data['nom'];
                $_SESSION['user_prenom'] = $data['prenom'];
                
                $_SESSION['flash_success'] = "Profil mis à jour avec succès.";
                $this->redirect('profile');
            } else {
                 $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
                 $this->redirect('profile/edit');
            }
        }
    }
}
