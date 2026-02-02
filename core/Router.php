<?php
namespace Core;

/**
 * Classe Router - Gestion du routage des requêtes
 */
class Router
{
    /**
     * @var array Tableau des routes enregistrées
     */
    protected $routes = [];

    /**
     * @var array Paramètres de la route actuelle
     */
    protected $params = [];

    /**
     * Ajoute une route au routeur
     *
     * @param string $route URL de la route
     * @param array $params Paramètres de la route (controller, action, etc.)
     * @return void
     */
    public function add($route, $params = [])
    {
        // Conversion de la route en expression régulière
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-zA-Z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Récupère toutes les routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Fait correspondre une URL avec une route
     *
     * @param string $url URL à faire correspondre
     * @return bool
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Récupère les paramètres de la route actuelle
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Dispatch la requête vers le contrôleur approprié
     *
     * @return void
     */
    public function dispatch()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        
        // Suppression des paramètres GET de l'URL
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }

        // Suppression du sous-dossier de l'URL si présent
        $path = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $basePath = trim($path, '/');
        if ($basePath && strpos($url, $basePath) === 0) {
            $url = substr($url, strlen($basePath));
            $url = trim($url, '/');
        }

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = "Controllers\\" . $controller;

            if (class_exists($controller)) {
                $controllerObject = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controllerObject, $action])) {
                    $controllerObject->$action();
                } else {
                    throw new \Exception("Méthode $action non trouvée dans le contrôleur $controller");
                }
            } else {
                throw new \Exception("Contrôleur $controller non trouvé");
            }
        } else {
            // Route non trouvée - 404
            $controller = new \Controllers\Error($this->params);
            $controller->notFound();
        }
    }

    /**
     * Convertit une chaîne en StudlyCaps
     *
     * @param string $string
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convertit une chaîne en camelCase
     *
     * @param string $string
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }
}
