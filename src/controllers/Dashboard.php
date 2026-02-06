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
        $this->requireRole(['admin', 'pilote', 'etudiant', 'recruteur']);

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
        } elseif ($_SESSION['user_role'] === 'recruteur') {
            // Le recruteur voit les stats de ses offres et candidatures reçues
            $stats['total_candidatures'] = $candidatureModel->countByRecruteur($_SESSION['user_id']);
            $stats['nb_entreprises'] = $userModel->countEntreprisesByRecruteur($_SESSION['user_id']);
            $stats['candidatures_par_statut'] = $candidatureModel->countByStatutForRecruteur($_SESSION['user_id']);
            
            // Récupérer les entreprises assignées pour affichage dans le dashboard
            if ($stats['nb_entreprises'] > 0) {
                $stats['entreprises_assignees'] = $userModel->getEntreprisesByRecruteur($_SESSION['user_id']);
            }
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
        $this->requireRole(['admin', 'pilote', 'etudiant', 'recruteur']);

        $offreModel = new Offre();
        $candidatureModel = new Candidature();
        $entrepriseModel = new Entreprise();
        $userModel = new User();

        $data = [
            'title' => 'Statistiques - ' . APP_NAME
        ];

        // SFx 11 - Statistiques des offres pour tous (Carrousel)
        $data['repartitionDuree'] = $offreModel->getRepartitionParDuree();
        $data['topWishlist'] = $offreModel->getTopWishlist(5);
        $totalCandidatures = $candidatureModel->count();
        $data['totalOffres'] = $offreModel->count(); // Besoin pour le calcul ci-dessous et l'affichage global
        $data['moyenneCandidatures'] = ($data['totalOffres'] > 0) ? round($totalCandidatures / $data['totalOffres'], 2) : 0;

        // Rôle Administrateur
        if ($_SESSION['user_role'] === 'admin') {
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
        
        // Rôle Recruteur
        elseif ($_SESSION['user_role'] === 'recruteur') {
            $data['totalEntreprises'] = $entrepriseModel->count();
            $data['totalCandidatures'] = $candidatureModel->count();
            // Statistiques des candidatures par statut
            $allCandidatures = $candidatureModel->getAllWithDetails();
            $statuts = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
            foreach ($allCandidatures as $c) {
                $statuts[$c['statut'] ?? 'en_attente']++;
            }
            $data['candidaturesParStatut'] = $statuts;
        }

        $this->render('dashboard/statistics', $data);
    }
}
