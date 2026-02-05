<?php
namespace Controllers;

use Core\Controller;
use Models\Entreprise as EntrepriseModel;
use Models\Evaluation;

/**
 * Contrôleur des entreprises
 */
class Entreprise extends Controller
{
    /**
     * Liste des entreprises
     *
     * @return void
     */
    public function index()
    {
        $entrepriseModel = new EntrepriseModel();
        $search = $_GET['search'] ?? '';
        $ville = $_GET['ville'] ?? '';
        $page = intval($_GET['page'] ?? 1);

        // Récupération des filtres pour le select
        $villes = $entrepriseModel->getAllVilles();

        if (!empty($search) || !empty($ville)) {
            $entreprises = $entrepriseModel->searchWithFilter($search, $ville);
            $total = count($entreprises);
        } else {
            $entreprises = $entrepriseModel->paginate($page, ITEMS_PER_PAGE);
            $total = $entrepriseModel->count();
        }

        $totalPages = ceil($total / ITEMS_PER_PAGE);

        $this->render('entreprises/index', [
            'title' => 'Entreprises - ' . APP_NAME,
            'entreprises' => $entreprises,
            'villes' => $villes,
            'search' => $search,
            'ville' => $ville,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Affiche une entreprise
     *
     * @return void
     */
    public function show()
    {
        $id = $this->routeParams['id'] ?? 0;
        $entrepriseModel = new EntrepriseModel();
        $entreprise = $entrepriseModel->find($id);

        if (!$entreprise) {
            $_SESSION['flash_error'] = "Entreprise non trouvée.";
            $this->redirect('entreprises');
        }

        // Récupération des offres liées
        $offres = $entrepriseModel->getOffres($id);

        // Récupération des évaluations
        $evaluationModel = new Evaluation();
        $evaluations = $evaluationModel->getByEntreprise($id);
        $moyenneEvaluations = $evaluationModel->getMoyenne($id);

        $this->render('entreprises/show', [
            'title' => $entreprise['nom'] . ' - ' . APP_NAME,
            'entreprise' => $entreprise,
            'offres' => $offres,
            'evaluations' => $evaluations,
            'moyenneEvaluations' => $moyenneEvaluations
        ]);
    }

    /**
     * Formulaire de création d'entreprise
     *
     * @return void
     */
    public function create()
    {
        $this->requireRole(['admin', 'pilote']);

        $this->render('entreprises/create', [
            'title' => 'Créer une entreprise - ' . APP_NAME,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Enregistre une nouvelle entreprise
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
                $this->redirect('entreprises/create');
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
            $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
            $secteur = htmlspecialchars(trim($_POST['secteur'] ?? ''));

            if (empty($nom) || empty($email)) {
                $_SESSION['flash_error'] = "Le nom et l'email sont obligatoires.";
                $this->redirect('entreprises/create');
            }

            $entrepriseModel = new EntrepriseModel();
            $entrepriseId = $entrepriseModel->create([
                'nom' => $nom,
                'description' => $description,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'secteur' => $secteur,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($entrepriseId) {
                $_SESSION['flash_success'] = "Entreprise créée avec succès !";
                $this->redirect('entreprises/show/' . $entrepriseId);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la création.";
                $this->redirect('entreprises/create');
            }
        }
    }

    /**
     * Formulaire de modification d'entreprise
     *
     * @return void
     */
    public function edit()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $entrepriseModel = new EntrepriseModel();
        $entreprise = $entrepriseModel->find($id);

        if (!$entreprise) {
            $_SESSION['flash_error'] = "Entreprise non trouvée.";
            $this->redirect('entreprises');
        }

        $this->render('entreprises/edit', [
            'title' => 'Modifier une entreprise - ' . APP_NAME,
            'entreprise' => $entreprise,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Met à jour une entreprise
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
                $this->redirect('entreprises/edit/' . $id);
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
            $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
            $secteur = htmlspecialchars(trim($_POST['secteur'] ?? ''));

            if (empty($nom) || empty($email)) {
                $_SESSION['flash_error'] = "Le nom et l'email sont obligatoires.";
                $this->redirect('entreprises/edit/' . $id);
            }

            $entrepriseModel = new EntrepriseModel();
            $success = $entrepriseModel->update($id, [
                'nom' => $nom,
                'description' => $description,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'secteur' => $secteur,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($success) {
                $_SESSION['flash_success'] = "Entreprise mise à jour avec succès !";
                $this->redirect('entreprises/show/' . $id);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la mise à jour.";
                $this->redirect('entreprises/edit/' . $id);
            }
        }
    }

    /**
     * Supprime une entreprise
     *
     * @return void
     */
    public function delete()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $entrepriseModel = new EntrepriseModel();

        if ($entrepriseModel->delete($id)) {
            $_SESSION['flash_success'] = "Entreprise supprimée avec succès !";
        } else {
            $_SESSION['flash_error'] = "Une erreur s'est produite lors de la suppression.";
        }

        $this->redirect('entreprises');
    }

    /**
     * Évalue une entreprise
     *
     * @return void
     */
    public function evaluate()
    {
        $this->requireRole(['admin', 'pilote', 'etudiant']);

        $id = $this->routeParams['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('entreprises/show/' . $id);
            }

            $note = intval($_POST['note'] ?? 0);
            $commentaire = trim($_POST['commentaire'] ?? '');

            if ($note < 1 || $note > 5) {
                $_SESSION['flash_error'] = "La note doit être entre 1 et 5.";
                $this->redirect('entreprises/show/' . $id);
            }

            $evaluationModel = new Evaluation();
            try {
                $evaluationModel->create([
                    'entreprise_id' => $id,
                    'user_id' => $_SESSION['user_id'],
                    'note' => $note,
                    'commentaire' => $commentaire,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $_SESSION['flash_success'] = "Évaluation ajoutée avec succès !";
            } catch (\PDOException $e) {
                if ($e->getCode() == '23000') {
                    $_SESSION['flash_error'] = "Vous avez déjà évalué cette entreprise. Impossible de soumettre une nouvelle évaluation.";
                } else {
                    $_SESSION['flash_error'] = "Une erreur est survenue lors de l'enregistrement de votre avis.";
                }
            }

            $this->redirect('entreprises/show/' . $id);
        }
    }

    /**
     * Supprime une évaluation
     *
     * @return void
     */
    public function deleteEvaluation()
    {
        $this->requireRole('admin');

        $id = $this->routeParams['id'] ?? 0;
        $evaluationModel = new Evaluation();
        
        // Récupérer l'évaluation pour connaître l'entreprise ID (pour la redirection)
        $eval = $evaluationModel->find($id);
        
        if (!$eval) {
            $_SESSION['flash_error'] = "Évaluation non trouvée.";
            $this->redirect('entreprises');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check for JSON request (AJAX)
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            // Get input data (supports JSON or Form data)
            if ($isAjax && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
                $csrfToken = $input['csrf_token'] ?? '';
            } else {
                $csrfToken = $_POST['csrf_token'] ?? '';
            }
            
            if (!$this->verifyCsrfToken($csrfToken)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide.']);
                    exit;
                }
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('entreprises/show/' . $eval['entreprise_id']);
            }

            if ($evaluationModel->delete($id)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Avis supprimé avec succès.']);
                    exit;
                }
                $_SESSION['flash_success'] = "Avis supprimé avec succès.";
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de l\'avis.']);
                    exit;
                }
                $_SESSION['flash_error'] = "Erreur lors de la suppression de l'avis.";
            }
        }
        
        $this->redirect('entreprises/show/' . $eval['entreprise_id']);
    }
}
