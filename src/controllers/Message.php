<?php
namespace Controllers;

use Core\Controller;
use Models\Message as MessageModel;
use Models\User;

/**
 * Contrôleur des messages internes
 */
class Message extends Controller
{
    /**
     * Boîte de réception
     */
    public function index()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        $messageModel = new MessageModel();
        $userId = $_SESSION['user_id'];

        $messagesRecus = $messageModel->getRecus($userId);
        $nbNonLus = $messageModel->countNonLus($userId);

        $this->render('messages/index', [
            'title' => 'Messagerie - ' . APP_NAME,
            'messages' => $messagesRecus,
            'nbNonLus' => $nbNonLus,
            'type' => 'recus'
        ]);
    }

    /**
     * Messages envoyés
     */
    public function envoyes()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        $messageModel = new MessageModel();
        $userId = $_SESSION['user_id'];

        $messagesEnvoyes = $messageModel->getEnvoyes($userId);

        $this->render('messages/index', [
            'title' => 'Messages envoyés - ' . APP_NAME,
            'messages' => $messagesEnvoyes,
            'nbNonLus' => $messageModel->countNonLus($userId),
            'type' => 'envoyes'
        ]);
    }

    /**
     * Affiche un message
     */
    public function show()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        $id = $this->routeParams['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $messageModel = new MessageModel();

        // Vérifier l'accès
        if (!$messageModel->peutAcceder($id, $userId)) {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à ce message.";
            $this->redirect('messages');
        }

        $message = $messageModel->getWithDetails($id);

        if (!$message) {
            $_SESSION['flash_error'] = "Message non trouvé.";
            $this->redirect('messages');
        }

        // Marquer comme lu si c'est le destinataire
        if ($message['destinataire_id'] == $userId && !$message['lu']) {
            $messageModel->marquerLu($id);
            $message['lu'] = 1;
        }

        $this->render('messages/show', [
            'title' => $message['sujet'] . ' - ' . APP_NAME,
            'message' => $message,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Formulaire nouveau message
     */
    public function nouveau()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        $userModel = new User();
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        // Destinataire pré-sélectionné ?
        $destinataireId = $_GET['destinataire'] ?? null;
        $destinataire = null;
        if ($destinataireId) {
            $destinataire = $userModel->find($destinataireId);
        }

        // Réponse à un message ?
        $replyTo = $_GET['reply'] ?? null;
        $messageReponse = null;
        if ($replyTo) {
            $messageModel = new MessageModel();
            $messageReponse = $messageModel->getWithDetails($replyTo);
        }

        // Liste des destinataires possibles selon le rôle
        $destinataires = [];
        if ($userRole === 'admin') {
            // Admin peut contacter tout le monde
            $destinataires = $userModel->all();
        } elseif ($userRole === 'pilote') {
            // Pilote peut contacter ses étudiants
            $destinataires = $userModel->getEtudiantsByPilote($userId);
        } elseif ($userRole === 'recruteur') {
            // Recruteur peut contacter les étudiants qui ont candidaté
            $destinataires = $this->getEtudiantsCandidatsRecruteur($userId);
        } elseif ($userRole === 'etudiant') {
            // Étudiant peut répondre aux messages reçus (pilotes, recruteurs, admins)
            $destinataires = $this->getContactsEtudiant($userId);
        }

        // Exclure soi-même
        $destinataires = array_filter($destinataires, function($d) use ($userId) {
            return $d['id'] != $userId;
        });

        $this->render('messages/nouveau', [
            'title' => 'Nouveau message - ' . APP_NAME,
            'destinataires' => $destinataires,
            'destinataire' => $destinataire,
            'messageReponse' => $messageReponse,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Envoie un message
     */
    public function envoyer()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('messages/nouveau');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('messages/nouveau');
        }

        $destinataireId = $_POST['destinataire_id'] ?? null;
        $sujet = trim($_POST['sujet'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        // Validation
        if (!$destinataireId || empty($sujet) || empty($contenu)) {
            $_SESSION['flash_error'] = "Tous les champs sont obligatoires.";
            $this->redirect('messages/nouveau');
        }

        // Vérifier que le destinataire existe
        $userModel = new User();
        $destinataire = $userModel->find($destinataireId);
        if (!$destinataire) {
            $_SESSION['flash_error'] = "Destinataire invalide.";
            $this->redirect('messages/nouveau');
        }

        $messageModel = new MessageModel();
        $result = $messageModel->envoyer(
            $_SESSION['user_id'],
            $destinataireId,
            htmlspecialchars($sujet),
            htmlspecialchars($contenu)
        );

        if ($result) {
            $_SESSION['flash_success'] = "Message envoyé à " . $destinataire['prenom'] . " " . $destinataire['nom'] . ".";
            $this->redirect('messages/envoyes');
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi du message.";
            $this->redirect('messages/nouveau');
        }
    }

    /**
     * Supprime un message
     */
    public function supprimer()
    {
        $this->requireRole(['admin', 'pilote', 'recruteur', 'etudiant']);

        $id = $this->routeParams['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $messageModel = new MessageModel();

        if (!$messageModel->peutAcceder($id, $userId)) {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à ce message.";
            $this->redirect('messages');
        }

        if ($messageModel->supprimer($id)) {
            $_SESSION['flash_success'] = "Message supprimé.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la suppression.";
        }

        $this->redirect('messages');
    }

    /**
     * Récupère les étudiants qui ont candidaté chez le recruteur
     */
    private function getEtudiantsCandidatsRecruteur($recruteurId)
    {
        $db = \Core\Model::getDBStatic();
        $stmt = $db->prepare(
            "SELECT DISTINCT u.* FROM users u
             JOIN candidatures c ON u.id = c.etudiant_id
             JOIN offres o ON c.offre_id = o.id
             JOIN recruteur_entreprise re ON o.entreprise_id = re.entreprise_id
             WHERE re.recruteur_id = :recruteur_id AND u.role = 'etudiant'
             ORDER BY u.nom, u.prenom"
        );
        $stmt->execute([':recruteur_id' => $recruteurId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les contacts d'un étudiant :
     * - Son pilote
     * - Les recruteurs des entreprises où il a candidaté
     * - Les personnes avec qui il a déjà échangé
     */
    private function getContactsEtudiant($etudiantId)
    {
        $db = \Core\Model::getDBStatic();
        $stmt = $db->prepare(
            "SELECT DISTINCT u.* FROM users u
             WHERE u.id IN (
                 -- Son pilote
                 SELECT pilote_id FROM pilote_etudiant WHERE etudiant_id = :etudiant_id
                 UNION
                 -- Recruteurs des entreprises où il a postulé
                 SELECT re.recruteur_id FROM recruteur_entreprise re
                 JOIN offres o ON o.entreprise_id = re.entreprise_id
                 JOIN candidatures c ON c.offre_id = o.id
                 WHERE c.etudiant_id = :etudiant_id
                 UNION
                 -- Personnes qui lui ont envoyé des messages
                 SELECT DISTINCT expediteur_id FROM messages WHERE destinataire_id = :etudiant_id
                 UNION
                 -- Personnes à qui il a envoyé des messages
                 SELECT DISTINCT destinataire_id FROM messages WHERE expediteur_id = :etudiant_id
             )
             ORDER BY u.role, u.nom, u.prenom"
        );
        $stmt->execute([':etudiant_id' => $etudiantId]);
        return $stmt->fetchAll();
    }

    /**
     * API: Nombre de messages non lus
     */
    public function countNonLus()
    {
        if (!$this->isAuthenticated()) {
            $this->json(['count' => 0]);
        }

        $messageModel = new MessageModel();
        $count = $messageModel->countNonLus($_SESSION['user_id']);
        
        $this->json(['count' => $count]);
    }
}
