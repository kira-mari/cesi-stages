<?php
namespace Controllers;

use Core\Controller;
use Models\Message;
use Models\User;

/**
 * Contrôleur des notifications
 */
class Notification extends Controller
{
    /**
     * Compte le nombre de notifications non lues
     */
    public function count()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['count' => 0]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $messageModel = new Message();
        
        // Les notifications sont principalement les messages non lus
        $count = $messageModel->countNonLus($userId);
        
        // Pour les admins, ajouter les demandes d'approbation en attente
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $userModel = new User();
            $count += $userModel->countPendingApprovals();
        }
        
        $this->json(['count' => $count]);
    }

    /**
     * Liste les notifications récentes
     */
    public function list()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['notifications' => []]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $messageModel = new Message();
        
        $notifications = [];
        
        // Pour les admins, ajouter les demandes d'approbation en attente
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $userModel = new User();
            $pendingApprovals = $userModel->getPendingApprovals();
            
            foreach ($pendingApprovals as $request) {
                $roleLabel = $request['role'] === 'pilote' ? 'Pilote' : 'Recruteur';
                $notifications[] = [
                    'id' => 'approval_' . $request['id'],
                    'type' => 'system',
                    'title' => 'Nouvelle demande ' . $roleLabel,
                    'preview' => $request['prenom'] . ' ' . $request['nom'] . ' souhaite s\'inscrire',
                    'lu' => false,
                    'time_ago' => $this->timeAgo($request['approval_requested_at'] ?? $request['created_at']),
                    'url' => BASE_URL . '/approbations'
                ];
            }
        }
        
        // Récupérer les messages récents (derniers 10)
        $messages = $messageModel->getRecus($userId);
        $recentMessages = array_slice($messages, 0, 10);
        
        foreach ($recentMessages as $msg) {
            $notifications[] = [
                'id' => $msg['id'],
                'type' => 'message',
                'title' => $msg['expediteur_prenom'] . ' ' . $msg['expediteur_nom'],
                'preview' => $msg['sujet'],
                'lu' => (bool) $msg['lu'],
                'time_ago' => $this->timeAgo($msg['created_at']),
                'url' => BASE_URL . '/messages/show/' . $msg['id']
            ];
        }
        
        // Limiter à 10 notifications
        $notifications = array_slice($notifications, 0, 10);
        
        $this->json(['notifications' => $notifications]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllRead()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['success' => false]);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Marquer tous les messages comme lus
        $db = \Core\Model::getDBStatic();
        $stmt = $db->prepare("UPDATE messages SET lu = 1, lu_at = NOW() WHERE destinataire_id = :user_id AND lu = 0");
        $stmt->execute([':user_id' => $userId]);
        
        $this->json(['success' => true]);
    }

    /**
     * Convertir une date en "il y a X temps"
     */
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) {
            return "À l'instant";
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return "Il y a " . $mins . " min";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Il y a " . $hours . "h";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "Il y a " . $days . "j";
        } else {
            return date('d/m/Y', $time);
        }
    }
}
