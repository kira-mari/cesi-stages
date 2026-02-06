<?php
namespace Controllers;

use Core\Controller;
use Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Contrôleur d'authentification
 */
class Auth extends Controller
{
    /**
     * Page de connexion
     *
     * @return void
     */
    public function login()
    {
        // Si déjà connecté, rediriger vers le tableau de bord
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember_me']);

            if (empty($email) || empty($password)) {
                $errors[] = "Veuillez remplir tous les champs.";
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Vérification du statut de vérification
                    if ($user['is_verified'] == 0) {
                        $_SESSION['verify_email'] = $email;
                        $_SESSION['flash_warning'] = "Votre compte n'est pas encore vérifié. Veuillez entrer le code reçu par email.";
                        $this->redirect('verify');
                    }

                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    
                    // Vérifier si le compte nécessite une approbation
                    $needsApproval = in_array($user['role'], ['pilote', 'recruteur']);
                    $isApproved = isset($user['is_approved']) ? (bool) $user['is_approved'] : true;
                    
                    if ($needsApproval && !$isApproved) {
                        // Compte en attente d'approbation - accès limité
                        if ($user['role'] === 'pilote') {
                            $_SESSION['user_role'] = 'pilote'; // Rôle temporaire avec restrictions
                        } else {
                            $_SESSION['user_role'] = 'etudiant'; // Pour les recruteurs
                        }
                        $_SESSION['user_role_pending'] = $user['role'];
                        $_SESSION['user_is_approved'] = false;
                        $_SESSION['flash_info'] = "Votre demande de compte " . ucfirst($user['role']) . " est en attente de validation par un administrateur.";
                    } else {
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['user_is_approved'] = true;
                        
                        // Pour les recruteurs, compter le nombre d'entreprises assignées
                        if ($user['role'] === 'recruteur') {
                            $_SESSION['user_nb_entreprises'] = $userModel->countEntreprisesByRecruteur($user['id']);
                        }
                    }

                    // Gestion du "Se souvenir de moi"
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $userModel->updateRememberToken($user['id'], $token);
                        // Cookie valable 30 jours
                        setcookie('remember_me', $user['id'] . ':' . $token, time() + (86400 * 30), "/", "", false, true);
                    }

                    // Régénération de l'ID de session pour la sécurité
                    session_regenerate_id(true);

                    $_SESSION['flash_success'] = "Connexion réussie ! Bienvenue, " . $user['prenom'] . ".";
                    $this->redirect('dashboard');
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            }
        }

        $this->render('auth/login', [
            'title' => 'Connexion - ' . APP_NAME,
            'errors' => $errors,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Démarre le flux OAuth Google (redirection vers Google)
     */
    public function googleRedirect()
    {
        if (!GOOGLE_OAUTH_ENABLED) {
            http_response_code(404);
            echo 'Google SSO non configuré.';
            exit;
        }

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'select_account'
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Callback Google OAuth: échange le code, récupère les infos utilisateur
     */
    public function googleCallback()
    {
        if (!GOOGLE_OAUTH_ENABLED) {
            http_response_code(404);
            echo 'Google SSO non configuré.';
            exit;
        }

        $code = $_GET['code'] ?? null;
        if (!$code) {
            $_SESSION['flash_error'] = 'Authentification Google annulée.';
            $this->redirect('login');
        }

        // Échange du code contre un token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $post = http_build_query([
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT,
            'grant_type' => 'authorization_code'
        ]);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $post,
                'timeout' => 10
            ]
        ];

        $response = @file_get_contents($tokenUrl, false, stream_context_create($opts));
        if ($response === false) {
            $_SESSION['flash_error'] = 'Erreur lors de la récupération du token Google.';
            $this->redirect('login');
        }

        $data = json_decode($response, true);
        if (empty($data['access_token'])) {
            $_SESSION['flash_error'] = 'Token Google invalide.';
            $this->redirect('login');
        }

        // Récupération des informations utilisateur
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer " . $data['access_token'] . "\r\n",
                'timeout' => 10
            ]
        ];

        $userInfo = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, stream_context_create($opts));
        if ($userInfo === false) {
            $_SESSION['flash_error'] = 'Impossible de récupérer les informations Google.';
            $this->redirect('login');
        }

        $u = json_decode($userInfo, true);
        $email = $u['email'] ?? null;
        $prenom = $u['given_name'] ?? ($u['name'] ?? '');
        $nom = $u['family_name'] ?? '';

        if (!$email) {
            $_SESSION['flash_error'] = 'Email Google introuvable.';
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            // Création d'un compte utilisateur par défaut (rôle étudiant)
            $password = bin2hex(random_bytes(8));
            $userData = [
                'nom' => $nom ?: 'Google',
                'prenom' => $prenom ?: 'User',
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'etudiant',
                'is_verified' => 1 // Google users are verified by definition
            ];
            
            $userId = $userModel->create($userData);

            if ($userId) {
                $user = $userModel->findByEmail($email);
            }
        }

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            session_regenerate_id(true);
            $_SESSION['flash_success'] = "Connexion via Google réussie. Bienvenue, " . ($user['prenom'] ?? '') . ".";
            $this->redirect('dashboard');
        }

        $_SESSION['flash_error'] = 'Impossible de créer ou trouver l\'utilisateur.';
        $this->redirect('login');
    }

    /**
     * Déconnexion
     *
     * @return void
     */
    public function logout()
    {
        // On récupère l'ID avant de détruire la session pour le nettoyage côté client
        $userId = $_SESSION['user_id'] ?? null;

        // Suppression de toutes les variables de session
        $_SESSION = [];

        // Destruction du cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        // Suppression du cookie "Se souvenir de moi"
        if (isset($_COOKIE['remember_me'])) {
             setcookie('remember_me', '', time() - 3600, "/");
        }

        // Destruction de la session
        session_destroy();

        // Affichage de la vue de déconnexion (qui contient le JS de nettoyage)
        // au lieu d'une redirection directe header()
        require_once __DIR__ . '/../views/auth/logout.php';
        exit;
    }

    /**
     * Page d'inscription (Publique)
     *
     * @return void
     */
    public function register()
    {
        // Si déjà connecté, rediriger
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($csrfToken)) {
                $errors[] = "Token de sécurité invalide.";
            } else {
                $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
                $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                // Récupérer le rôle choisi (par défaut étudiant)
                $roleChoisi = $_POST['role'] ?? 'etudiant';
                // Valider que le rôle est autorisé (pas admin)
                $rolesAutorises = ['etudiant', 'pilote', 'recruteur'];
                $role = in_array($roleChoisi, $rolesAutorises) ? $roleChoisi : 'etudiant';

                // Validation
                if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                    $errors[] = "Veuillez remplir tous les champs obligatoires.";
                }

                if ($password !== $confirm_password) {
                    $errors[] = "Les mots de passe ne correspondent pas.";
                }

                if (strlen($password) < 8 || 
                    !preg_match('/[A-Z]/', $password) || 
                    !preg_match('/[a-z]/', $password) || 
                    !preg_match('/[0-9]/', $password) || 
                    !preg_match('/[\W_]/', $password)) {
                    $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                }

                if (empty($errors)) {
                    $userModel = new User();

                    // Vérification si l'email existe déjà
                    if ($userModel->findByEmail($email)) {
                        $errors[] = "Cet email est déjà utilisé.";
                    } else {
                        // Générer un code de vérification
                        $verificationCode = (string) rand(100000, 999999);

                        // Stocker les données en session pour vérification
                        $_SESSION['pending_registration'] = [
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'email' => $email,
                            'password' => password_hash($password, PASSWORD_BCRYPT),
                            'role' => $role,
                            'verification_code' => $verificationCode,
                            'expires_at' => time() + (15 * 60), // 15 minutes
                            'attempts' => 0
                        ];
                        $_SESSION['verify_email'] = $email;

                        // Envoyer l'email de vérification
                        if ($this->sendVerificationEmail($email, $prenom, $verificationCode)) {
                            $_SESSION['flash_success'] = "Un code de vérification a été envoyé à votre adresse email.";
                            $this->redirect('verify');
                        } else {
                            $errors[] = "Erreur lors de l'envoi de l'email de vérification. Veuillez réessayer.";
                            // Nettoyer la session en cas d'erreur
                            unset($_SESSION['pending_registration']);
                            unset($_SESSION['verify_email']);
                        }
                    }
                }
            }
        }

        $this->render('auth/register', [
            'title' => 'Créer un compte - ' . APP_NAME,
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Page de vérification du code
     */
    public function verify()
    {
        if (empty($_SESSION['verify_email']) || empty($_SESSION['pending_registration'])) {
            $this->redirect('login');
        }

        $pendingUser = $_SESSION['pending_registration'];
        $expiresAt = $pendingUser['expires_at'] ?? time();
        $attempts = $pendingUser['attempts'] ?? 0;
        
        // Si expiré
        if (time() > $expiresAt) {
            $_SESSION['flash_error'] = "Le code a expiré. Veuillez en demander un nouveau.";
        }

        $this->render('auth/verify', [
            'title' => 'Vérification du compte - ' . APP_NAME,
            'csrf_token' => $this->generateCsrfToken(),
            'expires_at' => $expiresAt,
            'attempts' => $attempts
        ]);
    }

    /**
     * Traitement du code de vérification
     */
    public function verifyCode()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('verify');
        }

        if (empty($_SESSION['verify_email']) || empty($_SESSION['pending_registration'])) {
            $this->redirect('register');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('verify');
        }

        $code = $_POST['code'] ?? '';
        $email = $_SESSION['verify_email'];
        
        // Référence pour pouvoir modifier directement dans la session
        $pendingUser = &$_SESSION['pending_registration']; 

        // Vérifier l'expiration
        if (time() > $pendingUser['expires_at']) {
            $_SESSION['flash_error'] = "Le code a expiré. Veuillez en demander un nouveau.";
            $this->redirect('verify');
        }

        if ($pendingUser['verification_code'] === $code) {
            // Code valide : création finale du compte
            $userModel = new User();

            if ($userModel->findByEmail($email)) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé par un compte actif.";
                unset($_SESSION['pending_registration']);
                unset($_SESSION['verify_email']);
                $this->redirect('login');
            }

            // Déterminer si le compte nécessite une approbation
            $needsApproval = in_array($pendingUser['role'], ['pilote', 'recruteur']);

            $userData = [
                'nom' => $pendingUser['nom'],
                'prenom' => $pendingUser['prenom'],
                'email' => $pendingUser['email'],
                'password' => $pendingUser['password'],
                'role' => $pendingUser['role'],
                'is_verified' => 1,
                'verification_code' => null
            ];

            // Si le rôle nécessite une approbation, mettre en attente
            if ($needsApproval) {
                $userData['is_approved'] = 0; // En attente
                $userData['approval_requested_at'] = date('Y-m-d H:i:s');
            }

            $userId = $userModel->create($userData);

            if ($userId) {
                // Notifier les admins si approbation requise
                if ($needsApproval) {
                    $this->notifyAdminsNewApprovalRequest($userId, $pendingUser['nom'], $pendingUser['prenom'], $pendingUser['email'], $pendingUser['role']);
                }

                // Auto-login
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $pendingUser['email'];
                $_SESSION['user_nom'] = $pendingUser['nom'];
                $_SESSION['user_prenom'] = $pendingUser['prenom'];
                session_regenerate_id(true);

                // Nettoyage session
                unset($_SESSION['pending_registration']);
                unset($_SESSION['verify_email']);

                if ($pendingUser['role'] === 'recruteur') {
                    // Recruteur en attente d'approbation
                    $_SESSION['user_role'] = 'etudiant'; // Rôle temporaire
                    $_SESSION['user_role_pending'] = $pendingUser['role'];
                    $_SESSION['user_is_approved'] = false;
                    $_SESSION['flash_success'] = "Compte créé et vérifié avec succès ! Veuillez configurer votre entreprise. Votre compte sera activé après validation par un administrateur.";
                    $this->redirect('recruteur/configurer-entreprise');
                } elseif ($pendingUser['role'] === 'pilote') {
                    // Pilote en attente d'approbation
                    $_SESSION['user_role'] = 'pilote'; // Rôle temporaire avec restrictions
                    $_SESSION['user_role_pending'] = $pendingUser['role'];
                    $_SESSION['user_is_approved'] = false;
                    $_SESSION['flash_success'] = "Compte créé et vérifié avec succès ! Votre demande de compte Pilote est en attente de validation par un administrateur.";
                    $this->redirect('dashboard');
                } else {
                    // Étudiant - accès direct
                    $_SESSION['user_role'] = $pendingUser['role'];
                    $_SESSION['flash_success'] = "Compte créé et vérifié avec succès ! Bienvenue sur votre tableau de bord.";
                    $this->redirect('dashboard');
                }
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la création du compte. Veuillez réessayer.";
                $this->redirect('register');
            }
        } else {
            // Code invalide
            $pendingUser['attempts']++;
            $remaining = 3 - $pendingUser['attempts'];
            
            if ($remaining <= 0) {
                unset($_SESSION['pending_registration']);
                unset($_SESSION['verify_email']);
                $_SESSION['flash_error'] = "Nombre maximum de tentatives atteint. Veuillez recommencer l'inscription.";
                $this->redirect('register');
            }
            
            $_SESSION['flash_error'] = "Code incorrect. Il vous reste $remaining tentative(s).";
            $this->redirect('verify');
        }
    }

    /**
     * Renvoyer le code de vérification
     */
    public function resendCode()
    {
        if (empty($_SESSION['verify_email']) || empty($_SESSION['pending_registration'])) {
            $this->redirect('register');
        }

        $email = $_SESSION['verify_email'];
        $pendingUser = &$_SESSION['pending_registration']; 

        $newCode = (string) rand(100000, 999999);
        $pendingUser['verification_code'] = $newCode;
        $pendingUser['expires_at'] = time() + (15 * 60); // Reset timer
        $pendingUser['attempts'] = 0; // Reset attempts

        if ($this->sendVerificationEmail($email, $pendingUser['prenom'], $newCode)) {
            $_SESSION['flash_success'] = "Un nouveau code a été envoyé.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'email.";
        }
        
        $this->redirect('verify');
    }

    /**
     * Helper pour envoyer l'email de vérification via Brevo
     */
    private function sendVerificationEmail($toEmail, $prenom, $code)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration serveur
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $prenom);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Vérification de votre compte - CESI Stages';
            
            $styles = "
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 30px; text-align: center; color: white; }
                .logo { font-size: 24px; font-weight: bold; letter-spacing: 1px; }
                .content { padding: 40px 30px; text-align: center; color: #333333; }
                .h1 { color: #1e3c72; margin-top: 0; font-size: 24px; }
                .code-box { background-color: #f8f9fa; border: 2px dashed #1e3c72; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1e3c72; padding: 20px; margin: 30px 0; display: inline-block; }
                .text { line-height: 1.6; color: #555555; font-size: 16px; margin-bottom: 20px; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #888888; border-top: 1px solid #eeeeee; }
                .link { color: #1e3c72; text-decoration: none; }
            ";

            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>$styles</style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <div class='logo'>CESI Stages</div>
                        </div>
                        <div class='content'>
                            <h1 class='h1'>Bienvenue, $prenom !</h1>
                            <p class='text'>Nous sommes ravis de vous compter parmi nous. Pour finaliser votre inscription et accéder à toutes nos offres de stage, veuillez utiliser le code de vérification ci-dessous :</p>
                            
                            <div class='code-box'>$code</div>
                            
                            <p class='text'>Ce code est valable pendant 15 minutes. Ne le partagez avec personne.</p>
                        </div>
                        <div class='footer'>
                            <p>Ceci est un message automatique, merci de ne pas y répondre.</p>
                            <p>&copy; " . date('Y') . " CESI Stages. Tous droits réservés.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $mail->AltBody = "Bienvenue $prenom. Votre code de vérification est : $code. Merci de le saisir sur la page de vérification.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log l'erreur si nécessaire : error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Notifier les admins d'une nouvelle demande d'approbation
     */
    private function notifyAdminsNewApprovalRequest($userId, $nom, $prenom, $email, $role)
    {
        $messageModel = new \Models\Message();
        $userModel = new User();
        
        // Récupérer tous les admins
        $admins = $userModel->where('role', 'admin');
        
        $roleLabel = $role === 'pilote' ? 'Pilote' : 'Recruteur';
        $sujet = "Nouvelle demande d'inscription : $roleLabel";
        $contenu = "Bonjour,\n\n";
        $contenu .= "Un nouvel utilisateur souhaite s'inscrire en tant que $roleLabel :\n\n";
        $contenu .= "Nom : $nom\n";
        $contenu .= "Prénom : $prenom\n";
        $contenu .= "Email : $email\n\n";
        $contenu .= "Veuillez vous rendre dans la section 'Approbations' du tableau de bord pour valider ou refuser cette demande.\n\n";
        $contenu .= "Cordialement,\nLe système CesiStages";
        
        foreach ($admins as $admin) {
            $messageModel->envoyer(
                $userId, // Le nouveau utilisateur
                $admin['id'],
                $sujet,
                $contenu
            );
        }
    }

    /**
     * Page de profil utilisateur
     */
    public function profile()
    {
        // Vérification si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        if (!$user) {
            // Si l'utilisateur n'existe pas en base, on le déconnecte
            session_destroy();
            $this->redirect('login');
        }

        // Pour les recruteurs, récupérer les entreprises assignées
        $entreprises = [];
        if ($user['role'] === 'recruteur') {
            $entreprises = $userModel->getEntreprisesByRecruteur($user['id']);
        }

        $this->render('auth/profile', [
            'title' => 'Mon Profil - ' . APP_NAME,
            'user' => $user,
            'entreprises' => $entreprises
        ]);
    }

    /**
     * Affiche le formulaire d'édition du profil
     */
    public function editProfile()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        $this->render('auth/edit-profile', [
            'title' => 'Modifier mon profil - ' . APP_NAME,
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Traite la mise à jour du profil
     */
    public function updateProfile()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erreur de sécurité CSRF");
            }

            $userModel = new User();
            $data = [
                'nom' => htmlspecialchars($_POST['nom']),
                'prenom' => htmlspecialchars($_POST['prenom']),
                'telephone' => htmlspecialchars($_POST['telephone']),
                'age' => !empty($_POST['age']) ? intval($_POST['age']) : null,
                'adresse' => htmlspecialchars($_POST['adresse']),
                'bio' => htmlspecialchars($_POST['bio'])
            ];

            // Mise à jour (le mot de passe n'est pas modifié ici pour simplifier)
            // Pour modifier le mot de passe, il faudrait une méthode dédiée
            
            if ($userModel->update($_SESSION['user_id'], $data)) {
                // Mise à jour de la session
                $_SESSION['user_nom'] = $data['nom'];
                $_SESSION['user_prenom'] = $data['prenom'];
                
                $_SESSION['flash_success'] = "Profil mis à jour avec succès.";
                $this->redirect('profile');
            } else {
                 $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
                 $this->redirect('profile/edit');
            }
        }
    }

    /**
     * Page mot de passe oublié (saisie email)
     */
    public function forgotPassword()
    {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            
            if (empty($email)) {
                $errors[] = "Veuillez entrer votre adresse email.";
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail($email);
                
                // Toujours afficher le même message pour éviter l'énumération
                $_SESSION['flash_success'] = "Si cette adresse existe, un code de vérification vous a été envoyé.";
                
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_expires'] = time() + (5 * 60); // 5 minutes
                unset($_SESSION['reset_verified']);

                if ($user) {
                     $code = (string) rand(100000, 999999);
                     
                     // Stockage en session
                     $_SESSION['reset_code'] = $code;
                     
                     // Envoi email
                     if (!$this->sendPasswordResetEmail($email, $code)) {
                        // En cas d'erreur mail, on peut logger
                        error_log("Echec envoi mail reset pour $email");
                     }
                } else {
                    // Simulation délai
                     $_SESSION['reset_code'] = "INVALID_CODE_" . rand(); // Code impossible à deviner
                    usleep(500000); 
                }
                
                $this->redirect('forgot-password/verify');
            }
        }
        
        $this->render('auth/forgot-password', [
            'title' => 'Mot de passe oublié - ' . APP_NAME,
            'errors' => $errors,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Page de saisie du code de reset
     */
    public function verifyResetCodePage()
    {
        $this->render('auth/verify-reset', [
            'title' => 'Vérification - ' . APP_NAME,
            'errors' => [],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Traitement du code de reset
     */
    public function verifyResetCode()
    {
        $code = $_POST['code'] ?? '';
        $errors = [];
        
        if (empty($code)) {
            $errors[] = "Veuillez entrer le code.";
        }
        
        // Vérification de session
        if (!isset($_SESSION['reset_code']) || !isset($_SESSION['reset_expires'])) {
            $errors[] = "Session expirée ou invalide. Veuillez recommencer.";
        } elseif (time() > $_SESSION['reset_expires']) {
            $errors[] = "Le code a expiré.";
        } elseif ($code !== $_SESSION['reset_code']) {
             $errors[] = "Code incorrect.";
             $attempts = ($_SESSION['reset_attempts'] ?? 0) + 1;
             $_SESSION['reset_attempts'] = $attempts;
             if ($attempts > 3) {
                 unset($_SESSION['reset_code']);
                 $errors[] = "Trop de tentatives. Veuillez recommencer.";
             }
        }
        
        if (empty($errors)) {
            $_SESSION['reset_verified'] = true;
            $this->redirect('forgot-password/reset');
        }
        
        $this->render('auth/verify-reset', [
            'title' => 'Vérification - ' . APP_NAME,
            'errors' => $errors,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Page de nouveau mot de passe
     */
    public function resetPasswordPage()
    {
        if (empty($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
            $this->redirect('forgot-password');
        }
        
        $this->render('auth/reset-password', [
            'title' => 'Nouveau mot de passe - ' . APP_NAME,
            'errors' => [],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Traitement du nouveau mot de passe
     */
    public function resetPassword()
    {
        if (empty($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
            $this->redirect('forgot-password');
        }
        
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $errors = [];
        
        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (strlen($password) < 8 || 
            !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || 
            !preg_match('/[0-9]/', $password) || 
            !preg_match('/[\W_]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }
        
        if (empty($errors)) {
            $userModel = new User();
            $user = $userModel->findByEmail($_SESSION['reset_email']);
            
            if ($user) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                
                // On met à jour le mot de passe ET on valide le compte (car email vérifié par le code)
                $userModel->update($user['id'], [
                    'password' => $hash,
                    'is_verified' => 1
                ]);
                
                // Nettoyage
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_code']);
                unset($_SESSION['reset_expires']);
                unset($_SESSION['reset_verified']);
                
                $_SESSION['flash_success'] = "Mot de passe modifié avec succès. Connectez-vous.";
                $this->redirect('login');
            } else {
                $errors[] = "Erreur utilisateur introuvable.";
            }
        }
        
        $this->render('auth/reset-password', [
            'title' => 'Nouveau mot de passe - ' . APP_NAME,
            'errors' => $errors,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    private function sendPasswordResetEmail($toEmail, $code)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration serveur
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST; 
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation mot de passe - CESI Stages';
            
            $styles = "
                body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; }
                .header { background: #1e3c72; padding: 20px; color: white; text-align: center; }
                .content { padding: 30px; text-align: center; color: #333; }
                .code { background: #f8f9fa; border: 2px dashed #1e3c72; padding: 15px; font-size: 24px; letter-spacing: 5px; margin: 20px 0; display: inline-block; font-weight: bold; color: #1e3c72; }
            ";
            
            $mail->Body = "
                <!DOCTYPE html><html><head><meta charset='UTF-8'><style>$styles</style></head><body>
                <div class='container'>
                    <div class='header'><h1>CESI Stages</h1></div>
                    <div class='content'>
                        <h2>Réinitialisation de mot de passe</h2>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code :</p>
                        <div class='code'>$code</div>
                        <p>Ce code expire dans 5 minutes.</p>
                        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.</p>
                    </div>
                </div>
                </body></html>
            ";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            // error_log("Mail Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Suppression du compte utilisateur (par l'utilisateur lui-même)
     */
    public function deleteAccount()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        // Vérification CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Token de sécurité invalide.";
            $this->redirect('profile');
            return;
        }

        // Vérification du mot de passe pour confirmer
        $password = $_POST['password'] ?? '';
        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = "Mot de passe incorrect. La suppression a été annulée.";
            $this->redirect('profile');
            return;
        }

        // Empêcher la suppression du dernier admin
        if ($user['role'] === 'admin') {
            $adminCount = count($userModel->where('role', 'admin'));
            if ($adminCount <= 1) {
                $_SESSION['flash_error'] = "Impossible de supprimer le dernier compte administrateur.";
                $this->redirect('profile');
                return;
            }
        }

        $userId = $_SESSION['user_id'];

        // Supprimer le compte
        $userModel->delete($userId);

        // Déconnexion
        session_destroy();

        // Redirection avec message
        session_start();
        $_SESSION['flash_success'] = "Votre compte a été supprimé avec succès.";
        $this->redirect('');
    }
}
