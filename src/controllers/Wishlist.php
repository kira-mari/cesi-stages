<?php
namespace Controllers;

use Core\Controller;
use Models\Wishlist as WishlistModel;

/**
 * Contrôleur de la wishlist
 */
class Wishlist extends Controller
{
    /**
     * Affiche la wishlist de l'étudiant
     *
     * @return void
     */
    public function index()
    {
        $this->requireRole(['etudiant']);

        $wishlistModel = new WishlistModel();
        $offres = $wishlistModel->getByEtudiantWithDetails($_SESSION['user_id']);

        $this->render('wishlist/index', [
            'title' => 'Ma wishlist - ' . APP_NAME,
            'offres' => $offres
        ]);
    }
}
