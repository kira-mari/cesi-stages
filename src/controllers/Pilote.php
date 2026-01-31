<?php
namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Contrôleur des pilotes de promotion
 */
class Pilote extends Controller
{
    /**
     * Liste des pilotes
     *
     * @return void
     */
    public function index()
    {
        $this->requireRole(['admin']);

        $userModel = new User();
        $search = $_GET['search'] ?? '';
        $page = intval($_GET['page'] ?? 1);

        if (!empty($search)) {
            $pilotes = $userModel->searchByRole('pilote', $search);
            $total = count($pilotes);
        } else {
            $pilotes = $userModel->getByRolePaginated('pilote', $page, ITEMS_PER_PAGE);
            $total = $userModel->countByRole('pilote');
        }

        $totalPages = ceil($total / ITEMS_PER_PAGE);

        $this->render('pilotes/index', [
            'title' => 'Pilotes de promotion - ' . APP_NAME,
            'pilotes' => $pilotes,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Affiche un pilote
     *
     * @return void
     */
    public function show()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();
        $pilote = $userModel->find($id);

        if (!$pilote || $pilote['role'] !== 'pilote') {
            $_SESSION['flash_error'] = "Pilote non trouvé.";
            $this->redirect('pilotes');
        }

        // Récupération des étudiants gérés par ce pilote (si relation définie)
        $etudiants = $userModel->getEtudiantsByPilote($id);

        $this->render('pilotes/show', [
            'title' => $pilote['prenom'] . ' ' . $pilote['nom'] . ' - ' . APP_NAME,
            'pilote' => $pilote,
            'etudiants' => $etudiants
        ]);
    }

    /**
     * Formulaire de création de pilote
     *
     * @return void
     */
    public function create()
    {
        $this->requireRole(['admin']);

        $this->render('pilotes/create', [
            'title' => 'Créer un compte pilote - ' . APP_NAME,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Enregistre un nouveau pilote
     *
     * @return void
     */
    public function store()
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('pilotes/create');
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('pilotes/create');
            }

            if (strlen($password) < 8) {
                $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
                $this->redirect('pilotes/create');
            }

            $userModel = new User();

            if ($userModel->findByEmail($email)) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
                $this->redirect('pilotes/create');
            }

            $userId = $userModel->create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'pilote',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($userId) {
                $_SESSION['flash_success'] = "Pilote créé avec succès !";
                $this->redirect('pilotes/show/' . $userId);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la création.";
                $this->redirect('pilotes/create');
            }
        }
    }

    /**
     * Formulaire de modification de pilote
     *
     * @return void
     */
    public function edit()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();
        $pilote = $userModel->find($id);

        if (!$pilote || $pilote['role'] !== 'pilote') {
            $_SESSION['flash_error'] = "Pilote non trouvé.";
            $this->redirect('pilotes');
        }

        $this->render('pilotes/edit', [
            'title' => 'Modifier un pilote - ' . APP_NAME,
            'pilote' => $pilote,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Met à jour un pilote
     *
     * @return void
     */
    public function update()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $_SESSION['flash_error'] = "Token de sécurité invalide.";
                $this->redirect('pilotes/edit/' . $id);
            }

            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($nom) || empty($prenom) || empty($email)) {
                $_SESSION['flash_error'] = "Veuillez remplir tous les champs obligatoires.";
                $this->redirect('pilotes/edit/' . $id);
            }

            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);

            if ($existingUser && $existingUser['id'] != $id) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
                $this->redirect('pilotes/edit/' . $id);
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
                    $this->redirect('pilotes/edit/' . $id);
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            $success = $userModel->update($id, $data);

            if ($success) {
                $_SESSION['flash_success'] = "Pilote mis à jour avec succès !";
                $this->redirect('pilotes/show/' . $id);
            } else {
                $_SESSION['flash_error'] = "Une erreur s'est produite lors de la mise à jour.";
                $this->redirect('pilotes/edit/' . $id);
            }
        }
    }

    /**
     * Supprime un pilote
     *
     * @return void
     */
    public function delete()
    {
        $this->requireRole(['admin']);

        $id = $this->routeParams['id'] ?? 0;
        $userModel = new User();

        $pilote = $userModel->find($id);
        if (!$pilote || $pilote['role'] !== 'pilote') {
            $_SESSION['flash_error'] = "Pilote non trouvé.";
            $this->redirect('pilotes');
        }

        if ($userModel->delete($id)) {
            $_SESSION['flash_success'] = "Pilote supprimé avec succès !";
        } else {
            $_SESSION['flash_error'] = "Une erreur s'est produite lors de la suppression.";
        }

        $this->redirect('pilotes');
    }
}
