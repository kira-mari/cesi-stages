<?php
namespace Controllers;

use Core\Controller;

/**
 * Contrôleur des erreurs
 */
class Error extends Controller
{
    /**
     * Page 404 - Non trouvé
     *
     * @return void
     */
    public function notFound()
    {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => 'Page non trouvée - ' . APP_NAME
        ]);
    }

    /**
     * Page 403 - Accès interdit
     *
     * @return void
     */
    public function forbidden()
    {
        http_response_code(403);
        $this->render('errors/403', [
            'title' => 'Accès interdit - ' . APP_NAME
        ]);
    }

    /**
     * Page 500 - Erreur serveur
     *
     * @return void
     */
    public function serverError()
    {
        http_response_code(500);
        $this->render('errors/500', [
            'title' => 'Erreur serveur - ' . APP_NAME
        ]);
    }
}
