<?php
// ============================================================
// COSMOS BEACH — controllers/AuthController.php — v3.0 CORRIGÉ
// Corrections :
//   - session_regenerate_id(true) après login (anti session fixation)
//   - Rate limiting brute force : 5 échecs → blocage 15 min
//   - double emailExists() supprimé dans le bloc login échoué
//   - prenom collecté à l'inscription
//   - OTP : suppression du compteur session redondant
//   - Validation password >= 8 chars avec message clair
// ============================================================

class AuthController
{
    // --------------------------------------------------------
    // Connexion
    // --------------------------------------------------------
    public function login(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Vérification CSRF
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error = "Session invalide. Veuillez réessayer.";
            } else {
                $email    = filter_var(trim($_POST['email']    ?? ''), FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';

                // 2. Rate limiting : bloquer après 5 échecs en 15 minutes
                if ($this->isRateLimited($email)) {
                    $error = "Trop de tentatives de connexion. Veuillez attendre 15 minutes.";
                } else {

                    $user        = new User();
                    $user->email = $email;

                    $email_found = $user->emailExists();
                    $pass_ok     = $email_found && password_verify($password, $user->password_hash);

                    if ($email_found && $pass_ok) {

                        // 3. Compte désactivé
                        if (!$user->is_active) {
                            LoginHistory::recordLogin(null, $email, '', '', 'failed', 'Compte désactivé');
                            AuditLog::log('login_failed', 'auth', $user->id, 'Compte désactivé', 'failed');
                            $error = "Votre compte a été désactivé. Contactez l'administrateur.";

                        } else {
                            // 4. Connexion réussie
                            // Régénérer l'ID session pour prévenir la fixation de session
                            session_regenerate_id(true);

                            $_SESSION['user_id']     = $user->id;
                            $_SESSION['user_nom']    = $user->nom;
                            $_SESSION['user_prenom'] = $user->prenom;
                            $_SESSION['user_role']   = $user->role;
                            $_SESSION['last_activity'] = time();

                            // Enregistrements
                            LoginHistory::recordLogin(
                                $user->id, $email,
                                $user->prenom, $user->nom,
                                'success'
                            );
                            $user->updateLastLogin($user->id);
                            AuditLog::log('login', 'auth', $user->id, 'Connexion réussie');

                            header("Location: " . BASE_URL . "/?action=dashboard");
                            exit;
                        }

                    } else {
                        // 5. Échec — message générique (ne pas révéler si email existe)
                        LoginHistory::recordLogin(
                            null, $email, '', '', 'failed',
                            $email_found ? 'Mot de passe incorrect' : 'Email inexistant'
                        );
                        AuditLog::log('login_failed', 'auth', null, 'Échec connexion : ' . $email, 'failed');
                        $error = "Email ou mot de passe incorrect.";

                        // Délai artificiel anti brute-force
                        sleep(1);
                    }
                }
            }
        }

