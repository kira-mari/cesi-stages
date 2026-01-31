<?php
namespace Controllers;

use Core\Controller;
use Models\User;
use Models\Candidature;

/**
 * Contrôleur des étudiants
 */
class Etudiant extends Controller
{
    /**
     * Liste des étudiants
     *
     * @return void
     */
    public function index()
    {
        $this->requireRole(['admin', 'pilote']);

        $userModel = new User();
        $search = $_GET['search'] ?? '';
        $page = intval($_GET['page'] ?? 1);

        if (!empty($search)) {
            $etudiants = $userModel->searchByRole('etudiant', $search);
            $total = count($etudiants);
        } else {
            $etudiants = $userModel->getByRolePaginated('etudiant', $page, ITEMS_PER_PAGE);
            $total = $userModel->countByRole('etudiant');
        }

        $totalPages = ceil($total / ITEMS_PER_PAGE);

        $this->render('etudiants/index', [
            'title' => 'Étudiants - ' . APP_NAME,
            'etudiants' => $etudiants,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Affiche un étudiant
     *
     * @return void
     */
    public function show()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();
        $etudiant = $userModel->find($id);

        if (!$etudiant || $etudiant['role'] !== 'etudiant') {
            $_SESSION['flash_error'] = "Étudiant non trouvé.";
            $this->redirect('etudiants');
        }

        // Récupération des candidatures
        $candidatureModel = new Candidature();
        $candidatures = $candidatureModel->getByEtudiant($id);

        $this->render('etudiants/show', [
            'title' => $etudiant['prenom'] . ' ' . $etudiant['nom'] . ' - ' . APP_NAME,
            'etudiant' => $etudiant,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * Formulaire de création d'étudiant
     *
     * @return void
     */
    public function create()
    {
        $this->requireRole(['admin', 'pilote']);

        $this->render('etudiants/create', [
            'title' => 'Créer un compte étudiant - ' . APP_NAME,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Enregistre un nouvel étudiant
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
                $this->redirect('etudiants/create');
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('etudiants/create');
            }

            if (strlen($password) < 8) {
                $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
                $this->redirect('etudiants/create');
            }

            $userModel = new User();

            if ($userModel->findByEmail($email)) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
                $this->redirect('etudiants/create');
            }

            $userId = $userModel->create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'etudiant',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($userId) {
                $_SESSION['flash_success'] = "Étudiant créé avec succès !";
                $this->redirect('etudiants/show/' . $userId);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la création.";
                $this->redirect('etudiants/create');
            }
        }
    }

    /**
     * Formulaire de modification d'étudiant
     *
     * @return void
     */
    public function edit()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();
        $etudiant = $userModel->find($id);

        if (!$etudiant || $etudiant['role'] !== 'etudiant') {
            $_SESSION['flash_error'] = "Étudiant non trouvé.";
            $this->redirect('etudiants');
        }

        $this->render('etudiants/edit', [
            'title' => 'Modifier un étudiant - ' . APP_NAME,
            'etudiant' => $etudiant,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Met à jour un étudiant
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
                $this->redirect('etudiants/edit/' . $id);
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($nom) || empty($prenom) || empty($email)) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('etudiants/edit/' . $id);
            }

            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);

            if ($existingUser && $existingUser['id'] != $id) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
                $this->redirect('etudiants/edit/' . $id);
            }

            $data = [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Mise à jour du mot de passe si fourni
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 8) {
                    $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
                    $this->redirect('etudiants/edit/' . $id);
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            $success = $userModel->update($id, $data);

            if ($success) {
                $_SESSION['flash_success'] = "Étudiant mis à jour avec succès !";
                $this->redirect('etudiants/show/' . $id);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la mise à jour.";
                $this->redirect('etudiants/edit/' . $id);
            }
        }
    }

    /**
     * Supprime un étudiant
     *
     * @return void
     */
    public function delete()
    {
        $this->requireRole(['admin', 'pilote']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();

        $etudiant = $userModel->find($id);
        if (!$etudiant || $etudiant['role'] !== 'etudiant') {
            $_SESSION['flash_error'] = "Étudiant non trouvé.";
            $this->redirect('etudiants');
        }

        if ($userModel->delete($id)) {
            $_SESSION['flash_success'] = "Étudiant supprimé avec succès !";
        } else {
            $_SESSION['flash_error'] = "Une erreur s'est produite lors de la suppression.";
        }

        $this->redirect('etudiants');
    }
}
