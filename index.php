<?php
// ============================================================
// COSMOS BEACH — Point d'entrée principal (index.php) — v3.0
// Corrections : headers sécurité, session sécurisée, gestion
// d'erreurs propre, routes manquantes ajoutées.
// ============================================================

// --- 1. Headers de sécurité HTTP (avant tout output) ---
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), camera=(), microphone=()");
header("Content-Security-Policy: default-src 'self'; "
     . "script-src 'self' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://fonts.googleapis.com 'unsafe-inline'; "
     . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
     . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
     . "img-src 'self' https://images.unsplash.com data: blob:; "
     . "connect-src 'self';");

// --- 2. Session sécurisée ---
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
// cookie_secure activé plus bas après détection de l'environnement

session_start();

// Expiration automatique de session après 1h d'inactivité
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['flash_error'] = "Votre session a expiré. Veuillez vous reconnecter.";
}
$_SESSION['last_activity'] = time();

// --- 3. Constantes globales ---
// BASE_URL = '' en production Hostinger (site à la racine du domaine)
// BASE_URL = '/Application-Defense' en local XAMPP
$is_production = (getenv('APP_BASE_URL') && !str_contains(getenv('APP_BASE_URL'), 'localhost'));
define('BASE_URL',  $is_production ? '' : '/Application-Defense');
define('APP_ENV',   $is_production ? 'production' : 'development');

// Masquer les erreurs en production
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('session.cookie_secure', 1); // HTTPS obligatoire en prod
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    ini_set('session.cookie_secure', 0); // Pas de HTTPS en local
}

// --- 4. Chargement sécurité + CSRF ---
require_once 'backend/config/Security.php';
Security::generateCSRFToken();

