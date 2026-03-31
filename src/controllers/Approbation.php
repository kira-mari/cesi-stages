<?php
namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Contrôleur pour gérer les approbations de comptes
 */
class Approbation extends Controller
{
    /**
     * Liste des demandes d'approbation en attente
     */
    public function index()
    {
        $this->requireRole(['admin']);
        
        $userModel = new User();
        
        // Récupérer les demandes en attente
        $pendingRequests = $userModel->getPendingApprovals();
        
        // Statistiques
        $stats = [
            'pending' => count($pendingRequests),
            'approved_today' => $userModel->countApprovedToday(),
            'total_approved' => $userModel->countTotalApproved()
        ];
        
        $this->render('approbations/index', [
            'title' => 'Demandes d\'approbation - ' . APP_NAME,
            'requests' => $pendingRequests,
            'stats' => $stats,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Approuver une demande
     */
    public function approve()
    {
        $this->requireRole(['admin']);
        
        $id = $this->routeParams['id'] ?? null;
        
        if (!$id) {
            $_SESSION['flash_error'] = "ID utilisateur manquant.";
            $this->redirect('approbations');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('approbations');
            return;
        }
        
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('approbations');
            return;
        }
        
        $userModel = new User();
        $user = $userModel->find($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = "Utilisateur non trouvé.";
            $this->redirect('approbations');
            return;
        }
        
        // Approuver le compte
        $userModel->update($id, [
            'is_approved' => 1,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $_SESSION['user_id']
        ]);
        
        // Notifier l'utilisateur
        $this->notifyUserApproval($user, true);
        
        $_SESSION['flash_success'] = "Le compte de " . $user['prenom'] . " " . $user['nom'] . " a été approuvé.";
        $this->redirect('approbations');
    }
    
    /**
     * Refuser une demande
     */
    public function reject()
    {
        $this->requireRole(['admin']);
        
        $id = $this->routeParams['id'] ?? null;
        
        if (!$id) {
            $_SESSION['flash_error'] = "ID utilisateur manquant.";
            $this->redirect('approbations');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('approbations');
            return;
        }
        
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('approbations');
            return;
        }
        
        $userModel = new User();
        $user = $userModel->find($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = "Utilisateur non trouvé.";
            $this->redirect('approbations');
            return;
        }
        
        $reason = $_POST['reason'] ?? '';
        
        // Notifier l'utilisateur du refus
        $this->notifyUserApproval($user, false, $reason);
        
        // Supprimer le compte ou le mettre en mode étudiant
        $userModel->update($id, [
            'role' => 'etudiant', // Rétrograde en étudiant
            'is_approved' => null // Plus besoin d'approbation
        ]);
        
        $_SESSION['flash_success'] = "La demande de " . $user['prenom'] . " " . $user['nom'] . " a été refusée.";
        $this->redirect('approbations');
    }
    
    /**
     * Notifier l'utilisateur de la décision
     */
    private function notifyUserApproval($user, $approved, $reason = '')
    {
        $messageModel = new \Models\Message();
        
        $roleLabel = $user['role'] === 'pilote' ? 'Pilote' : 'Recruteur';
        
        if ($approved) {
            $sujet = "Votre compte $roleLabel a été approuvé";
            $contenu = "Bonjour " . $user['prenom'] . ",\n\n";
            $contenu .= "Bonne nouvelle ! Votre demande de compte $roleLabel a été approuvée par notre équipe.\n\n";
            $contenu .= "Veuillez vous déconnecter et vous reconnecter pour accéder à toutes les fonctionnalités de votre compte.\n\n";
            $contenu .= "Bienvenue sur CesiStages !\n\n";
            $contenu .= "Cordialement,\nL'équipe CesiStages";
        } else {
            $sujet = "Votre demande de compte $roleLabel";
            $contenu = "Bonjour " . $user['prenom'] . ",\n\n";
            $contenu .= "Nous avons examiné votre demande de compte $roleLabel.\n\n";
            $contenu .= "Malheureusement, votre demande n'a pas pu être approuvée.\n\n";
            if (!empty($reason)) {
                $contenu .= "Raison : $reason\n\n";
            }
            $contenu .= "Votre compte reste actif en tant qu'étudiant. Si vous pensez qu'il s'agit d'une erreur, n'hésitez pas à nous contacter.\n\n";
            $contenu .= "Cordialement,\nL'équipe CesiStages";
        }
        
        // Envoyer un message système (expéditeur = admin connecté)
        $messageModel->envoyer(
            $_SESSION['user_id'],
            $user['id'],
            $sujet,
            $contenu
        );
    }
}
