<?php
namespace Controllers;

use Core\Controller;

/**
 * Contrôleur des pages statiques
 */
class Page extends Controller
{
    /**
     * Page des mentions légales
     *
     * @return void
     */
    public function mentionsLegales()
    {
        $this->render('pages/mentions-legales', [
            'title' => 'Mentions légales - ' . APP_NAME
        ]);
    }
}
