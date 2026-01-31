<?php
namespace Controllers;

use Core\Controller;
use Models\Offre;
use Models\Entreprise;
use Models\Candidature;
use Models\User;

/**
 * Contrôleur du tableau de bord
 */
class Dashboard extends Controller
{
    /**
     * Page principale du tableau de bord
     *
     * @return void
     */
    public function index()
    {
        $this->requireRole(['admin', 'pilote', 'etudiant']);

        $stats = [];
        $offreModel = new Offre();
        $entrepriseModel = new Entreprise();
        $candidatureModel = new Candidature();
        $userModel = new User();

        // Statistiques communes
        $stats['total_offres'] = $offreModel->count();
        $stats['total_entreprises'] = $entrepriseModel->count();

        // Statistiques selon le rôle
        if ($_SESSION['user_role'] === 'admin') {
            $stats['total_etudiants'] = $userModel->countByRole('etudiant');
            $stats['total_pilotes'] = $userModel->countByRole('pilote');
            $stats['total_candidatures'] = $candidatureModel->count();
        } elseif ($_SESSION['user_role'] === 'pilote') {
            $stats['candidatures_etudiants'] = $candidatureModel->countByPilote($_SESSION['user_id']);
        } elseif ($_SESSION['user_role'] === 'etudiant') {
            $stats['mes_candidatures'] = $candidatureModel->countByEtudiant($_SESSION['user_id']);
            $stats['wishlist_count'] = (new \Models\Wishlist())->countByEtudiant($_SESSION['user_id']);
        }

        // Dernières offres
        $dernieresOffres = $offreModel->getRecentes(5);

        $this->render('dashboard/index', [
            'title' => 'Tableau de bord - ' . APP_NAME,
            'stats' => $stats,
            'dernieresOffres' => $dernieresOffres
        ]);
    }

    /**
     * Page des statistiques détaillées
     *
     * @return void
     */
    public function statistics()
    {
        $this->requireRole(['admin', 'pilote', 'etudiant']);

        $offreModel = new Offre();
        $candidatureModel = new Candidature();
        $entrepriseModel = new Entreprise();
        $userModel = new User();

        $data = [
            'title' => 'Statistiques - ' . APP_NAME
        ];

        // Rôle Administrateur
        if ($_SESSION['user_role'] === 'admin') {
            $data['totalOffres'] = $offreModel->count();
            $data['totalEntreprises'] = $entrepriseModel->count();
            $data['totalPilotes'] = $userModel->countByRole('pilote');
            $data['totalEtudiants'] = $userModel->countByRole('etudiant');
            $data['totalAdmins'] = $userModel->countByRole('admin');
        } 
        
        // Rôle Pilote
        elseif ($_SESSION['user_role'] === 'pilote') {
            $data['offresEtudiants'] = $candidatureModel->getByPiloteWithDetails($_SESSION['user_id']);
            $data['statsEtudiants'] = $candidatureModel->getStatsByEtudiantsPilote($_SESSION['user_id']);
            $data['topWishlistPromo'] = $offreModel->getTopWishlistByPilote($_SESSION['user_id']);
        }
        
        // Rôle Étudiant
        elseif ($_SESSION['user_role'] === 'etudiant') {
            // Offres postulées
            $offresPostulees = $candidatureModel->getByEtudiantWithDetails($_SESSION['user_id']);
            $data['offresPostulees'] = $offresPostulees;
            
            // Calcul des compétences
            $competencesCount = [];
            foreach ($offresPostulees as $cand) {
                // Il faudrait récupérer les compétences de l'offre.
                // Ici $cand contient les infos de l'offre (jointure dans getByEtudiantWithDetails)
                // Mais getByEtudiantWithDetails récupère 'titre', 'description', 'entreprise_nom', etc.
                // Il faut s'assurer qu'il récupère 'competences' ou refaire une query.
                // Vérifions getByEtudiantWithDetails dans CandidatureModel.
                
                // On va charger l'offre complète pour être sûr
                $offreFull = $offreModel->find($cand['offre_id']);
                if ($offreFull && !empty($offreFull['competences'])) {
                    $comps = json_decode($offreFull['competences'], true);
                    if (is_array($comps)) {
                        foreach ($comps as $c) {
                            $c = trim($c);
                            if (!isset($competencesCount[$c])) $competencesCount[$c] = 0;
                            $competencesCount[$c]++;
                        }
                    }
                }
            }
            arsort($competencesCount);
            $data['repartitionCompetences'] = array_slice($competencesCount, 0, 10); // Top 10
        }

        $this->render('dashboard/statistics', $data);
    }
}
