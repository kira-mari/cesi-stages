<?php
namespace Controllers;

use Core\Controller;
use Models\User;
use Models\Candidature;
use Models\Entreprise;
use Models\Offre;

/**
 * Contrôleur pour les recruteurs
 */
class Recruteur extends Controller
{
    /**
     * Liste des recruteurs (admin uniquement)
     */
    public function index()
    {
        $this->requireRole(['admin']);

        $userModel = new User();
        $recruteurs = $userModel->getAllRecruteurs();

        // Ajouter le nombre d'entreprises assignées à chaque recruteur
        foreach ($recruteurs as &$recruteur) {
            $recruteur['nb_entreprises'] = $userModel->countEntreprisesByRecruteur($recruteur['id']);
        }

        $this->render('recruteurs/index', [
            'title' => 'Gestion des recruteurs - ' . APP_NAME,
            'recruteurs' => $recruteurs,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Affiche le détail d'un recruteur (admin)
     */
    public function show()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? null;
        
        $userModel = new User();
        $recruteur = $userModel->find($id);

        if (!$recruteur || $recruteur['role'] !== 'recruteur') {
            $_SESSION['flash_error'] = "Recruteur non trouvé.";
            $this->redirect('recruteurs');
        }

        $entreprises = $userModel->getEntreprisesByRecruteur($id);
        
        // Toutes les entreprises pour l'assignation
        $entrepriseModel = new Entreprise();
        $toutesEntreprises = $entrepriseModel->all();

        $this->render('recruteurs/show', [
            'title' => $recruteur['prenom'] . ' ' . $recruteur['nom'] . ' - ' . APP_NAME,
            'recruteur' => $recruteur,
            'entreprises' => $entreprises,
            'toutesEntreprises' => $toutesEntreprises,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Assigner une entreprise à un recruteur
     */
    public function assignEntreprise()
    {
        $this->requireRole(['admin']);

        $recruteurId = $this->routeParams['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('recruteurs/' . $recruteurId);
        }

        $entrepriseId = $_POST['entreprise_id'] ?? null;
        
        if (!$entrepriseId) {
            $_SESSION['flash_error'] = "Veuillez sélectionner une entreprise.";
            $this->redirect('recruteurs/show/' . $recruteurId);
        }

        $userModel = new User();
        
        if ($userModel->assignEntrepriseToRecruteur($recruteurId, $entrepriseId)) {
            $_SESSION['flash_success'] = "Entreprise assignée avec succès.";
        } else {
            $_SESSION['flash_error'] = "Cette entreprise est déjà assignée à ce recruteur.";
        }

        $this->redirect('recruteurs/show/' . $recruteurId);
    }

    /**
     * Retirer une entreprise d'un recruteur
     */
    public function removeEntreprise()
    {
        $this->requireRole(['admin']);

        $recruteurId = $this->routeParams['recruteurId'] ?? null;
        $entrepriseId = $this->routeParams['entrepriseId'] ?? null;

        $userModel = new User();
        
        if ($userModel->removeEntrepriseFromRecruteur($recruteurId, $entrepriseId)) {
            $_SESSION['flash_success'] = "Entreprise retirée avec succès.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la suppression.";
        }

        $this->redirect('recruteurs/show/' . $recruteurId);
    }

    // ============================================================
    // FONCTIONS POUR LE RECRUTEUR CONNECTÉ
    // ============================================================

    /**
     * Liste des candidatures reçues par le recruteur
     */
    public function candidatures()
    {
        $this->requireRole(['recruteur']);

        $recruteurId = $_SESSION['user_id'];
        $candidatureModel = new Candidature();
        $userModel = new User();

        // Filtre par statut
        $statut = $_GET['statut'] ?? 'all';
        
        $candidatures = $candidatureModel->getByRecruteurWithDetails($recruteurId);
        
        // Filtrer par statut si nécessaire
        if ($statut !== 'all') {
            $candidatures = array_filter($candidatures, function($c) use ($statut) {
                return $c['statut'] === $statut;
            });
        }

        // Statistiques
        $stats = $candidatureModel->countByStatutForRecruteur($recruteurId);
        $entreprises = $userModel->getEntreprisesByRecruteur($recruteurId);

        $this->render('recruteurs/candidatures', [
            'title' => 'Mes candidatures - ' . APP_NAME,
            'candidatures' => $candidatures,
            'stats' => $stats,
            'entreprises' => $entreprises,
            'filtreStatut' => $statut,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Affiche le détail d'une candidature
     */
    public function showCandidature()
    {
        $this->requireRole(['recruteur']);

        $id = $this->routeParams['id'] ?? null;
        $recruteurId = $_SESSION['user_id'];
        $candidatureModel = new Candidature();

        // Vérifier que la candidature appartient au recruteur
        if (!$candidatureModel->belongsToRecruteur($id, $recruteurId)) {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à cette candidature.";
            $this->redirect('recruteur/candidatures');
        }

        $candidature = $candidatureModel->find($id);
        
        if (!$candidature) {
            $_SESSION['flash_error'] = "Candidature non trouvée.";
            $this->redirect('recruteur/candidatures');
        }

        // Récupérer les détails
        $userModel = new User();
        $offreModel = new Offre();
        $entrepriseModel = new Entreprise();

        $etudiant = $userModel->find($candidature['etudiant_id']);
        $offre = $offreModel->find($candidature['offre_id']);
        $entreprise = $offre ? $entrepriseModel->find($offre['entreprise_id']) : null;

        $this->render('recruteurs/candidature-detail', [
            'title' => 'Détail candidature - ' . APP_NAME,
            'candidature' => $candidature,
            'etudiant' => $etudiant,
            'offre' => $offre,
            'entreprise' => $entreprise,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Met à jour le statut d'une candidature
     */
    public function updateCandidature()
    {
        $this->requireRole(['recruteur']);

        $id = $this->routeParams['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('recruteur/candidatures');
        }

        $recruteurId = $_SESSION['user_id'];
        $candidatureModel = new Candidature();

        // Vérifier que la candidature appartient au recruteur
        if (!$candidatureModel->belongsToRecruteur($id, $recruteurId)) {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à cette candidature.";
            $this->redirect('recruteur/candidatures');
        }

        $newStatut = $_POST['statut'] ?? null;
        
        if (!in_array($newStatut, ['en_attente', 'acceptee', 'refusee'])) {
            $_SESSION['flash_error'] = "Statut invalide.";
            $this->redirect('recruteur/candidature/' . $id);
        }

        if ($candidatureModel->update($id, ['statut' => $newStatut])) {
            $statutLabel = ['en_attente' => 'en attente', 'acceptee' => 'acceptée', 'refusee' => 'refusée'][$newStatut];
            $_SESSION['flash_success'] = "Candidature marquée comme {$statutLabel}.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
        }

        // Rediriger vers la liste ou vers le détail selon le contexte
        $redirectTo = $_POST['redirect'] ?? 'detail';
        if ($redirectTo === 'list') {
            $this->redirect('recruteur/candidatures');
        } else {
            $this->redirect('recruteur/candidature/' . $id);
        }
    }

    /**
     * Mes entreprises assignées
     */
    public function mesEntreprises()
    {
        $this->requireRole(['recruteur']);

        $recruteurId = $_SESSION['user_id'];
        $userModel = new User();

        $entreprises = $userModel->getEntreprisesByRecruteur($recruteurId);

        $this->render('recruteurs/mes-entreprises', [
            'title' => 'Mes entreprises - ' . APP_NAME,
            'entreprises' => $entreprises
        ]);
    }

    /**
     * Configuration de l'entreprise pour un nouveau recruteur
     */
    public function configurerEntreprise()
    {
        $this->requireRole(['recruteur']);

        $userModel = new User();
        $entrepriseModel = new Entreprise();
        $recruteurId = $_SESSION['user_id'];

        // Vérifier si le recruteur a déjà une entreprise
        $entreprisesExistantes = $userModel->getEntreprisesByRecruteur($recruteurId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('recruteur/configurer-entreprise');
                return;
            }

            $action = $_POST['action'] ?? '';
            
            if ($action === 'select_existing') {
                // Sélectionner une entreprise existante
                $entrepriseId = (int) ($_POST['entreprise_id'] ?? 0);
                
                if ($entrepriseId > 0) {
                    // Vérifier que l'entreprise existe
                    $entreprise = $entrepriseModel->find($entrepriseId);
                    if ($entreprise) {
                        // Assigner l'entreprise au recruteur
                        $userModel->assignEntrepriseToRecruteur($recruteurId, $entrepriseId);
                        $_SESSION['flash_success'] = "Vous avez été associé à l'entreprise " . $entreprise['nom'] . ".";
                        $this->redirect('dashboard');
                        return;
                    }
                }
                $_SESSION['flash_error'] = "Entreprise non trouvée.";
                
            } elseif ($action === 'create_new') {
                // Créer une nouvelle entreprise
                $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
                $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
                $email = filter_input(INPUT_POST, 'email_entreprise', FILTER_SANITIZE_EMAIL);
                $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
                $site_web = htmlspecialchars(trim($_POST['site_web'] ?? ''));
                $description = htmlspecialchars(trim($_POST['description'] ?? ''));
                $secteur = htmlspecialchars(trim($_POST['secteur'] ?? ''));

                // Validation
                if (empty($nom)) {
                    $_SESSION['flash_error'] = "Le nom de l'entreprise est obligatoire.";
                    $this->redirect('recruteur/configurer-entreprise');
                    return;
                }

                // Vérifier si l'entreprise existe déjà (par nom)
                $existante = $entrepriseModel->where('nom', $nom);
                if (!empty($existante)) {
                    // L'entreprise existe, proposer de s'y associer
                    $userModel->assignEntrepriseToRecruteur($recruteurId, $existante[0]['id']);
                    $_SESSION['flash_success'] = "L'entreprise " . $nom . " existait déjà. Vous y avez été associé.";
                    $this->redirect('dashboard');
                    return;
                }

                // Créer l'entreprise
                $entrepriseId = $entrepriseModel->create([
                    'nom' => $nom,
                    'adresse' => $adresse,
                    'email' => $email,
                    'telephone' => $telephone,
                    'site_web' => $site_web,
                    'description' => $description,
                    'secteur' => $secteur
                ]);

                if ($entrepriseId) {
                    // Assigner au recruteur
                    $userModel->assignEntrepriseToRecruteur($recruteurId, $entrepriseId);
                    $_SESSION['flash_success'] = "Entreprise créée avec succès ! Vous pouvez maintenant publier des offres.";
                    $this->redirect('dashboard');
                    return;
                } else {
                    $_SESSION['flash_error'] = "Erreur lors de la création de l'entreprise.";
                }
            }
        }

        // Liste des entreprises existantes pour le choix
        $toutesEntreprises = $entrepriseModel->all();

        $this->render('recruteurs/configurer-entreprise', [
            'title' => 'Configurer mon entreprise - ' . APP_NAME,
            'entreprisesExistantes' => $entreprisesExistantes,
            'toutesEntreprises' => $toutesEntreprises,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Supprime un recruteur (admin uniquement)
     */
    public function delete()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();

        $recruteur = $userModel->find($id);
        if (!$recruteur || $recruteur['role'] !== 'recruteur') {
            $_SESSION['flash_error'] = "Recruteur non trouvé.";
            $this->redirect('recruteurs');
            return;
        }

        // Supprimer les associations entreprise-recruteur
        $stmt = \Core\Model::getDB()->prepare(
            "DELETE FROM recruteur_entreprise WHERE recruteur_id = :id"
        );
        $stmt->execute([':id' => $id]);

        // Supprimer le recruteur
        if ($userModel->delete($id)) {
            $_SESSION['flash_success'] = "Recruteur supprimé avec succès !";
        } else {
            $_SESSION['flash_error'] = "Une erreur s'est produite lors de la suppression.";
        }

        $this->redirect('recruteurs');
    }
}
