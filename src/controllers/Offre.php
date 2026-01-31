<?php
namespace Controllers;

use Core\Controller;
use Models\Offre as OffreModel;
use Models\Entreprise;
use Models\Candidature;
use Models\Wishlist;

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
        } else {
            $offres = $offreModel->getAllWithEntreprise($page, ITEMS_PER_PAGE);
            $total = $offreModel->count();
        }

        $totalPages = ceil($total / ITEMS_PER_PAGE);

        // Récupération des compétences pour le filtre
        $competences = $offreModel->getAllCompetences();

        $this->render('offres/index', [
            'title' => 'Offres de stage - ' . APP_NAME,
            'offres' => $offres,
            'competences' => $competences,
            'search' => $search,
            'competence' => $competence,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
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
        $this->requireRole(['admin', 'pilote']);

        $entrepriseModel = new Entreprise();
        $entreprises = $entrepriseModel->all();

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
        $this->requireRole(['admin', 'pilote']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
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

        if (!$wishlistModel->isInWishlist($_SESSION['user_id'], $id)) {
            $wishlistModel->create([
                'etudiant_id' => $_SESSION['user_id'],
                'offre_id' => $id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $_SESSION['flash_success'] = "Offre ajoutée à votre wishlist !";
        } else {
            $_SESSION['flash_info'] = "Cette offre est déjà dans votre wishlist.";
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