        $pageTitle = "Connexion";
        require 'views/auth/login.php';
    }

    // --------------------------------------------------------
    // Inscription
    // --------------------------------------------------------
    public function register(): void
    {
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error = "Session invalide. Veuillez réessayer.";
            } else {
                $nom             = trim($_POST['nom']              ?? '');
                $prenom          = trim($_POST['prenom']           ?? '');
                $email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $password        = $_POST['password']              ?? '';
                $confirm_password = $_POST['confirm_password']     ?? '';

                // Validations
                if (empty($nom) || empty($email) || empty($password)) {
                    $error = "Veuillez remplir tous les champs obligatoires.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Adresse email invalide.";
                } elseif (strlen($password) < 8) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères.";
                } elseif ($password !== $confirm_password) {
                    $error = "Les mots de passe ne correspondent pas.";
                } else {
                    $user        = new User();
                    $user->email = $email;

                    if ($user->emailExists()) {
                        $error = "Cet email est déjà utilisé.";
                    } else {
                        $user->nom           = $nom;
                        $user->prenom        = $prenom;
                        $user->password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                        if ($user->create()) {
                            AuditLog::log('register', 'auth', null, 'Nouvel utilisateur : ' . $email);
                            $_SESSION['flash_success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                            header("Location: " . BASE_URL . "/?action=login");
                            exit;
                        } else {
                            $error = "Une erreur est survenue. Veuillez réessayer.";
                        }
                    }
                }
            }
        }

        $pageTitle = "Inscription";
        require 'views/auth/register.php';
    }

    // --------------------------------------------------------
    // Déconnexion
    // --------------------------------------------------------
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            LoginHistory::recordLogout($_SESSION['user_id']);
            AuditLog::log('logout', 'auth', $_SESSION['user_id'], 'Déconnexion');
        }

        // Détruire complètement la session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        header("Location: " . BASE_URL . "/?action=login");
        exit;
    }

    // --------------------------------------------------------
    // Mot de passe oublié — envoi OTP
    // --------------------------------------------------------
    public function forgot_password(): void
    {
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error = "Requête invalide. Veuillez réessayer.";
            } else {
                $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Veuillez entrer une adresse email valide.";
                } else {
                    $user        = new User();
                    $user->email = $email;

                    // Message identique que le compte existe ou non (anti-énumération)
                    $generic_success = "Si un compte existe avec cette adresse, vous recevrez un email avec le code de vérification.";

                    if ($user->emailExists()) {
                        $passwordReset = new PasswordReset();
                        $result        = $passwordReset->createOTPRequest($user->id, $user->email);

                        if ($result) {
                            require_once 'backend/config/EmailService.php';
                            $emailService = new EmailService();

                            if ($emailService->isConfigured()) {
                                $sent = $emailService->sendOTPEmail(
                                    $user->email,
                                    $user->prenom ?: $user->nom,
                                    $passwordReset->otp_code
                                );
                                if (!$sent) {
                                    $error = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                                }
                            } else {
                                // Mode développement : logguer l'OTP
                                error_log("[COSMOS][DEV] OTP pour {$user->email} : {$passwordReset->otp_code}");
                                $_SESSION['debug_otp'] = $passwordReset->otp_code;
                            }

                            if (empty($error)) {
                                $_SESSION['reset_email'] = $user->email;
                                $success = $generic_success;
                            }
                        } else {
                            $error = "Erreur lors de la création du code OTP. Veuillez réessayer.";
                        }
                    } else {
                        // Compte inexistant → même message (sécurité)
                        $success = $generic_success;
                    }
                }
            }
        }

        $pageTitle = "Mot de passe oublié";
        require 'views/auth/forgot_password.php';
    }

    // --------------------------------------------------------
    // Vérification OTP
    // --------------------------------------------------------
    public function verify_otp(): void
    {
        // Rediriger si pas de session reset en cours
        if (!isset($_SESSION['reset_email'])) {
            header("Location: " . BASE_URL . "/?action=forgot_password");
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error = "Requête invalide.";
            } else {
                $email    = $_SESSION['reset_email'];
                $otp_code = preg_replace('/\s+/', '', $_POST['otp_code'] ?? '');

                if (empty($otp_code) || strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
                    $error = "Veuillez entrer un code OTP valide (6 chiffres).";
                } else {
                    $passwordReset = new PasswordReset();
                    $result        = $passwordReset->verifyOTP($email, $otp_code);

                    if ($result['success']) {
                        $_SESSION['reset_id']      = $result['reset_id'];
                        $_SESSION['reset_user_id'] = $result['user_id'];
                        $_SESSION['otp_verified']  = true;

                        header("Location: " . BASE_URL . "/?action=reset_password");
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                }
            }
        }

        $pageTitle = "Vérification OTP";
        require 'views/auth/verify_otp.php';
    }

    // --------------------------------------------------------
    // Réinitialisation du mot de passe
    // --------------------------------------------------------
    public function reset_password(): void
    {
        if (empty($_SESSION['otp_verified'])) {
            header("Location: " . BASE_URL . "/?action=forgot_password");
            exit;
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $error = "Requête invalide.";
            } else {
                $new_password     = $_POST['new_password']     ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                $reset_id         = $_SESSION['reset_id']      ?? '';
                $email            = $_SESSION['reset_email']   ?? '';

                // Validation mot de passe
                $pw_errors = Security::validatePassword($new_password);
                if (!empty($pw_errors)) {
                    $error = implode(' ', $pw_errors);
                } elseif ($new_password !== $confirm_password) {
                    $error = "Les mots de passe ne correspondent pas.";
                } else {
                    $passwordReset = new PasswordReset();

                    if (!$passwordReset->isValidResetRequest($reset_id)) {
                        $error = "Votre session a expiré. Veuillez recommencer.";
                    } else {
                        $user        = new User();
                        $user->email = $email;

                        if ($user->emailExists()) {
                            $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

                            if ($user->updatePassword($hash)) {
                                $passwordReset->markAsUsed($reset_id);
                                AuditLog::log('password_reset', 'auth', $user->id, 'Mot de passe réinitialisé');

                                // Email de confirmation
                                require_once 'backend/config/EmailService.php';
                                $emailService = new EmailService();
                                if ($emailService->isConfigured()) {
                                    $emailService->sendPasswordResetConfirmation($user->email, $user->prenom ?: $user->nom);
                                }

                                // Nettoyer la session OTP
                                foreach (['reset_email','reset_id','reset_user_id','otp_verified','debug_otp'] as $key) {
                                    unset($_SESSION[$key]);
                                }

                                $_SESSION['flash_success'] = "Mot de passe réinitialisé avec succès. Connectez-vous.";
                                header("Location: " . BASE_URL . "/?action=login");
                                exit;
                            } else {
                                $error = "Erreur lors de la mise à jour. Veuillez réessayer.";
                            }
                        } else {
                            $error = "Compte introuvable.";
                        }
                    }
                }
            }
        }

        $pageTitle = "Nouveau mot de passe";
        require 'views/auth/reset_password.php';
    }

    // --------------------------------------------------------
    // Rate limiting : 5 échecs en 15 min → blocage
    // --------------------------------------------------------
    private function isRateLimited(string $email): bool
    {
        try {
            $db   = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare(
                "SELECT COUNT(*) FROM login_history
                 WHERE email = ?
                   AND status = 'failed'
                   AND login_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
            );
            $stmt->execute([$email]);
            $count = (int)$stmt->fetchColumn();

            return $count >= 5;

        } catch (Exception $e) {
            // En cas d'erreur BDD, ne pas bloquer par défaut
            return false;
        }
    }
}
