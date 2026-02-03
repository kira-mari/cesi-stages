<?php
namespace Core;

/**
 * Classe de base pour tous les contrôleurs
 */
abstract class Controller
{
    /**
     * @var array Paramètres de la route
     */
    protected $routeParams = [];

    /**
     * Constructeur
     *
     * @param array $routeParams
     */
    public function __construct($routeParams = [])
    {
        $this->routeParams = $routeParams;
    }

    /**
     * Méthode appelée avant l'action du contrôleur
     * Peut être surchargée dans les classes filles
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * Méthode appelée après l'action du contrôleur
     * Peut être surchargée dans les classes filles
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Rend une vue
     *
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @return void
     */
    protected function render($view, $data = [])
    {
        // Extraction des données pour les rendre disponibles dans la vue
        extract($data, EXTR_SKIP);

        // Construction du chemin de la vue
        $viewFile = APP_PATH . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("Vue {$view} non trouvée");
        }

        // Inclusion de la vue dans le layout
        $content = $this->getViewContent($viewFile, $data);
        
        // Chargement du layout
        require APP_PATH . '/views/layouts/main.php';
    }

    /**
     * Récupère le contenu d'une vue
     *
     * @param string $viewFile
     * @param array $data
     * @return string
     */
    protected function getViewContent($viewFile, $data = [])
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    /**
     * Rend une vue partielle
     *
     * @param string $partial Nom de la vue partielle
     * @param array $data Données à passer à la vue
     * @return void
     */
    protected function renderPartial($partial, $data = [])
    {
        extract($data, EXTR_SKIP);
        $partialFile = APP_PATH . "/views/partials/{$partial}.php";

        if (file_exists($partialFile)) {
            require $partialFile;
        }
    }

    /**
     * Redirige vers une URL
     *
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        header("Location: " . BASE_URL . "/" . ltrim($url, '/'));
        exit;
    }

    /**
     * Retourne une réponse JSON
     *
     * @param mixed $data
     * @return void
     */
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        // 1. Session active ?
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        
        // 2. Cookie "Se souvenir de moi" ?
        if (isset($_COOKIE['remember_me'])) {
            $parts = explode(':', $_COOKIE['remember_me']);
            if (count($parts) === 2) {
                list($userId, $token) = $parts;
                
                // Essai de reconnexion auto
                if (class_exists('\Models\User')) {
                    try {
                        $userModel = new \Models\User();
                        $user = $userModel->find($userId);
                        
                        // On vérifie que le token correspond
                        // Note: nécessite la colonne remember_token (Migration 002)
                        if ($user && isset($user['remember_token']) && $user['remember_token'] === $token) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_email'] = $user['email'];
                            $_SESSION['user_role'] = $user['role'];
                            $_SESSION['user_nom'] = $user['nom'];
                            $_SESSION['user_prenom'] = $user['prenom'];
                            session_regenerate_id(true);
                            return true;
                        }
                    } catch (\Exception $e) {
                        // Erreur silencieuse
                    }
                }
            }
            
            // Si le cookie est là mais invalide (token changé, user supprimé...), on le vire
            setcookie('remember_me', '', time() - 3600, "/");
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     *
     * @param string|array $roles
     * @return bool
     */
    protected function hasRole($roles)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        return in_array($_SESSION['user_role'], $roles);
    }

    /**
     * Vérifie les permissions et redirige si nécessaire
     *
     * @param string|array $roles
     * @return void
     */
    protected function requireRole($roles)
    {
        if (!$this->hasRole($roles)) {
            $_SESSION['flash_error'] = "Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
            $this->redirect('login');
        }
    }

    /**
     * Génère un token CSRF
     *
     * @return string
     */
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Vérifie le token CSRF
     *
     * @param string $token
     * @return bool
     */
    protected function verifyCsrfToken($token)
    {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}
