<?php
namespace Controllers;

use Core\Controller;
use Models\Offre as OffreModel;
use Models\Entreprise;
use Models\Candidature;
use Models\Wishlist;
use Models\User;

/**
 * Contrôleur des offres de stage
 */
class Offre extends Controller
{
    /**
     * Liste des offres
     *
     * @return void
     */
    public function index()
    {
        $offreModel = new OffreModel();
        $search = $_GET['search'] ?? '';
        $competence = $_GET['competence'] ?? '';
        $page = intval($_GET['page'] ?? 1);

        if (!empty($search) || !empty($competence)) {
            $offres = $offreModel->searchAdvanced($search, $competence);
            $total = count($offres);
            $offresMap = $offres; // Pour la recherche, on affiche tous les résultats sur la carte
        } else {
            $offres = $offreModel->getAllWithEntreprise($page, ITEMS_PER_PAGE);
            $total = $offreModel->count();
            // Pour l'affichage normal, on veut TOUTES les offres sur la carte, pas juste la page courante
            $offresMap = $offreModel->getAllForMap();
        }

        $totalPages = ceil($total / ITEMS_PER_PAGE);

        // Récupération des compétences pour le filtre
        $competences = $offreModel->getAllCompetences();
        
        // Wishlist for user
        $wishlistIds = [];
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant') {
            $wishlistModel = new Wishlist();
            $userWishlist = $wishlistModel->getByEtudiant($_SESSION['user_id']);
            $wishlistIds = array_column($userWishlist, 'offre_id');
        }

