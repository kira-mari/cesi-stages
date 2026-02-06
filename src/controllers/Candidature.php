<?php
namespace Controllers;

use Core\Controller;
use Models\Candidature as CandidatureModel;

/**
 * Contrôleur des candidatures
 */
class Candidature extends Controller
{
    /**
     * Liste des candidatures (admin)
     *
     * @return void
     */
    public function index()
    {
        $this->requireRole(['admin']);

        $candidatureModel = new CandidatureModel();
        $candidatures = $candidatureModel->getAllWithDetails();

        $this->render('candidatures/index', [
            'title' => 'Candidatures - ' . APP_NAME,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * Candidatures de l'étudiant connecté
     *
     * @return void
     */
    public function mesCandidatures()
    {
        $this->requireRole(['etudiant']);

        $candidatureModel = new CandidatureModel();
        $candidatures = $candidatureModel->getByEtudiantWithDetails($_SESSION['user_id']);

        $this->render('candidatures/mes-candidatures', [
            'title' => 'Mes candidatures - ' . APP_NAME,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * Candidatures des étudiants du pilote
     *
     * @return void
     */
    public function candidaturesPilote()
    {
        // Vérifier l'approbation pour les pilotes
        if ($_SESSION['user_role'] === 'pilote' && isset($_SESSION['user_is_approved']) && $_SESSION['user_is_approved'] === false) {
            $this->redirect('dashboard');
            return;
        }
        $this->requireRole(['pilote']);

        $candidatureModel = new CandidatureModel();
        $candidatures = $candidatureModel->getByPiloteWithDetails($_SESSION['user_id']);

        $this->render('candidatures/pilote', [
            'title' => 'Candidatures de mes étudiants - ' . APP_NAME,
            'candidatures' => $candidatures
        ]);
    }
}
