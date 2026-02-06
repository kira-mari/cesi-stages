<?php
namespace Controllers;

use Core\Controller;
use Models\User;
use Models\Candidature;
use Models\Entreprise;
use Models\Offre;
use Models\Message;

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

        $recruteurId = $this->routeParams['id'] ?? null;
        $entrepriseId = $this->routeParams['eid'] ?? null;

        if (!$recruteurId || !$entrepriseId) {
            $_SESSION['flash_error'] = "Paramètres manquants.";
            $this->redirect('dashboard');
            return;
        }

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

        // Vérifier que le recruteur a au moins une entreprise assignée
        $entreprises = $userModel->getEntreprisesByRecruteur($recruteurId);
        if (empty($entreprises)) {
            $_SESSION['flash_error'] = "Vous devez d'abord être assigné à une entreprise par un administrateur avant de pouvoir gérer les candidatures.";
            $this->redirect('recruteur/configurer-entreprise');
            return;
        }

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

        $recruteurId = $_SESSION['user_id'];
        $userModel = new User();

        // Vérifier que le recruteur a au moins une entreprise assignée
        $entreprises = $userModel->getEntreprisesByRecruteur($recruteurId);
        if (empty($entreprises)) {
            $_SESSION['flash_error'] = "Vous devez d'abord être assigné à une entreprise par un administrateur avant de pouvoir gérer les candidatures.";
            $this->redirect('recruteur/configurer-entreprise');
            return;
        }

        $id = $this->routeParams['id'] ?? null;
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

        $recruteurId = $_SESSION['user_id'];
        $userModel = new User();

        // Vérifier que le recruteur a au moins une entreprise assignée
        $entreprises = $userModel->getEntreprisesByRecruteur($recruteurId);
        if (empty($entreprises)) {
            $_SESSION['flash_error'] = "Vous devez d'abord être assigné à une entreprise par un administrateur avant de pouvoir gérer les candidatures.";
            $this->redirect('recruteur/configurer-entreprise');
            return;
        }

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
                // Demander l'association à une entreprise existante
                $entrepriseId = (int) ($_POST['entreprise_id'] ?? 0);
                
                if ($entrepriseId > 0) {
                    // Vérifier que l'entreprise existe
                    $entreprise = $entrepriseModel->find($entrepriseId);
                    if ($entreprise) {
                        // Envoyer une demande aux administrateurs
                        $this->sendAssignmentRequest($recruteurId, $entrepriseId, 'existing');
                        $_SESSION['flash_success'] = "Votre demande d'association à l'entreprise " . $entreprise['nom'] . " a été envoyée aux administrateurs.";
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
                    // L'entreprise existe, envoyer une demande d'association
                    $this->sendAssignmentRequest($recruteurId, $existante[0]['id'], 'existing');
                    $_SESSION['flash_success'] = "L'entreprise " . $nom . " existait déjà. Votre demande d'association a été envoyée aux administrateurs.";
                    $this->redirect('dashboard');
                    return;
                }

                // Envoyer une demande de création d'entreprise
                $this->sendAssignmentRequest($recruteurId, null, 'new', [
                    'nom' => $nom,
                    'adresse' => $adresse,
                    'email' => $email,
                    'telephone' => $telephone,
                    'site_web' => $site_web,
                    'description' => $description,
                    'secteur' => $secteur
                ]);
                $_SESSION['flash_success'] = "Votre demande de création de l'entreprise " . $nom . " a été envoyée aux administrateurs.";
                $this->redirect('dashboard');
                return;
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

    /**
     * Approuve une demande d'assignation d'entreprise
     */
    public function approveRequest()
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('messages');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('messages');
            return;
        }

        $messageId = (int) ($_POST['message_id'] ?? 0);
        $messageModel = new Message();
        $userModel = new User();
        $entrepriseModel = new Entreprise();

        // Récupérer le message
        $message = $messageModel->find($messageId);
        if (!$message || $message['sujet'] !== "Demande d'assignation d'entreprise") {
            $_SESSION['flash_error'] = "Message non trouvé ou invalide.";
            $this->redirect('messages');
            return;
        }

        // Parser le contenu du message pour extraire les informations
        $contenu = $message['contenu'];
        $recruteurId = $message['expediteur_id'];

        // Extraire le type de demande
        if (strpos($contenu, 'Association à une entreprise existante') !== false) {
            // Extraire l'ID de l'entreprise
            if (preg_match('/ID entreprise: (\d+)/', $contenu, $matches)) {
                $entrepriseId = (int) $matches[1];
                // Assigner l'entreprise
                if ($userModel->assignEntrepriseToRecruteur($recruteurId, $entrepriseId)) {
                    $_SESSION['flash_success'] = "Demande approuvée : Le recruteur a été assigné à l'entreprise.";
                } else {
                    $_SESSION['flash_error'] = "Erreur lors de l'assignation.";
                }
            }
        } elseif (strpos($contenu, 'Création d\'une nouvelle entreprise') !== false) {
            // Extraire les données de la nouvelle entreprise
            $nom = $this->extractFromMessage($contenu, 'Nom: ');
            $adresse = $this->extractFromMessage($contenu, 'Adresse: ');
            $email = $this->extractFromMessage($contenu, 'Email: ');
            $telephone = $this->extractFromMessage($contenu, 'Téléphone: ');
            $site_web = $this->extractFromMessage($contenu, 'Site web: ');
            $description = $this->extractFromMessage($contenu, 'Description: ');
            $secteur = $this->extractFromMessage($contenu, 'Secteur: ');

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
                $_SESSION['flash_success'] = "Demande approuvée : L'entreprise a été créée et le recruteur y a été assigné.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la création de l'entreprise.";
            }
        }

        $this->redirect('messages');
    }

    /**
     * Rejette une demande d'assignation d'entreprise
     */
    public function rejectRequest()
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('messages');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('messages');
            return;
        }

        $messageId = (int) ($_POST['message_id'] ?? 0);
        $messageModel = new Message();

        // Récupérer le message
        $message = $messageModel->find($messageId);
        if (!$message || $message['sujet'] !== "Demande d'assignation d'entreprise") {
            $_SESSION['flash_error'] = "Message non trouvé ou invalide.";
            $this->redirect('messages');
            return;
        }

        // Marquer le message comme traité (on pourrait ajouter un champ, mais pour l'instant on le laisse)
        $_SESSION['flash_success'] = "Demande rejetée.";

        $this->redirect('messages');
    }

    /**
     * Extrait une valeur d'un message
     */
    private function extractFromMessage($contenu, $label)
    {
        $lines = explode("\n", $contenu);
        foreach ($lines as $line) {
            if (strpos($line, $label) === 0) {
                return trim(substr($line, strlen($label)));
            }
        }
        return '';
    }

    /**
     * Envoie une demande d'assignation d'entreprise aux administrateurs
     */
    private function sendAssignmentRequest($recruteurId, $entrepriseId = null, $type = 'existing', $newEntrepriseData = null)
    {
        $userModel = new User();
        $messageModel = new Message();
        $entrepriseModel = new Entreprise();

        // Récupérer les informations du recruteur
        $recruteur = $userModel->find($recruteurId);
        if (!$recruteur) return;

        // Récupérer tous les administrateurs
        $admins = $userModel->getByRolePaginated('admin', 1, 100); // Récupérer jusqu'à 100 admins

        if (empty($admins)) return;

        $sujet = "Demande d'assignation d'entreprise";
        $contenu = "Le recruteur " . $recruteur['prenom'] . " " . $recruteur['nom'] . " (" . $recruteur['email'] . ") fait une demande d'assignation.\n\n";

        if ($type === 'existing') {
            $entreprise = $entrepriseModel->find($entrepriseId);
            $contenu .= "Type: Association à une entreprise existante\n";
            $contenu .= "Entreprise: " . $entreprise['nom'] . "\n";
            $contenu .= "ID entreprise: " . $entrepriseId . "\n";
        } elseif ($type === 'new') {
            $contenu .= "Type: Création d'une nouvelle entreprise\n";
            $contenu .= "Nom: " . $newEntrepriseData['nom'] . "\n";
            if (!empty($newEntrepriseData['adresse'])) $contenu .= "Adresse: " . $newEntrepriseData['adresse'] . "\n";
            if (!empty($newEntrepriseData['email'])) $contenu .= "Email: " . $newEntrepriseData['email'] . "\n";
            if (!empty($newEntrepriseData['telephone'])) $contenu .= "Téléphone: " . $newEntrepriseData['telephone'] . "\n";
            if (!empty($newEntrepriseData['site_web'])) $contenu .= "Site web: " . $newEntrepriseData['site_web'] . "\n";
            if (!empty($newEntrepriseData['description'])) $contenu .= "Description: " . $newEntrepriseData['description'] . "\n";
            if (!empty($newEntrepriseData['secteur'])) $contenu .= "Secteur: " . $newEntrepriseData['secteur'] . "\n";
        }

        $contenu .= "\nVeuillez traiter cette demande dans l'interface d'administration.";

        // Envoyer le message à tous les administrateurs
        foreach ($admins as $admin) {
            $messageModel->envoyer($recruteurId, $admin['id'], $sujet, $contenu);
        }
    }
}