// --- 5. Autoloader ---
spl_autoload_register(function (string $class_name): void {
    $paths = [
        'controllers/' . $class_name . '.php',
        'models/'      . $class_name . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// --- 6. Routeur ---
$action = isset($_GET['action']) ? trim($_GET['action']) : 'home';

// Whitelist des actions autorisées (sécurité contre les routes inattendues)
$allowed_actions = [
    'home','login','register','logout',
    'forgot_password','verify_otp','reset_password',
    'activities','activity_details',
    'book_activity','process_payment','online_checkout',
    'process_online_payment','receipt',
    'submit_reclamation',
    'admin_activities','marketing_dashboard','events',
    'dashboard','reports',
    'admin_dashboard','admin_users','toggle_user_status',
    'admin_login_history','admin_audit_logs','admin_roles',
    'update_role_permissions','export_data',
    'cashier_search', 'search_payments', 'mon_historique',
    'admin_manage_permissions', 'update_user_permissions', 'activity_history',
    'marketing_create', 'marketing_edit', 'marketing_toggle', 'marketing_interactions',
    'book_ticket_event', 'my_event_tickets', 'cancel_event_ticket',
];

if (!in_array($action, $allowed_actions)) {
    http_response_code(404);
    require 'views/errors/404.php';
    exit;
}

try {
    switch ($action) {

        // --- Pages publiques ---
        case 'home':
            $controller = new HomeController();
            $controller->index();
            break;

        case 'activities':
            $controller = new ActivityController();
            $controller->list();
            break;

        case 'activity_details':
            $controller = new ActivityController();
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $controller->details($id);
            break;

        case 'events':
            require_once 'models/EventModel.php';
            $controller = new MarketingController();
            $controller->list_public();
            break;

        // --- Authentification ---
        case 'login':
            $controller = new AuthController();
            $controller->login();
            break;

        case 'register':
            $controller = new AuthController();
            $controller->register();
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;

        case 'forgot_password':
            $controller = new AuthController();
            $controller->forgot_password();
            break;

        case 'verify_otp':
            $controller = new AuthController();
            $controller->verify_otp();
            break;

        case 'reset_password':
            $controller = new AuthController();
            $controller->reset_password();
            break;

        // --- Réservations & Paiements ---
        case 'book_activity':
            $controller = new ReservationController();
            $controller->book_activity();
            break;

        case 'process_payment':
            $controller = new PaymentController();
            $controller->process();
            break;

        case 'online_checkout':
            $controller = new PaymentController();
            $controller->online_checkout();
            break;

        case 'process_online_payment':
            $controller = new PaymentController();
            $controller->process_online();
            break;

        case 'receipt':
            $controller = new PaymentController();
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $controller->receipt($id);
            break;

        // --- Caisse ---
        case 'cashier_search':
            $controller = new PaymentController();
            $controller->cashier_search();
            break;

        case 'search_payments':
            $controller = new PaymentController();
            $controller->search_payments();
            break;

        // --- Réclamations ---
        case 'submit_reclamation':
            require_once 'models/Reclamation.php';
            $controller = new ReclamationController();
            $controller->submit();
            break;

        // --- Dashboards ---
        case 'dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;

        case 'reports':
            $controller = new ReportController();
            $controller->inventory_report();
            break;

        // --- Admin ---
        case 'admin_dashboard':
            $controller = new AdminController();
            $controller->dashboard();
            break;

        case 'admin_users':
            $controller = new AdminController();
            $controller->manageUsers();
            break;

        case 'toggle_user_status':
            $controller = new AdminController();
            $controller->toggleUserStatus();
            break;

        case 'admin_login_history':
            $controller = new AdminController();
            $controller->loginHistory();
            break;

        case 'admin_audit_logs':
            $controller = new AdminController();
            $controller->auditLogs();
            break;

        case 'admin_roles':
            $controller = new AdminController();
            $controller->manageRoles();
            break;

        case 'update_role_permissions':
            $controller = new AdminController();
            $controller->updateRolePermissions();
            break;

        case 'export_data':
            $controller = new AdminController();
            $controller->exportData();
            break;

        case 'admin_manage_permissions':
            $controller = new AdminController();
            $controller->managePermissions();
            break;

        case 'update_user_permissions':
            $controller = new AdminController();
            $controller->updateUserPermissions();
            break;

        case 'activity_history':
            $controller = new AdminController();
            $controller->activityHistory();
            break;

        case 'mon_historique':
            $controller = new HistoryController();
            $controller->index();
            break;

        // --- Marketing ---
        case 'admin_activities':
            require_once 'controllers/AdminActivityController.php';
            $controller = new AdminActivityController();
            $controller->manage();
            break;

        case 'marketing_dashboard':
            require_once 'models/EventModel.php';
            require_once 'models/EventTicket.php';
            $controller = new MarketingController();
            $controller->dashboard();
            break;

        case 'marketing_create':
            require_once 'models/EventModel.php';
            $controller = new MarketingController();
            $controller->create();
            break;

        case 'marketing_edit':
            require_once 'models/EventModel.php';
            $controller = new MarketingController();
            $controller->edit();
            break;

        case 'marketing_toggle':
            require_once 'models/EventModel.php';
            $controller = new MarketingController();
            $controller->delete();
            break;

        case 'marketing_interactions':
            require_once 'models/EventModel.php';
            require_once 'models/EventTicket.php';
            $controller = new MarketingController();
            $controller->interactions();
            break;

        case 'book_ticket_event':
            require_once 'models/EventModel.php';
            require_once 'models/EventTicket.php';
            $controller = new MarketingController();
            $controller->book_ticket();
            break;

        case 'my_event_tickets':
            require_once 'models/EventTicket.php';
            $controller = new MarketingController();
            $controller->my_tickets();
            break;

        case 'cancel_event_ticket':
            require_once 'models/EventTicket.php';
            $controller = new MarketingController();
            $controller->cancel_ticket();
            break;
    }

} catch (Throwable $e) {
    // Logger l'erreur complète sans l'exposer à l'utilisateur
    error_log(sprintf(
        "[COSMOS] %s — %s in %s:%d\nStack: %s",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));

    if (APP_ENV === 'development') {
        // En développement : afficher le détail (jamais en production)
        echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:20px;margin:20px;border-radius:8px;">';
        echo '<strong style="color:#f38ba8;">Erreur Application</strong>' . "\n\n";
        echo htmlspecialchars($e->getMessage()) . "\n";
        echo htmlspecialchars($e->getFile() . ':' . $e->getLine()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        require 'views/errors/500.php';
    }
    exit;
}