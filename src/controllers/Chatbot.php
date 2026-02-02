<?php
namespace Controllers;

use Core\Controller;
use Core\Model;
use PDO;

/**
 * Contrôleur du chatbot (API JSON)
 */
class Chatbot extends Controller
{
    /**
     * Point d'entrée JSON pour le chatbot
     *
     * URL : /chatbot/ask
     * Méthode : POST
     * Corps JSON attendu : { "message": "..." }
     */
    public function ask()
    {
        // Forcer JSON pour toutes les réponses
        header('Content-Type: application/json');

        // Limiter aux requêtes AJAX / POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'error' => 'Méthode non autorisée',
            ]);
            return;
        }

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data) || !isset($data['message'])) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Requête invalide',
            ]);
            return;
        }

        $message = trim((string) $data['message']);

        if ($message === '') {
            http_response_code(400);
            echo json_encode([
                'error' => 'Le message ne peut pas être vide.',
            ]);
            return;
        }

        // Récupération d'infos de contexte simples (si l'utilisateur est connecté)
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $userRole = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : null;

        // Génération d'une réponse simple côté serveur (placeholder)
        $answer = $this->buildSimpleAnswer($message, $userRole);

        // Log de l'interaction dans la base (si possible, mais sans bloquer la réponse)
        try {
            $this->storeInteraction($userId, $message, $answer);
        } catch (\Throwable $e) {
            // On ignore l'erreur de log pour ne pas casser l'UX
        }

        echo json_encode([
            'answer' => $answer,
        ]);
    }

    /**
     * Construit une réponse simple en fonction du message
     *
     * @param string $message
     * @param string|null $userRole
     * @return string
     */
    protected function buildSimpleAnswer(string $message, ?string $userRole): string
    {
        $lower = mb_strtolower($message, 'UTF-8');

        if (strpos($lower, 'bonjour') !== false || strpos($lower, 'salut') !== false) {
            return "Bonjour ! Comment puis-je vous aider avec les stages ou la plateforme CesiStages ?";
        }

        if (strpos($lower, 'offre') !== false || strpos($lower, 'stage') !== false) {
            return "Pour consulter les offres de stage, rendez-vous dans le menu « Offres ».\n"
                . "Vous pourrez filtrer par localisation, compétences ou entreprise.";
        }

        if (strpos($lower, 'candidature') !== false) {
            if ($userRole === 'etudiant') {
                return "Vous pouvez suivre vos candidatures depuis la page « Mes candidatures ».\n"
                    . "Chaque statut (en attente, acceptée, refusée) est mis à jour par les pilotes.";
            }

            return "La gestion des candidatures se fait via le module « Candidatures ».\n"
                . "Les étudiants voient leurs candidatures, les pilotes peuvent les suivre et les mettre à jour.";
        }

        if (strpos($lower, 'contact') !== false || strpos($lower, 'support') !== false) {
            return "Pour tout problème technique, rapprochez-vous de votre pilote ou de l'administrateur de la plateforme.";
        }

        // Réponse par défaut
        return "Je suis un assistant de base intégré à CesiStages.\n"
            . "Je peux vous orienter vers les pages : offres, candidatures, entreprises et tableau de bord.\n"
            . "Reformulez votre question en mentionnant ce que vous cherchez (ex : « trouver une offre », « suivre ma candidature »).";
    }

    /**
     * Enregistre l'interaction dans la table chatbot_interactions
     *
     * @param int|null $userId
     * @param string $question
     * @param string $answer
     * @return void
     */
    protected function storeInteraction(?int $userId, string $question, string $answer): void
    {
        // Utilisation de Core\Model pour récupérer la connexion PDO
        $db = Model::getDB();

        $stmt = $db->prepare(
            "INSERT INTO chatbot_interactions (user_id, question, answer, created_at)
             VALUES (:user_id, :question, :answer, NOW())"
        );

        $stmt->bindValue(':user_id', $userId, $userId !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':question', $question, PDO::PARAM_STR);
        $stmt->bindValue(':answer', $answer, PDO::PARAM_STR);

        $stmt->execute();
    }
}