        $this->render('offres/index', [
            'title' => 'Offres de stage - ' . APP_NAME,
            'offres' => $offres,
            'offresMap' => $offresMap,
            'competences' => $competences,
            'search' => $search,
            'competence' => $competence,
            'page' => $page,
            'totalPages' => $totalPages,
            'wishlistIds' => $wishlistIds
        ]);
    }

    /**
     * Affiche une offre
     *
     * @return void
     */
    public function show()
    {
        $id = $this->routeParams['id'] ?? 0;
        $offreModel = new OffreModel();
        $offre = $offreModel->getWithEntreprise($id);

        if (!$offre) {
            $_SESSION['flash_error'] = "Offre non trouvée.";
            $this->redirect('offres');
        }

        // Vérifier si l'offre est dans la wishlist de l'étudiant
        $inWishlist = false;
        $aPostule = false;

        if ($this->isAuthenticated() && $_SESSION['user_role'] === 'etudiant') {
            $wishlistModel = new Wishlist();
            $inWishlist = $wishlistModel->isInWishlist($_SESSION['user_id'], $id);

            $candidatureModel = new Candidature();
            $aPostule = $candidatureModel->aPostule($_SESSION['user_id'], $id);
        }

        // Nombre de candidatures
        $nbCandidatures = (new Candidature())->countByOffre($id);

        $this->render('offres/show', [
            'title' => $offre['titre'] . ' - ' . APP_NAME,
            'offre' => $offre,
            'inWishlist' => $inWishlist,
            'aPostule' => $aPostule,
            'nbCandidatures' => $nbCandidatures,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Formulaire de création d'offre
     *
     * @return void
     */
    public function create()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur']);

        $entrepriseModel = new Entreprise();
        
        // Si recruteur, ne montrer que ses entreprises assignées
        if ($_SESSION['user_role'] === 'recruteur') {
            $userModel = new User();
            $entreprises = $userModel->getEntreprisesByRecruteur($_SESSION['user_id']);
        } else {
            $entreprises = $entrepriseModel->all();
        }

        $this->render('offres/create', [
            'title' => 'Créer une offre - ' . APP_NAME,
            'entreprises' => $entreprises,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Enregistre une nouvelle offre
     *
     * @return void
     */
    public function store()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            
            // Si recruteur, vérifier que l'entreprise lui est assignée
            if ($_SESSION['user_role'] === 'recruteur') {
                $userModel = new User();
                $entreprisesRecruteur = $userModel->getEntreprisesByRecruteur($_SESSION['user_id']);
                $entrepriseIds = array_column($entreprisesRecruteur, 'id');
                $entrepriseId = (int) ($_POST['entreprise_id'] ?? 0);
                
                if (!in_array($entrepriseId, $entrepriseIds)) {
                    $_SESSION['flash_error'] = "Vous n'êtes pas autorisé à créer une offre pour cette entreprise.";
                    $this->redirect('offres/create');
                    return;
                }
            }
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('offres/create');
            }

            $entrepriseId = intval($_POST['entreprise_id'] ?? 0);
            $titre = htmlspecialchars(trim($_POST['titre'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $competences = $_POST['competences'] ?? [];
            $remuneration = floatval($_POST['remuneration'] ?? 0);
            $duree = intval($_POST['duree'] ?? 0);
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';

            if (empty($titre) || empty($description) || $entrepriseId === 0) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('offres/create');
            }

            $offreModel = new OffreModel();
            $offreId = $offreModel->create([
                'entreprise_id' => $entrepriseId,
                'titre' => $titre,
                'description' => $description,
                'competences' => json_encode($competences),
                'remuneration' => $remuneration,
                'duree' => $duree,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($offreId) {
                $_SESSION['flash_success'] = "Offre créée avec succès !";
                $this->redirect('offres/show/' . $offreId);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la création.";
                $this->redirect('offres/create');
            }
        }
    }

    /**
     * Formulaire de modification d'offre
     *
     * @return void
     */
    public function edit()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $offreModel = new OffreModel();
        $offre = $offreModel->find($id);

        if (!$offre) {
            $_SESSION['flash_error'] = "Offre non trouvée.";
            $this->redirect('offres');
        }

        $entrepriseModel = new Entreprise();
        $entreprises = $entrepriseModel->all();

        $offre['competences'] = json_decode($offre['competences'], true) ?? [];

        $this->render('offres/edit', [
            'title' => 'Modifier une offre - ' . APP_NAME,
            'offre' => $offre,
            'entreprises' => $entreprises,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Met à jour une offre
     *
     * @return void
     */
    public function update()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('offres/edit/' . $id);
            }

            $entrepriseId = intval($_POST['entreprise_id'] ?? 0);
            $titre = htmlspecialchars(trim($_POST['titre'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $competences = $_POST['competences'] ?? [];
            $remuneration = floatval($_POST['remuneration'] ?? 0);
            $duree = intval($_POST['duree'] ?? 0);
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';

            if (empty($titre) || empty($description) || $entrepriseId === 0) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('offres/edit/' . $id);
            }

            $offreModel = new OffreModel();
            $success = $offreModel->update($id, [
                'entreprise_id' => $entrepriseId,
                'titre' => $titre,
                'description' => $description,
                'competences' => json_encode($competences),
                'remuneration' => $remuneration,
                'duree' => $duree,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($success) {
                $_SESSION['flash_success'] = "Offre mise à jour avec succès !";
                $this->redirect('offres/show/' . $id);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la mise à jour.";
                $this->redirect('offres/edit/' . $id);
            }
        }
    }

    /**
     * Supprime une offre
     *
     * @return void
     */
    public function delete()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $offreModel = new OffreModel();

        if ($offreModel->delete($id)) {
            $_SESSION['flash_success'] = "Offre supprimée avec succès !";
        } else {
            $_SESSION['flash_error'] = "Une erreur s'est produite lors de la suppression.";
        }

        $this->redirect('offres');
    }

    /**
     * Postuler à une offre
     *
     * @return void
     */
    public function postuler()
    {
        $this->requireRole(['etudiant']);

        $id = $this->routeParams['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('offres/show/' . $id);
            }

            $lettreMotivation = htmlspecialchars(trim($_POST['lettre_motivation'] ?? ''));

            // Gestion du CV
            $cvPath = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $cvPath = $this->handleCvUpload($_FILES['cv']);
                if (!$cvPath) {
                    $_SESSION['flash_error'] = "Erreur lors du téléchargement du CV.";
                    $this->redirect('offres/show/' . $id);
                }
            }

            $candidatureModel = new Candidature();
            $candidatureModel->create([
                'offre_id' => $id,
                'etudiant_id' => $_SESSION['user_id'],
                'lettre_motivation' => $lettreMotivation,
                'cv_path' => $cvPath,
                'statut' => 'en_attente',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $_SESSION['flash_success'] = "Candidature envoyée avec succès !";
            $this->redirect('candidatures/etudiant');
        }
    }

    /**
     * Ajoute une offre à la wishlist
     *
     * @return void
     */
    public function addToWishlist()
    {
        $this->requireRole(['etudiant']);

        $id = $this->routeParams['id'] ?? 0;
        $wishlistModel = new Wishlist();
        $added = false;

        if (!$wishlistModel->isInWishlist($_SESSION['user_id'], $id)) {
            $wishlistModel->create([
                'etudiant_id' => $_SESSION['user_id'],
                'offre_id' => $id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $_SESSION['flash_success'] = "Offre ajoutée à votre wishlist !";
            $added = true;
        } else {
            $_SESSION['flash_info'] = "Cette offre est déjà dans votre wishlist.";
        }

        // AJAX Response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Nettoyage des messages flash pour éviter qu'ils ne réapparaissent au rechargement
            unset($_SESSION['flash_success']);
            unset($_SESSION['flash_info']);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'state' => 'added',
                'message' => $added ? "Offre ajoutée à votre liste" : "Déjà dans votre liste"
            ]);
            exit;
        }

        // Redirection vers la page précédente si possible, sinon vers l'offre
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        $this->redirect('offres/show/' . $id);
    }
    
    /**
     * Retire une offre de la wishlist
     *
     * @return void
     */
    public function removeFromWishlist()
    {
        $this->requireRole(['etudiant']);

        $id = $this->routeParams['id'] ?? 0;
        $wishlistModel = new Wishlist();

        $wishlistModel->remove($_SESSION['user_id'], $id);
        $_SESSION['flash_success'] = "Offre retirée de votre wishlist.";

        // AJAX Response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Nettoyage des messages flash pour éviter qu'ils ne réapparaissent au rechargement
            unset($_SESSION['flash_success']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'state' => 'removed',
                'message' => "Offre retirée de votre liste"
            ]);
            exit;
        }

        // Redirection vers la page précédente si possible, sinon vers la wishlist
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $this->redirect('wishlist');
    }

    /**
     * Gère le téléchargement du CV
     *
     * @param array $file
     * @return string|false
     */
    private function handleCvUpload($file)
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return false;
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return false;
        }

        $filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = UPLOAD_PATH . '/cv/' . $filename;

        if (!is_dir(UPLOAD_PATH . '/cv')) {
            mkdir(UPLOAD_PATH . '/cv', 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'cv/' . $filename;
        }

        return false;
    }
}
