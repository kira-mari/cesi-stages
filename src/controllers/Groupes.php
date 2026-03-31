<?php
namespace Controllers;

use Core\Controller;
use Models\Groupe;
use Models\User;

/**
 * Contrôleur des groupes d'étudiants (pilotes uniquement)
 */
class Groupes extends Controller
{
    private function getPiloteId(): int
    {
        $this->requireRole(['pilote']);
        if (isset($_SESSION['user_is_approved']) && $_SESSION['user_is_approved'] === false) {
            $this->redirect('dashboard');
            exit;
        }
        return (int) $_SESSION['user_id'];
    }

    /**
     * Liste des groupes
     */
    public function index()
    {
        $piloteId = $this->getPiloteId();
        $model = new Groupe();
        $groupes = $model->getByPilote($piloteId);

        $this->render('groupes/index', [
            'title' => 'Mes groupes - ' . APP_NAME,
            'groupes' => $groupes
        ]);
    }

    /**
     * Créer un groupe
     */
    public function create()
    {
        $piloteId = $this->getPiloteId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            if (empty($nom)) {
                $_SESSION['flash_error'] = 'Le nom du groupe est requis.';
                $this->redirect('groupes/create');
                return;
            }

            $model = new Groupe();
            if ($model->createGroupe($piloteId, $nom)) {
                $_SESSION['flash_success'] = 'Groupe créé avec succès.';
                $this->redirect('groupes');
            } else {
                $_SESSION['flash_error'] = 'Erreur lors de la création du groupe.';
                $this->redirect('groupes/create');
            }
            return;
        }

        $this->render('groupes/create', [
            'title' => 'Créer un groupe - ' . APP_NAME
        ]);
    }

    /**
     * Modifier un groupe (renommer)
     */
    public function edit()
    {
        $piloteId = $this->getPiloteId();
        $id = (int) ($this->routeParams['id'] ?? 0);

        $model = new Groupe();
        if (!$model->belongsToPilote($id, $piloteId)) {
            $_SESSION['flash_error'] = 'Groupe non trouvé.';
            $this->redirect('groupes');
            return;
        }

        $groupe = $model->find($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            if (empty($nom)) {
                $_SESSION['flash_error'] = 'Le nom du groupe est requis.';
                $this->redirect('groupes/edit/' . $id);
                return;
            }

            if ($model->updateNom($id, $nom)) {
                $_SESSION['flash_success'] = 'Groupe renommé avec succès.';
                $this->redirect('groupes/show/' . $id);
            } else {
                $_SESSION['flash_error'] = 'Erreur lors de la modification.';
            }
            return;
        }

        $this->render('groupes/edit', [
            'title' => 'Modifier le groupe - ' . APP_NAME,
            'groupe' => $groupe
        ]);
    }

    /**
     * Voir un groupe et gérer les membres
     */
    public function show()
    {
        $piloteId = $this->getPiloteId();
        $id = (int) ($this->routeParams['id'] ?? 0);

        $model = new Groupe();
        if (!$model->belongsToPilote($id, $piloteId)) {
            $_SESSION['flash_error'] = 'Groupe non trouvé.';
            $this->redirect('groupes');
            return;
        }

        $groupe = $model->find($id);
        $etudiants = $model->getEtudiants($id);

        $userModel = new User();
        $tousEtudiants = $userModel->getEtudiantsByPilote($piloteId);
        $idsEnGroupe = array_column($etudiants, 'id');
        $disponibles = array_filter($tousEtudiants, fn($e) => !in_array($e['id'], $idsEnGroupe));

        $this->render('groupes/show', [
            'title' => $groupe['nom'] . ' - ' . APP_NAME,
            'groupe' => $groupe,
            'etudiants' => $etudiants,
            'disponibles' => array_values($disponibles)
        ]);
    }

    /**
     * Supprimer un groupe
     */
    public function delete()
    {
        $piloteId = $this->getPiloteId();
        $id = (int) ($this->routeParams['id'] ?? 0);

        $model = new Groupe();
        if (!$model->belongsToPilote($id, $piloteId)) {
            $_SESSION['flash_error'] = 'Groupe non trouvé.';
        } elseif ($model->deleteGroupe($id)) {
            $_SESSION['flash_success'] = 'Groupe supprimé.';
        } else {
            $_SESSION['flash_error'] = 'Erreur lors de la suppression.';
        }
        $this->redirect('groupes');
    }

    /**
     * Ajouter un étudiant au groupe
     */
    public function addEtudiant()
    {
        $piloteId = $this->getPiloteId();
        $groupeId = (int) ($this->routeParams['id'] ?? 0);
        $etudiantId = (int) ($_POST['etudiant_id'] ?? $_GET['etudiant_id'] ?? 0);

        $model = new Groupe();
        if (!$model->belongsToPilote($groupeId, $piloteId)) {
            $_SESSION['flash_error'] = 'Groupe non trouvé.';
            $this->redirect('groupes');
            return;
        }
        if (!$model->etudiantAssignedToPilote($etudiantId, $piloteId)) {
            $_SESSION['flash_error'] = 'Cet étudiant ne vous est pas assigné.';
            $this->redirect('groupes/show/' . $groupeId);
            return;
        }

        if ($model->addEtudiant($groupeId, $etudiantId, $piloteId)) {
            $_SESSION['flash_success'] = 'Étudiant ajouté au groupe.';
        } else {
            $_SESSION['flash_error'] = 'Erreur lors de l\'ajout.';
        }
        $this->redirect('groupes/show/' . $groupeId);
    }

    /**
     * API: Déplacer un étudiant vers un groupe (AJAX drag & drop)
     */
    public function moveEtudiant()
    {
        header('Content-Type: application/json');
        $piloteId = (int) ($_SESSION['user_id'] ?? 0);
        if ($_SESSION['user_role'] !== 'pilote') {
            echo json_encode(['success' => false, 'error' => 'Non autorisé']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $etudiantId = (int) ($data['etudiant_id'] ?? 0);
        $groupeId = (int) ($data['groupe_id'] ?? 0); // 0 = retirer du groupe

        $model = new Groupe();

        if (!$model->etudiantAssignedToPilote($etudiantId, $piloteId)) {
            echo json_encode(['success' => false, 'error' => 'Étudiant non assigné']);
            exit;
        }

        if ($groupeId === 0) {
            // Retirer de tous les groupes du pilote
            $groupes = $model->getByPilote($piloteId);
            foreach ($groupes as $g) {
                $model->removeEtudiant($g['id'], $etudiantId);
            }
            echo json_encode(['success' => true]);
            exit;
        }

        if (!$model->belongsToPilote($groupeId, $piloteId)) {
            echo json_encode(['success' => false, 'error' => 'Groupe non trouvé']);
            exit;
        }

        $model->addEtudiant($groupeId, $etudiantId, $piloteId);
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Retirer un étudiant du groupe
     */
    public function removeEtudiant()
    {
        $piloteId = $this->getPiloteId();
        $groupeId = (int) ($this->routeParams['id'] ?? 0);
        $etudiantId = (int) ($this->routeParams['eid'] ?? 0);

        $model = new Groupe();
        if (!$model->belongsToPilote($groupeId, $piloteId)) {
            $_SESSION['flash_error'] = 'Groupe non trouvé.';
        } elseif ($model->removeEtudiant($groupeId, $etudiantId)) {
            $_SESSION['flash_success'] = 'Étudiant retiré du groupe.';
        } else {
            $_SESSION['flash_error'] = 'Erreur lors du retrait.';
        }
        $this->redirect('groupes/show/' . $groupeId);
    }
}
