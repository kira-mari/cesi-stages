<?php
namespace Controllers;

use Core\Controller;

/**
 * Contrôleur de la page d'accueil
 */
class Home extends Controller
{
    /**
     * Page d'accueil
     *
     * @return void
     */
    public function index()
    {
        // Si connecté, rediriger vers le tableau de bord
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $this->render('home/index', [
            'title' => 'Accueil - ' . APP_NAME,
            'description' => 'Plateforme de recherche de stages pour les étudiants CESI',
            'bodyClass' => 'home-page'
        ]);
    }
}
