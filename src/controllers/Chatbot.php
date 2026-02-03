<?php
namespace Controllers;

use Core\Controller;
use Core\Model;
use PDO;
use Models\Offre as OffreModel;
use Models\Entreprise;
use Models\Candidature;
use Models\User;
use Models\Wishlist;

/**
 * Contrôleur du chatbot (API JSON)
 * Entraîné pour répondre selon le rôle : admin, pilote, etudiant, ou visiteur non connecté
 */
class Chatbot extends Controller
{
    /**
     * Point d'entrée JSON pour le chatbot
     */
    public function ask()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data) || !isset($data['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Requête invalide']);
            return;
        }

        $message = trim((string) $data['message']);

        if ($message === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Le message ne peut pas être vide.']);
            return;
        }

        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $userRole = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : null;

        $responseData = $this->buildSmartAnswer($message, $userId, $userRole);

        // Log de l'interaction
        try {
            $this->storeInteraction($userId, $message, $responseData['answer'], $responseData['needs_admin']);
        } catch (\Throwable $e) {
            // Ignore
        }

        echo json_encode([
            'answer' => $responseData['answer'],
            'needs_admin' => $responseData['needs_admin'] ?? false,
            'offers' => $responseData['offers'] ?? [],
            'etudiants' => $responseData['etudiants'] ?? [],
            'etudiants_table' => $responseData['etudiants_table'] ?? [],
        ]);
    }

    /**
     * Construit une réponse intelligente basée sur le message et le rôle
     */
    protected function buildSmartAnswer(string $message, ?int $userId, ?string $userRole): array
    {
        $lower = mb_strtolower($message, 'UTF-8');

        // ========================================
        // 1) SALUTATIONS
        // ========================================
        if ($this->matchKeywords($lower, ['bonjour', 'salut', 'hello', 'hi', 'coucou', 'hey', 'bonsoir'])) {
            $greeting = $this->getGreeting($userRole);
            return ['answer' => $greeting, 'needs_admin' => false];
        }

        // ========================================
        // 2) QUI ES-TU / AIDE
        // ========================================
        if ($this->matchKeywords($lower, ['qui es-tu', 'qui es tu', 'tu es qui', 'c\'est quoi', 'aide', 'help', 'comment ça marche', 'comment ca marche', 'que peux-tu faire', 'que peux tu faire'])) {
            return ['answer' => $this->getHelpMessage($userRole), 'needs_admin' => false];
        }

        // ========================================
        // 3) QUESTIONS ADMIN UNIQUEMENT
        // ========================================
        if ($userRole === 'admin') {
            $adminResponse = $this->handleAdminQuestions($lower, $message);
            if ($adminResponse !== null) {
                return $adminResponse;
            }
        }

        // ========================================
        // 4) QUESTIONS PILOTE UNIQUEMENT
        // ========================================
        if ($userRole === 'pilote') {
            $piloteResponse = $this->handlePiloteQuestions($lower, $message, $userId);
            if ($piloteResponse !== null) {
                return $piloteResponse;
            }
        }

        // ========================================
        // 5) QUESTIONS ETUDIANT UNIQUEMENT
        // ========================================
        if ($userRole === 'etudiant') {
            $etudiantResponse = $this->handleEtudiantQuestions($lower, $message, $userId);
            if ($etudiantResponse !== null) {
                return $etudiantResponse;
            }
        }

        // ========================================
        // 6) QUESTIONS GÉNÉRALES (tous les utilisateurs)
        // ========================================

        // Recherche par durée
        $duree = $this->detectDureeInMessage($lower);
        if ($duree !== null) {
            return $this->searchOffersByDuree($duree);
        }

        // Recherche par ville
        $ville = $this->detectVilleInMessage($lower);
        if ($ville !== null) {
            return $this->searchOffersByVille($ville);
        }

        // Recherche par compétence
        $competence = $this->detectCompetenceInMessage($lower);
        if ($competence !== null) {
            return $this->searchOffersByCompetence($competence);
        }

        // Recherche par entreprise
        $entreprise = $this->detectEntrepriseInMessage($lower);
        if ($entreprise !== null) {
            return $this->searchOffersByEntreprise($entreprise);
        }

        // Questions sur les offres/stages en général
        if ($this->matchKeywords($lower, ['offre', 'stage', 'recherche stage', 'trouver stage', 'chercher stage'])) {
            return $this->getOffersHelp($userRole);
        }

        // Questions sur les candidatures
        if ($this->matchKeywords($lower, ['candidature', 'postuler', 'candidater', 'comment postuler', 'envoyer cv'])) {
            return $this->getCandidatureHelp($userRole);
        }

        // Questions sur les entreprises
        if ($this->matchKeywords($lower, ['entreprise', 'société', 'societe', 'employeur'])) {
            return $this->getEntreprisesHelp($userRole);
        }

        // Questions sur la wishlist
        if ($this->matchKeywords($lower, ['wishlist', 'favoris', 'liste de souhaits', 'sauvegarder offre'])) {
            return $this->getWishlistHelp($userRole);
        }

        // Questions sur l'inscription/connexion
        if ($this->matchKeywords($lower, ['inscription', 'inscrire', 's\'inscrire', 'créer compte', 'creer compte', 'register'])) {
            return $this->getRegisterHelp($userRole);
        }

        if ($this->matchKeywords($lower, ['connexion', 'connecter', 'se connecter', 'login', 'mot de passe', 'mdp'])) {
            return $this->getLoginHelp($userRole);
        }

        // Questions sur le contact/support
        if ($this->matchKeywords($lower, ['contact', 'support', 'aide', 'problème', 'probleme', 'bug', 'erreur'])) {
            return ['answer' => "Pour tout problème technique ou question, vous pouvez :\n• Contacter votre pilote (si vous êtes étudiant)\n• Écrire à l'administrateur via le formulaire de contact\n• Consulter la FAQ dans les mentions légales", 'needs_admin' => false];
        }

        // Questions de remerciement
        if ($this->matchKeywords($lower, ['merci', 'thanks', 'super', 'parfait', 'génial', 'genial', 'cool'])) {
            return ['answer' => "Avec plaisir ! N'hésitez pas si vous avez d'autres questions. 😊", 'needs_admin' => false];
        }

        // Au revoir
        if ($this->matchKeywords($lower, ['au revoir', 'bye', 'à bientôt', 'a bientot', 'ciao', 'bonne journée', 'bonne soirée'])) {
            return ['answer' => "Au revoir et bonne continuation dans votre recherche de stage ! 👋", 'needs_admin' => false];
        }

        // Questions sur les statistiques (accessible à tous)
        if ($this->matchKeywords($lower, ['statistique', 'stat', 'chiffre', 'combien d\'offre', 'combien offre', 'nombre offre'])) {
            return $this->getPublicStats();
        }

        // ========================================
        // RÉPONSE PAR DÉFAUT
        // ========================================
        return $this->getDefaultResponse($userRole);
    }

    // ============================================================
    // GESTION DES QUESTIONS PAR RÔLE
    // ============================================================

    /**
     * Questions réservées aux admins
     */
    protected function handleAdminQuestions(string $lower, string $message): ?array
    {
        // Combien d'étudiants / liste étudiants
        if ($this->matchKeywords($lower, ['étudiant', 'etudiant']) && 
            $this->matchKeywords($lower, ['combien', 'nombre', 'liste', 'tous', 'afficher', 'voir', 'j\'ai', 'j ai', 'total'])) {
            return $this->getAllEtudiants();
        }

        // Combien de pilotes / liste pilotes
        if ($this->matchKeywords($lower, ['pilote']) && 
            $this->matchKeywords($lower, ['combien', 'nombre', 'liste', 'tous', 'afficher', 'voir', 'total'])) {
            return $this->getAllPilotes();
        }

        // Combien d'entreprises
        if ($this->matchKeywords($lower, ['entreprise', 'société', 'societe']) && 
            $this->matchKeywords($lower, ['combien', 'nombre', 'liste', 'total'])) {
            return $this->getEntreprisesCount();
        }

        // Candidatures pour une offre spécifique
        if ($this->matchKeywords($lower, ['candidat', 'candidature', 'postulé', 'postule', 'qui a'])) {
            $offreId = $this->detectOffreIdInMessage($lower, $message);
            if ($offreId !== null) {
                return $this->getCandidaturesForOffre($offreId);
            }
        }

        // Statistiques globales admin
        if ($this->matchKeywords($lower, ['statistique', 'stat', 'dashboard', 'tableau de bord', 'résumé', 'resume', 'vue d\'ensemble'])) {
            return $this->getAdminStats();
        }

        // Offres les plus populaires
        if ($this->matchKeywords($lower, ['populaire', 'top offre', 'meilleure offre', 'plus demandé', 'plus demande'])) {
            return $this->getTopOffers();
        }

        // Questions non admin-spécifiques
        return null;
    }

    /**
     * Questions réservées aux pilotes
     */
    protected function handlePiloteQuestions(string $lower, string $message, int $userId): ?array
    {
        // Mes étudiants
        if ($this->matchKeywords($lower, ['mes étudiant', 'mes etudiant', 'étudiant', 'etudiant']) && 
            $this->matchKeywords($lower, ['combien', 'nombre', 'liste', 'afficher', 'voir', 'j\'ai', 'j ai', 'mes'])) {
            return $this->getPiloteEtudiants($userId);
        }

        // Candidatures de mes étudiants
        if ($this->matchKeywords($lower, ['candidature', 'mes étudiant', 'mes etudiant']) ||
            ($this->matchKeywords($lower, ['candidature']) && $this->matchKeywords($lower, ['étudiant', 'etudiant']))) {
            return $this->getPiloteCandidatures($userId);
        }

        // Stats du pilote
        if ($this->matchKeywords($lower, ['statistique', 'stat', 'résumé', 'resume', 'bilan'])) {
            return $this->getPiloteStats($userId);
        }

        return null;
    }

    /**
     * Questions réservées aux étudiants
     */
    protected function handleEtudiantQuestions(string $lower, string $message, int $userId): ?array
    {
        // Mes candidatures
        if ($this->matchKeywords($lower, ['mes candidature', 'ma candidature', 'candidature']) && 
            $this->matchKeywords($lower, ['statut', 'état', 'etat', 'où en', 'ou en', 'combien', 'voir', 'afficher', 'mes', 'liste'])) {
            return $this->getEtudiantCandidatures($userId);
        }

        // Ma wishlist
        if ($this->matchKeywords($lower, ['wishlist', 'ma wishlist', 'favoris', 'mes favoris', 'liste de souhaits'])) {
            return $this->getEtudiantWishlist($userId);
        }

        // Conseils pour postuler
        if ($this->matchKeywords($lower, ['conseil', 'astuce', 'tip', 'comment réussir', 'comment reussir', 'cv', 'lettre motivation'])) {
            return $this->getApplicationTips();
        }

        // Mon profil
        if ($this->matchKeywords($lower, ['mon profil', 'mes informations', 'mes infos', 'modifier profil'])) {
            return ['answer' => "Pour modifier votre profil, allez dans le menu en haut à droite et cliquez sur « Mon compte ».\nVous pourrez y modifier vos informations personnelles.", 'needs_admin' => false];
        }

        return null;
    }

    // ============================================================
    // FONCTIONS DE RÉPONSE
    // ============================================================

    protected function getGreeting(?string $userRole): string
    {
        $base = "Bonjour ! 👋 Je suis l'assistant CesiStages.";
        
        if ($userRole === null) {
            return $base . "\n\nVoici des exemples de questions que vous pouvez me poser :\n• « Je cherche un stage à Lyon »\n• « Stage de 6 mois »\n• « Comment postuler ? »\n• « Comment s'inscrire ? »\n\nDites « aide » pour voir plus d'exemples !";
        }
        
        if ($userRole === 'admin') {
            return $base . "\n\nVoici des exemples de questions que vous pouvez me poser :\n• « Combien d'étudiants ? »\n• « Liste des pilotes »\n• « Statistiques globales »\n• « Candidatures pour l'offre 3 »\n\nDites « aide » pour voir plus d'exemples !";
        }
        
        if ($userRole === 'pilote') {
            return $base . "\n\nVoici des exemples de questions que vous pouvez me poser :\n• « Mes étudiants »\n• « Candidatures de mes étudiants »\n• « Mes statistiques »\n\nDites « aide » pour voir plus d'exemples !";
        }
        
        return $base . "\n\nVoici des exemples de questions que vous pouvez me poser :\n• « Je cherche un stage à Lyon »\n• « Stage de 6 mois »\n• « Mes candidatures »\n• « Ma wishlist »\n\nDites « aide » pour voir plus d'exemples !";
    }

    protected function getHelpMessage(?string $userRole): string
    {
        $base = "Je suis un assistant virtuel pour vous aider sur CesiStages.\n\n";
        $base .= "Voici des exemples de questions que vous pouvez me poser :\n\n";

        if ($userRole === 'admin') {
            $base .= "• « Combien d'étudiants ? »\n";
            $base .= "• « Liste des pilotes »\n";
            $base .= "• « Combien d'entreprises ? »\n";
            $base .= "• « Candidatures pour l'offre 5 »\n";
            $base .= "• « Statistiques globales »\n";
            $base .= "• « Offres les plus populaires »\n";
        } elseif ($userRole === 'pilote') {
            $base .= "• « Mes étudiants »\n";
            $base .= "• « Combien d'étudiants j'ai ? »\n";
            $base .= "• « Candidatures de mes étudiants »\n";
            $base .= "• « Mes statistiques »\n";
            $base .= "• « Stage à Lyon » (pour conseiller vos étudiants)\n";
            $base .= "• « Stage de 6 mois »\n";
        } elseif ($userRole === 'etudiant') {
            $base .= "• « Je cherche un stage à Lyon »\n";
            $base .= "• « Stage de 6 mois »\n";
            $base .= "• « Offres en PHP »\n";
            $base .= "• « Stages chez TechCorp »\n";
            $base .= "• « Mes candidatures »\n";
            $base .= "• « Ma wishlist »\n";
            $base .= "• « Conseils pour postuler »\n";
        } else {
            $base .= "• « Je cherche un stage à Lyon »\n";
            $base .= "• « Stage de 6 mois »\n";
            $base .= "• « Offres en PHP »\n";
            $base .= "• « Stages chez TechCorp »\n";
            $base .= "• « Comment postuler ? »\n";
            $base .= "• « Comment s'inscrire ? »\n";
            $base .= "• « Voir les entreprises »\n";
            $base .= "• « Statistiques du site »\n\n";
            $base .= "🔐 Connectez-vous pour accéder à plus de fonctionnalités !";
        }

        return $base;
    }

    protected function getOffersHelp(?string $userRole): array
    {
        $answer = "📋 Pour trouver des offres de stage, vous avez plusieurs options :\n\n";
        $answer .= "1. Allez dans le menu « Offres » pour voir toutes les offres disponibles\n";
        $answer .= "2. Utilisez les filtres (ville, recherche) pour affiner\n";
        $answer .= "3. Demandez-moi directement, par exemple :\n";
        $answer .= "   • « Stage à Paris »\n";
        $answer .= "   • « Offres de 3 mois »\n";
        $answer .= "   • « Stage en développement web »\n\n";

        if ($userRole === 'etudiant') {
            $answer .= "💡 Astuce : Ajoutez les offres intéressantes à votre wishlist pour les retrouver facilement !";
        } elseif ($userRole === null) {
            $answer .= "💡 Connectez-vous pour postuler aux offres et gérer votre wishlist.";
        }

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getCandidatureHelp(?string $userRole): array
    {
        if ($userRole === 'etudiant') {
            $answer = "📝 Pour postuler à une offre :\n\n";
            $answer .= "1. Consultez le détail d'une offre qui vous intéresse\n";
            $answer .= "2. Cliquez sur « Postuler »\n";
            $answer .= "3. Rédigez votre lettre de motivation\n";
            $answer .= "4. Téléchargez votre CV (format PDF)\n";
            $answer .= "5. Validez votre candidature\n\n";
            $answer .= "Vous pouvez suivre le statut de vos candidatures dans « Mes candidatures ».";
        } elseif ($userRole === 'pilote') {
            $answer = "📊 En tant que pilote, vous pouvez :\n\n";
            $answer .= "• Voir les candidatures de vos étudiants dans « Candidatures »\n";
            $answer .= "• Mettre à jour le statut (acceptée, refusée, en attente)\n";
            $answer .= "• Accompagner vos étudiants dans leur recherche";
        } elseif ($userRole === 'admin') {
            $answer = "👑 En tant qu'admin, vous avez accès à toutes les candidatures.\n";
            $answer .= "Utilisez le menu « Candidatures » ou demandez-moi « candidatures pour l'offre X ».";
        } else {
            $answer = "Pour postuler à une offre, vous devez d'abord vous connecter avec un compte étudiant.\n";
            $answer .= "Rendez-vous sur la page de connexion ou créez un compte.";
        }

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getEntreprisesHelp(?string $userRole): array
    {
        $answer = "🏢 Les entreprises sur CesiStages :\n\n";
        $answer .= "• Consultez la liste dans le menu « Entreprises »\n";
        $answer .= "• Chaque fiche contient les coordonnées, le secteur et les offres\n";
        $answer .= "• Vous pouvez voir les évaluations des autres utilisateurs\n\n";
        $answer .= "Demandez-moi « offres chez [nom entreprise] » pour voir leurs stages !";

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getWishlistHelp(?string $userRole): array
    {
        if ($userRole === 'etudiant') {
            $answer = "❤️ Votre Wishlist :\n\n";
            $answer .= "• Cliquez sur le cœur sur une offre pour l'ajouter à vos favoris\n";
            $answer .= "• Retrouvez toutes vos offres sauvegardées dans « Wishlist »\n";
            $answer .= "• Vous pouvez retirer une offre à tout moment\n\n";
            $answer .= "Dites-moi « ma wishlist » pour voir vos offres sauvegardées !";
        } else {
            $answer = "La wishlist permet aux étudiants de sauvegarder les offres qui les intéressent.\n";
            if ($userRole === null) {
                $answer .= "Connectez-vous en tant qu'étudiant pour utiliser cette fonctionnalité.";
            }
        }

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getRegisterHelp(?string $userRole): array
    {
        if ($userRole !== null) {
            return ['answer' => "Vous êtes déjà connecté ! 😊\nSi vous souhaitez créer un autre compte, déconnectez-vous d'abord.", 'needs_admin' => false];
        }

        $answer = "📝 Pour vous inscrire sur CesiStages :\n\n";
        $answer .= "1. Cliquez sur « Inscription » en haut à droite\n";
        $answer .= "2. Remplissez le formulaire (nom, prénom, email...)\n";
        $answer .= "3. Choisissez un mot de passe sécurisé\n";
        $answer .= "4. Validez votre inscription\n\n";
        $answer .= "Vous pourrez ensuite vous connecter et accéder à toutes les fonctionnalités !";

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getLoginHelp(?string $userRole): array
    {
        if ($userRole !== null) {
            return ['answer' => "Vous êtes déjà connecté en tant que " . $userRole . " ! 😊", 'needs_admin' => false];
        }

        $answer = "🔐 Pour vous connecter :\n\n";
        $answer .= "1. Cliquez sur « Connexion » en haut à droite\n";
        $answer .= "2. Entrez votre email et mot de passe\n";
        $answer .= "3. Cliquez sur « Se connecter »\n\n";
        $answer .= "Mot de passe oublié ? Contactez votre administrateur.";

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getApplicationTips(): array
    {
        $answer = "💡 Conseils pour réussir vos candidatures :\n\n";
        $answer .= "📄 CV :\n";
        $answer .= "• Format PDF, 1-2 pages maximum\n";
        $answer .= "• Mettez en avant vos compétences techniques\n";
        $answer .= "• Incluez vos projets personnels/scolaires\n\n";
        $answer .= "✉️ Lettre de motivation :\n";
        $answer .= "• Personnalisez-la pour chaque entreprise\n";
        $answer .= "• Expliquez pourquoi ce stage vous intéresse\n";
        $answer .= "• Montrez ce que vous pouvez apporter\n\n";
        $answer .= "🎯 Général :\n";
        $answer .= "• Postulez à plusieurs offres\n";
        $answer .= "• Relancez après 1-2 semaines sans réponse\n";
        $answer .= "• Préparez-vous aux entretiens !";

        return ['answer' => $answer, 'needs_admin' => false];
    }

    protected function getDefaultResponse(?string $userRole): array
    {
        $answer = "Je ne suis pas sûr de comprendre votre question. 🤔\n\n";
        $answer .= "Voici des exemples de questions que vous pouvez me poser :\n";

        if ($userRole === 'admin') {
            $answer .= "• « Combien d'étudiants ? »\n";
            $answer .= "• « Liste des pilotes »\n";
            $answer .= "• « Statistiques globales »\n";
            $answer .= "• « Candidatures pour l'offre 5 »";
        } elseif ($userRole === 'pilote') {
            $answer .= "• « Mes étudiants »\n";
            $answer .= "• « Candidatures de mes étudiants »\n";
            $answer .= "• « Mes statistiques »\n";
            $answer .= "• « Stage à Lyon » (pour conseiller vos étudiants)";
        } elseif ($userRole === 'etudiant') {
            $answer .= "• « Stage à Lyon »\n";
            $answer .= "• « Stage de 6 mois »\n";
            $answer .= "• « Mes candidatures »\n";
            $answer .= "• « Ma wishlist »\n";
            $answer .= "• « Conseils pour postuler »";
        } else {
            $answer .= "• « Stage à Lyon »\n";
            $answer .= "• « Stage de 6 mois »\n";
            $answer .= "• « Comment postuler ? »\n";
            $answer .= "• « Comment s'inscrire ? »\n\n";
            $answer .= "💡 Connectez-vous pour accéder à plus de fonctionnalités.\n";
            $answer .= "Si votre question est trop technique, je la transmettrai à un administrateur.";
        }

        return ['answer' => $answer, 'needs_admin' => true];
    }

    // ============================================================
    // FONCTIONS DE RECHERCHE D'OFFRES
    // ============================================================

    protected function searchOffersByDuree(int $duree): array
    {
        $offreModel = new OffreModel();
        $offres = $offreModel->getByDuree($duree, 10);

        if (!empty($offres)) {
            $offersLinks = [];
            foreach (array_slice($offres, 0, 5) as $offre) {
                $offersLinks[] = [
                    'id' => (int) $offre['id'],
                    'title' => $offre['titre'] ?? 'Offre',
                    'url' => BASE_URL . '/offres/' . $offre['id'],
                ];
            }

            return [
                'answer' => "Voici quelques offres de stage d'une durée de {$duree} mois :",
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        }

        return [
            'answer' => "Je n'ai pas trouvé d'offres avec une durée de {$duree} mois.\nEssayez une autre durée (3, 4 ou 6 mois par exemple) ou consultez la page « Offres ».",
            'needs_admin' => false,
            'offers' => [],
        ];
    }

    protected function searchOffersByVille(string $ville): array
    {
        $offreModel = new OffreModel();
        $offres = $offreModel->getByVille($ville, 10);

        if (empty($offres)) {
            $offres = $offreModel->searchAdvanced($ville, '');
        }

        if (!empty($offres)) {
            $offersLinks = [];
            foreach (array_slice($offres, 0, 5) as $offre) {
                $offersLinks[] = [
                    'id' => (int) $offre['id'],
                    'title' => $offre['titre'] ?? 'Offre',
                    'url' => BASE_URL . '/offres/' . $offre['id'],
                ];
            }

            return [
                'answer' => "Voici quelques offres de stage à {$ville} :",
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        }

        return [
            'answer' => "Je n'ai pas trouvé d'offres de stage à {$ville} pour le moment.\nConsultez la page « Offres » ou essayez une autre ville.",
            'needs_admin' => false,
            'offers' => [],
        ];
    }

    protected function searchOffersByCompetence(string $competence): array
    {
        $offreModel = new OffreModel();
        $offres = $offreModel->getByCompetence($competence, 10);

        if (!empty($offres)) {
            $offersLinks = [];
            foreach (array_slice($offres, 0, 5) as $offre) {
                $offersLinks[] = [
                    'id' => (int) $offre['id'],
                    'title' => $offre['titre'] ?? 'Offre',
                    'url' => BASE_URL . '/offres/' . $offre['id'],
                ];
            }

            return [
                'answer' => "Voici quelques offres qui demandent « {$competence} » :",
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        }

        return [
            'answer' => "Je n'ai pas trouvé d'offres correspondant à « {$competence} ».\nEssayez un autre mot-clé ou consultez la page « Offres ».",
            'needs_admin' => false,
            'offers' => [],
        ];
    }

    protected function searchOffersByEntreprise(string $entrepriseNom): array
    {
        $offreModel = new OffreModel();
        $offres = $offreModel->getByEntreprise($entrepriseNom, 10);

        if (!empty($offres)) {
            $offersLinks = [];
            foreach (array_slice($offres, 0, 5) as $offre) {
                $offersLinks[] = [
                    'id' => (int) $offre['id'],
                    'title' => $offre['titre'] ?? 'Offre',
                    'url' => BASE_URL . '/offres/' . $offre['id'],
                ];
            }

            return [
                'answer' => "Voici quelques offres de stage chez « {$entrepriseNom} » :",
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        }

        return [
            'answer' => "Je n'ai pas trouvé d'offres pour l'entreprise « {$entrepriseNom} ».\nVérifiez le nom ou consultez la liste des entreprises.",
            'needs_admin' => false,
            'offers' => [],
        ];
    }

    // ============================================================
    // FONCTIONS ADMIN
    // ============================================================

    protected function getAllEtudiants(): array
    {
        try {
            $userModel = new User();
            $etudiants = $userModel->getByRolePaginated('etudiant', 1, 1000);

            if (empty($etudiants)) {
                return [
                    'answer' => "Il n'y a actuellement aucun étudiant dans la base de données.",
                    'needs_admin' => false,
                    'etudiants_table' => [],
                ];
            }

            $count = count($etudiants);
            $etudiantsData = [];
            foreach ($etudiants as $etudiant) {
                $etudiantsData[] = [
                    'id' => (int) ($etudiant['id'] ?? 0),
                    'nom' => ($etudiant['prenom'] ?? '') . ' ' . ($etudiant['nom'] ?? ''),
                    'email' => $etudiant['email'] ?? '',
                    'date_creation' => isset($etudiant['created_at']) ? date('d/m/Y', strtotime($etudiant['created_at'])) : 'N/A',
                ];
            }

            return [
                'answer' => "Il y a actuellement {$count} étudiant" . ($count > 1 ? 's' : '') . " dans la plateforme :",
                'needs_admin' => false,
                'etudiants_table' => $etudiantsData,
            ];
        } catch (\Exception $e) {
            return [
                'answer' => "Erreur lors de la récupération des étudiants : " . $e->getMessage(),
                'needs_admin' => false,
                'etudiants_table' => [],
            ];
        }
    }

    protected function getAllPilotes(): array
    {
        try {
            $userModel = new User();
            $pilotes = $userModel->getByRolePaginated('pilote', 1, 1000);

            if (empty($pilotes)) {
                return [
                    'answer' => "Il n'y a actuellement aucun pilote dans la base de données.",
                    'needs_admin' => false,
                    'etudiants_table' => [],
                ];
            }

            $count = count($pilotes);
            $pilotesData = [];
            foreach ($pilotes as $pilote) {
                $pilotesData[] = [
                    'id' => (int) ($pilote['id'] ?? 0),
                    'nom' => ($pilote['prenom'] ?? '') . ' ' . ($pilote['nom'] ?? ''),
                    'email' => $pilote['email'] ?? '',
                    'date_creation' => isset($pilote['created_at']) ? date('d/m/Y', strtotime($pilote['created_at'])) : 'N/A',
                ];
            }

            return [
                'answer' => "Il y a actuellement {$count} pilote" . ($count > 1 ? 's' : '') . " dans la plateforme :",
                'needs_admin' => false,
                'etudiants_table' => $pilotesData,
            ];
        } catch (\Exception $e) {
            return [
                'answer' => "Erreur lors de la récupération des pilotes : " . $e->getMessage(),
                'needs_admin' => false,
                'etudiants_table' => [],
            ];
        }
    }

    protected function getEntreprisesCount(): array
    {
        try {
            $entrepriseModel = new Entreprise();
            $entreprises = $entrepriseModel->all();
            $count = count($entreprises);

            $answer = "Il y a actuellement {$count} entreprise" . ($count > 1 ? 's' : '') . " dans la plateforme.\n\n";
            $answer .= "Pour voir la liste complète, allez dans le menu « Entreprises ».";

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors du comptage des entreprises.", 'needs_admin' => false];
        }
    }

    protected function getCandidaturesForOffre(int $offreId): array
    {
        try {
            $db = Model::getDB();
            $stmt = $db->prepare("SELECT id, titre FROM offres WHERE id = :id");
            $stmt->execute([':id' => $offreId]);
            $offre = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$offre) {
                return [
                    'answer' => "Je n'ai pas trouvé d'offre avec l'ID {$offreId}.",
                    'needs_admin' => false,
                    'etudiants' => [],
                ];
            }

            $candidatureModel = new Candidature();
            $candidatures = $candidatureModel->getByOffreWithEtudiants($offreId);

            if (empty($candidatures)) {
                return [
                    'answer' => "Aucun étudiant n'a encore candidaté pour l'offre « {$offre['titre']} » (ID: {$offreId}).",
                    'needs_admin' => false,
                    'etudiants' => [],
                ];
            }

            $count = count($candidatures);
            $answer = "Pour l'offre « {$offre['titre']} » (ID: {$offreId}), {$count} étudiant" . ($count > 1 ? 's ont' : ' a') . " candidaté :";

            $etudiants = [];
            foreach ($candidatures as $candidature) {
                $statutLabel = [
                    'en_attente' => 'En attente',
                    'acceptee' => 'Acceptée',
                    'refusee' => 'Refusée'
                ][$candidature['statut'] ?? 'en_attente'] ?? $candidature['statut'];

                $etudiants[] = [
                    'nom' => ($candidature['etudiant_prenom'] ?? '') . ' ' . ($candidature['etudiant_nom'] ?? ''),
                    'email' => $candidature['etudiant_email'] ?? '',
                    'statut' => $statutLabel,
                ];
            }

            return [
                'answer' => $answer,
                'needs_admin' => false,
                'etudiants' => $etudiants,
            ];
        } catch (\Exception $e) {
            return [
                'answer' => "Erreur lors de la récupération des candidatures.",
                'needs_admin' => false,
                'etudiants' => [],
            ];
        }
    }

    protected function getAdminStats(): array
    {
        try {
            $userModel = new User();
            $offreModel = new OffreModel();
            $candidatureModel = new Candidature();
            $entrepriseModel = new Entreprise();

            $nbEtudiants = $userModel->countByRole('etudiant');
            $nbPilotes = $userModel->countByRole('pilote');
            $nbOffres = count($offreModel->getAllWithEntreprise(1, 10000));
            $nbEntreprises = count($entrepriseModel->all());
            $nbCandidatures = count($candidatureModel->getAllWithDetails());

            $answer = "📊 Statistiques globales CesiStages :\n\n";
            $answer .= "👥 Utilisateurs :\n";
            $answer .= "   • {$nbEtudiants} étudiants\n";
            $answer .= "   • {$nbPilotes} pilotes\n\n";
            $answer .= "🏢 {$nbEntreprises} entreprises\n";
            $answer .= "📋 {$nbOffres} offres de stage\n";
            $answer .= "📝 {$nbCandidatures} candidatures au total";

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération des statistiques.", 'needs_admin' => false];
        }
    }

    protected function getTopOffers(): array
    {
        try {
            $offreModel = new OffreModel();
            $topOffres = $offreModel->getTopWishlist(5);

            if (empty($topOffres)) {
                return ['answer' => "Aucune offre n'a encore été ajoutée en wishlist.", 'needs_admin' => false];
            }

            $offersLinks = [];
            foreach ($topOffres as $offre) {
                $offersLinks[] = [
                    'id' => (int) $offre['id'],
                    'title' => $offre['titre'] . ' (' . ($offre['wishlist_count'] ?? 0) . ' ❤️)',
                    'url' => BASE_URL . '/offres/' . $offre['id'],
                ];
            }

            return [
                'answer' => "🏆 Top 5 des offres les plus populaires (en wishlist) :",
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération des offres populaires.", 'needs_admin' => false];
        }
    }

    protected function getPublicStats(): array
    {
        try {
            $offreModel = new OffreModel();
            $entrepriseModel = new Entreprise();

            $nbOffres = count($offreModel->getAllWithEntreprise(1, 10000));
            $nbEntreprises = count($entrepriseModel->all());

            $answer = "📊 CesiStages en chiffres :\n\n";
            $answer .= "• {$nbOffres} offres de stage disponibles\n";
            $answer .= "• {$nbEntreprises} entreprises partenaires\n\n";
            $answer .= "Consultez la page « Offres » pour découvrir toutes les opportunités !";

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération des statistiques.", 'needs_admin' => false];
        }
    }

    // ============================================================
    // FONCTIONS PILOTE
    // ============================================================

    protected function getPiloteEtudiants(int $piloteId): array
    {
        try {
            $userModel = new User();
            $etudiants = $userModel->getEtudiantsByPilote($piloteId);

            if (empty($etudiants)) {
                return [
                    'answer' => "Vous n'avez actuellement aucun étudiant assigné.",
                    'needs_admin' => false,
                    'etudiants_table' => [],
                ];
            }

            $count = count($etudiants);
            $etudiantsData = [];
            foreach ($etudiants as $etudiant) {
                $etudiantsData[] = [
                    'id' => (int) ($etudiant['id'] ?? 0),
                    'nom' => ($etudiant['prenom'] ?? '') . ' ' . ($etudiant['nom'] ?? ''),
                    'email' => $etudiant['email'] ?? '',
                    'date_creation' => isset($etudiant['created_at']) ? date('d/m/Y', strtotime($etudiant['created_at'])) : 'N/A',
                ];
            }

            return [
                'answer' => "Vous avez {$count} étudiant" . ($count > 1 ? 's' : '') . " assigné" . ($count > 1 ? 's' : '') . " :",
                'needs_admin' => false,
                'etudiants_table' => $etudiantsData,
            ];
        } catch (\Exception $e) {
            return [
                'answer' => "Erreur lors de la récupération de vos étudiants.",
                'needs_admin' => false,
                'etudiants_table' => [],
            ];
        }
    }

    protected function getPiloteCandidatures(int $piloteId): array
    {
        try {
            $candidatureModel = new Candidature();
            $candidatures = $candidatureModel->getByPiloteWithDetails($piloteId);

            if (empty($candidatures)) {
                return [
                    'answer' => "Vos étudiants n'ont pas encore postulé à des offres.",
                    'needs_admin' => false,
                ];
            }

            $count = count($candidatures);
            $answer = "📝 Vos étudiants ont soumis {$count} candidature" . ($count > 1 ? 's' : '') . " :\n\n";

            $statuts = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
            foreach ($candidatures as $c) {
                $statuts[$c['statut'] ?? 'en_attente']++;
            }

            $answer .= "• {$statuts['en_attente']} en attente\n";
            $answer .= "• {$statuts['acceptee']} acceptée(s)\n";
            $answer .= "• {$statuts['refusee']} refusée(s)\n\n";
            $answer .= "Consultez le menu « Candidatures » pour plus de détails.";

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération des candidatures.", 'needs_admin' => false];
        }
    }

    protected function getPiloteStats(int $piloteId): array
    {
        try {
            $userModel = new User();
            $candidatureModel = new Candidature();

            $etudiants = $userModel->getEtudiantsByPilote($piloteId);
            $nbEtudiants = count($etudiants);
            $nbCandidatures = $candidatureModel->countByPilote($piloteId);

            $answer = "📊 Vos statistiques de pilote :\n\n";
            $answer .= "👥 {$nbEtudiants} étudiant" . ($nbEtudiants > 1 ? 's' : '') . " assigné" . ($nbEtudiants > 1 ? 's' : '') . "\n";
            $answer .= "📝 {$nbCandidatures} candidature" . ($nbCandidatures > 1 ? 's' : '') . " au total\n\n";

            if ($nbEtudiants > 0) {
                $moyenne = round($nbCandidatures / $nbEtudiants, 1);
                $answer .= "📈 Moyenne : {$moyenne} candidature(s) par étudiant";
            }

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération de vos statistiques.", 'needs_admin' => false];
        }
    }

    // ============================================================
    // FONCTIONS ETUDIANT
    // ============================================================

    protected function getEtudiantCandidatures(int $etudiantId): array
    {
        try {
            $candidatureModel = new Candidature();
            $candidatures = $candidatureModel->getByEtudiant($etudiantId);

            if (empty($candidatures)) {
                return [
                    'answer' => "Vous n'avez pas encore postulé à des offres.\nAllez dans « Offres » pour trouver des stages qui vous intéressent !",
                    'needs_admin' => false,
                ];
            }

            $count = count($candidatures);
            $answer = "📝 Vous avez {$count} candidature" . ($count > 1 ? 's' : '') . " :\n\n";

            $statuts = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
            foreach ($candidatures as $c) {
                $statuts[$c['statut'] ?? 'en_attente']++;
            }

            $answer .= "• ⏳ {$statuts['en_attente']} en attente\n";
            $answer .= "• ✅ {$statuts['acceptee']} acceptée(s)\n";
            $answer .= "• ❌ {$statuts['refusee']} refusée(s)\n\n";
            $answer .= "Consultez « Mes candidatures » pour voir les détails.";

            return ['answer' => $answer, 'needs_admin' => false];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération de vos candidatures.", 'needs_admin' => false];
        }
    }

    protected function getEtudiantWishlist(int $etudiantId): array
    {
        try {
            $wishlistModel = new Wishlist();
            $wishlist = $wishlistModel->getByEtudiantWithDetails($etudiantId);

            if (empty($wishlist)) {
                return [
                    'answer' => "Votre wishlist est vide.\nAjoutez des offres qui vous intéressent en cliquant sur le cœur !",
                    'needs_admin' => false,
                    'offers' => [],
                ];
            }

            $count = count($wishlist);
            $offersLinks = [];
            foreach (array_slice($wishlist, 0, 5) as $item) {
                $offersLinks[] = [
                    'id' => (int) $item['id'],
                    'title' => $item['titre'] ?? 'Offre',
                    'url' => BASE_URL . '/offres/' . $item['id'],
                ];
            }

            $answer = "❤️ Vous avez {$count} offre" . ($count > 1 ? 's' : '') . " dans votre wishlist";
            if ($count > 5) {
                $answer .= " (voici les 5 premières)";
            }
            $answer .= " :";

            return [
                'answer' => $answer,
                'needs_admin' => false,
                'offers' => $offersLinks,
            ];
        } catch (\Exception $e) {
            return ['answer' => "Erreur lors de la récupération de votre wishlist.", 'needs_admin' => false];
        }
    }

    // ============================================================
    // UTILITAIRES
    // ============================================================

    protected function matchKeywords(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, mb_strtolower($keyword, 'UTF-8')) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function detectDureeInMessage(string $lower): ?int
    {
        if (preg_match('/(\d+)\s*mois/u', $lower, $matches)) {
            $value = (int) $matches[1];
            if ($value > 0 && $value <= 24) {
                return $value;
            }
        }
        return null;
    }

    protected function detectVilleInMessage(string $lower): ?string
    {
        if (strpos($lower, 'stage') === false && strpos($lower, 'offre') === false) {
            return null;
        }

        $words = preg_split('/\s+/', trim($lower));
        if (empty($words)) {
            return null;
        }

        $last = end($words);
        $last = preg_replace('/[^a-zàâäéèêëîïôöùûüç-]/u', '', $last);

        if (mb_strlen($last, 'UTF-8') < 3) {
            return null;
        }

        $ville = mb_convert_case($last, MB_CASE_TITLE, 'UTF-8');
        return $this->matchClosestVille($ville);
    }

    protected function matchClosestVille(string $ville): ?string
    {
        $entrepriseModel = new Entreprise();
        $connues = $entrepriseModel->getAllVilles();

        if (empty($connues)) {
            return $ville;
        }

        $villeLower = mb_strtolower($ville, 'UTF-8');
        $best = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($connues as $v) {
            $candidateLower = mb_strtolower($v, 'UTF-8');
            $distance = levenshtein($villeLower, $candidateLower);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $v;
            }
        }

        if ($best !== null && $bestDistance <= 2) {
            return $best;
        }

        return $ville;
    }

    protected function detectCompetenceInMessage(string $lower): ?string
    {
        $offreModel = new OffreModel();
        $competences = $offreModel->getAllCompetences();

        if (empty($competences)) {
            return null;
        }

        foreach ($competences as $comp) {
            $compLower = mb_strtolower($comp, 'UTF-8');
            if ($compLower !== '' && strpos($lower, $compLower) !== false) {
                return $comp;
            }
        }

        return null;
    }

    protected function detectEntrepriseInMessage(string $lower): ?string
    {
        $entrepriseModel = new Entreprise();
        $entreprises = $entrepriseModel->all();

        if (empty($entreprises)) {
            return null;
        }

        foreach ($entreprises as $e) {
            if (empty($e['nom'])) {
                continue;
            }
            $nomLower = mb_strtolower($e['nom'], 'UTF-8');
            if (strpos($lower, $nomLower) !== false) {
                return $e['nom'];
            }
        }

        return null;
    }

    protected function detectOffreIdInMessage(string $lower, string $originalMessage): ?int
    {
        $patterns = [
            '/offre\s*(?:#|n[°o]?|numéro|numero)?\s*(\d+)/ui',
            '/pour\s+l\'?offre\s*(?:#|n[°o]?)?\s*(\d+)/ui',
            '/l\'offre\s*(?:#|n[°o]?)?\s*(\d+)/ui',
            '/offre\s+(\d+)/ui',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $lower, $matches)) {
                $id = (int) $matches[1];
                if ($id > 0) {
                    return $id;
                }
            }
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $originalMessage, $matches)) {
                $id = (int) $matches[1];
                if ($id > 0) {
                    return $id;
                }
            }
        }

        return null;
    }

    protected function storeInteraction(?int $userId, string $question, string $answer, bool $needsAdmin): void
    {
        $db = Model::getDB();

        $stmt = $db->prepare(
            "INSERT INTO chatbot_interactions (user_id, question, answer, needs_admin, created_at)
             VALUES (:user_id, :question, :answer, :needs_admin, NOW())"
        );

        $stmt->bindValue(':user_id', $userId, $userId !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':question', $question, PDO::PARAM_STR);
        $stmt->bindValue(':answer', $answer, PDO::PARAM_STR);
        $stmt->bindValue(':needs_admin', $needsAdmin ? 1 : 0, PDO::PARAM_INT);

        $stmt->execute();
    }
}
