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

    /**
     * Page de contact
     *
     * @return void
     */
    public function contact()
    {
        $this->render('pages/contact', [
            'title' => 'Contact - ' . APP_NAME
        ]);
    }
}
