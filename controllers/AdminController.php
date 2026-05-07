<?php
// ============================================================
// COSMOS BEACH — controllers/AdminController.php — v3.1
// Corrections :
//   - Connexion BDD singleton ($this->conn) — plus d'instanciations multiples
//   - CSRF ajouté sur toggleUserStatus, updateRolePermissions, updateUserPermissions
//   - manageRoles() vérifie d'abord checkAdminAccess()
//   - activityHistory() implémentée complètement
//   - sendCSV() sécurisé si $data est vide
//   - managePermissions() : DIRECTEUR limité à AGENT/CAISSIER (ses employés)
//   - user_permissions table créée si absente (lazy init)
//   - Recherche aussi sur prenom dans getUsersWithFilters
// ============================================================

class AdminController
{
    private $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // ──────────────────────────────────────────────────────────
    // Vérification accès admin (SUPER_ADMIN ou DIRECTEUR)
    // ──────────────────────────────────────────────────────────
    private function checkAdminAccess(): void
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }
        if (!in_array($_SESSION['user_role'], ['SUPER_ADMIN', 'DIRECTEUR'], true)) {
            http_response_code(403);
            require 'views/errors/403.php';
            exit;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Dashboard admin
    // ──────────────────────────────────────────────────────────
    public function dashboard(): void
    {
        $this->checkAdminAccess();

        $users_count      = $this->getUsersCount();
        $active_users     = $this->getActiveUsersCount();
        $recent_logins    = $this->getRecentLogins(5);
        $suspicious_logins = $this->getSuspiciousLogins(5);
        $recent_activities = $this->getRecentActivities(10);

        AuditLog::log('access_admin_dashboard', 'admin', null, 'Accès au tableau de bord admin');

        $pageTitle = "Tableau de Bord Admin";
        require 'views/admin/dashboard.php';
    }

    // ──────────────────────────────────────────────────────────
    // Gestion des utilisateurs
    // ──────────────────────────────────────────────────────────
    public function manageUsers(): void
    {
        $this->checkAdminAccess();

        $search        = trim($_GET['search']  ?? '');
        $role_filter   = trim($_GET['role']    ?? '');
        $status_filter = $_GET['status']       ?? '';
        $page          = max(1, intval($_GET['page'] ?? 1));
        $limit         = 20;
        $offset        = ($page - 1) * $limit;

        $filters = [];
        if ($search !== '')        $filters['search']    = $search;
        if ($role_filter !== '')   $filters['role']      = $role_filter;
        if ($status_filter !== '') $filters['is_active'] = $status_filter === '1' ? 1 : 0;

        // DIRECTEUR : voit seulement ses employés
        if ($_SESSION['user_role'] === 'DIRECTEUR') {
            $filters['roles_allowed'] = ['AGENT', 'CAISSIER'];
        }

        $users       = $this->getUsersWithFilters($filters, $limit, $offset);
        $total_users = $this->countUsersWithFilters($filters);
        $total_pages = max(1, ceil($total_users / $limit));

        AuditLog::log('view_users_list', 'users', null, 'Consultation liste utilisateurs');

        $pageTitle = "Gestion des Utilisateurs";
        require 'views/admin/manage_users.php';
    }

    // ──────────────────────────────────────────────────────────
    // Activer / Désactiver un utilisateur
    // ──────────────────────────────────────────────────────────
    public function toggleUserStatus(): void
    {
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=admin_users");
            exit;
        }

        // CSRF
        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide (CSRF).";
            header("Location: " . BASE_URL . "/?action=admin_users");
            exit;
        }

        $user_id = intval($_POST['user_id'] ?? 0);
        $action  = $_POST['action']          ?? '';
        $reason  = trim($_POST['reason']     ?? '');

        // Ne pas se désactiver soi-même
        if ($user_id === (int)$_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Vous ne pouvez pas modifier votre propre compte.";
            header("Location: " . BASE_URL . "/?action=admin_users");
            exit;
        }

        // DIRECTEUR : ne peut agir que sur AGENT/CAISSIER
        if ($_SESSION['user_role'] === 'DIRECTEUR') {
            $stmt = $this->conn->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $target = $stmt->fetch();
            if (!$target || !in_array($target['role'], ['AGENT', 'CAISSIER'], true)) {
                $_SESSION['flash_error'] = "Action non autorisée pour ce rôle.";
                header("Location: " . BASE_URL . "/?action=admin_users");
                exit;
            }
        }

        try {
            if ($action === 'deactivate') {
                $stmt = $this->conn->prepare(
                    "UPDATE users SET is_active=0, disabled_at=NOW(), disabled_reason=? WHERE id=?"
                );
                $stmt->execute([$reason, $user_id]);
                AuditLog::log('deactivate_user', 'users', $user_id, 'Désactivation' . ($reason ? ': ' . $reason : ''));
                $_SESSION['flash_success'] = "Utilisateur désactivé.";

            } elseif ($action === 'activate') {
                $stmt = $this->conn->prepare(
                    "UPDATE users SET is_active=1, disabled_at=NULL, disabled_reason=NULL WHERE id=?"
                );
                $stmt->execute([$user_id]);
                AuditLog::log('activate_user', 'users', $user_id, 'Réactivation');
                $_SESSION['flash_success'] = "Utilisateur réactivé.";

            } else {
                $_SESSION['flash_error'] = "Action invalide.";
            }
        } catch (Exception $e) {
            error_log("[COSMOS] toggleUserStatus: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
        }

        header("Location: " . BASE_URL . "/?action=admin_users");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Historique des connexions
    // ──────────────────────────────────────────────────────────
    public function loginHistory(): void
    {
        $this->checkAdminAccess();

        $user_filter = intval($_GET['user_id'] ?? 0);
        $page        = max(1, intval($_GET['page'] ?? 1));
        $limit       = 50;
        $offset      = ($page - 1) * $limit;

        $history = new LoginHistory();

        if ($user_filter > 0) {
            $logins = $this->getUserLoginHistory($user_filter, $limit, $offset);
        } else {
            $logins = $history->getAll($limit, $offset);
        }

        $total_logins    = $history->count();
        $total_pages     = max(1, ceil($total_logins / $limit));
        $suspicious_logins = $history->getSuspiciousLogins();

        AuditLog::log('view_login_history', 'audit', null, 'Consultation historique connexions');

        $pageTitle = "Historique des Connexions";
        require 'views/admin/login_history.php';
    }

    // ──────────────────────────────────────────────────────────
    // Journaux d'audit
    // ──────────────────────────────────────────────────────────
    public function auditLogs(): void
    {
        $this->checkAdminAccess();

        $action_filter = trim($_GET['action']     ?? '');
        $user_filter   = intval($_GET['user_id']  ?? 0);
        $start_date    = trim($_GET['start_date'] ?? '');
        $end_date      = trim($_GET['end_date']   ?? '');
        $page          = max(1, intval($_GET['page'] ?? 1));
        $limit         = 100;
        $offset        = ($page - 1) * $limit;

        $audit   = new AuditLog();
        $filters = [];
        if ($action_filter) $filters['action']     = $action_filter;
        if ($user_filter)   $filters['user_id']    = $user_filter;
        if ($start_date)    $filters['start_date'] = $start_date . ' 00:00:00';
        if ($end_date)      $filters['end_date']   = $end_date   . ' 23:59:59';
        $filters['limit']  = $limit;
        $filters['offset'] = $offset;

        $logs        = $audit->getFiltered($filters);
        $total_logs  = $audit->count();
        $total_pages = max(1, ceil($total_logs / $limit));

        AuditLog::log('view_audit_logs', 'audit', null, 'Consultation journaux d\'audit');

        $pageTitle = "Journaux d'Audit";
        require 'views/admin/audit_logs.php';
    }

    // ──────────────────────────────────────────────────────────
    // Gestion des rôles (SUPER_ADMIN seulement)
    // ──────────────────────────────────────────────────────────
    public function manageRoles(): void
    {
        $this->checkAdminAccess(); // vérifie d'abord l'auth
        if ($_SESSION['user_role'] !== 'SUPER_ADMIN') {
            http_response_code(403);
            require 'views/errors/403.php';
            exit;
        }

        $permission      = new Permission();
        $permissions     = $permission->getAll();
        $roles           = ['SUPER_ADMIN', 'DIRECTEUR', 'AGENT', 'CAISSIER', 'VISITEUR', 'MARKETEUR'];
        $role_permissions = [];
        foreach ($roles as $role) {
            $role_permissions[$role] = $permission->getPermissionsByRole($role);
        }

        AuditLog::log('view_roles_permissions', 'permissions', null, 'Consultation gestion des rôles');

        $pageTitle = "Gestion des Rôles et Permissions";
        require 'views/admin/manage_roles.php';
    }

    // ──────────────────────────────────────────────────────────
    // Mettre à jour les permissions d'un rôle (SUPER_ADMIN)
    // ──────────────────────────────────────────────────────────
    public function updateRolePermissions(): void
    {
        $this->checkAdminAccess();
        if ($_SESSION['user_role'] !== 'SUPER_ADMIN') {
            http_response_code(403); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=admin_roles"); exit;
        }

        // CSRF
        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide.";
            header("Location: " . BASE_URL . "/?action=admin_roles"); exit;
        }

        $allowed_roles = ['SUPER_ADMIN', 'DIRECTEUR', 'AGENT', 'CAISSIER', 'VISITEUR', 'MARKETEUR'];
        $role          = $_POST['role'] ?? '';
        if (!in_array($role, $allowed_roles, true)) {
            $_SESSION['flash_error'] = "Rôle invalide.";
            header("Location: " . BASE_URL . "/?action=admin_roles"); exit;
        }

        $permissions = array_map('intval', $_POST['permissions'] ?? []);

        try {
            $this->conn->prepare("DELETE FROM role_permissions WHERE role = ?")->execute([$role]);
            $permission = new Permission();
            foreach ($permissions as $perm_id) {
                if ($perm_id > 0) $permission->assignPermissionToRole($role, $perm_id);
            }
            AuditLog::log('update_role_permissions', 'permissions', null, "Permissions rôle $role mises à jour");
            $_SESSION['flash_success'] = "Permissions du rôle $role mises à jour.";
        } catch (Exception $e) {
            error_log("[COSMOS] updateRolePermissions: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
        }

        header("Location: " . BASE_URL . "/?action=admin_roles");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Gestion des permissions individuelles par utilisateur
    // SUPER_ADMIN → tous les utilisateurs
    // DIRECTEUR   → uniquement ses employés (AGENT, CAISSIER)
    // ──────────────────────────────────────────────────────────
    public function managePermissions(): void
    {
        $this->checkAdminAccess();

        $query  = "SELECT id, nom, prenom, email, role FROM users WHERE 1=1";
        $params = [];

        if ($_SESSION['user_role'] === 'DIRECTEUR') {
            $query .= " AND role IN ('AGENT','CAISSIER')";
        } else {
            // SUPER_ADMIN voit tout sauf lui-même (optionnel)
            // $query .= " AND id != ?"; $params[] = $_SESSION['user_id'];
        }
        $query .= " ORDER BY role DESC, nom ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $permission     = new Permission();
        $all_permissions = $permission->getAll();

        // Permissions actuelles par utilisateur (table user_permissions)
        $user_permissions = [];
        $this->ensureUserPermissionsTable();

        foreach ($users as $user) {
            $stmt2 = $this->conn->prepare(
                "SELECT p.* FROM permissions p
                 JOIN user_permissions up ON p.id = up.permission_id
                 WHERE up.user_id = ?"
            );
            $stmt2->execute([$user['id']]);
            $user_permissions[$user['id']] = $stmt2->fetchAll();
        }

        AuditLog::log('view_user_permissions', 'permissions', null, 'Consultation gestion permissions utilisateurs');

        $pageTitle = $_SESSION['user_role'] === 'DIRECTEUR'
            ? "Droits de mes Employés"
            : "Gestion des Droits d'Administration";
        require 'views/admin/manage_user_permissions.php';
    }

    // ──────────────────────────────────────────────────────────
    // Mettre à jour les permissions d'un utilisateur
    // ──────────────────────────────────────────────────────────
    public function updateUserPermissions(): void
    {
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=admin_manage_permissions"); exit;
        }

        // CSRF
        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide.";
            header("Location: " . BASE_URL . "/?action=admin_manage_permissions"); exit;
        }

        $user_id     = intval($_POST['user_id'] ?? 0);
        $permissions = array_map('intval', $_POST['permissions'] ?? []);

        if ($user_id <= 0) {
            $_SESSION['flash_error'] = "Utilisateur invalide.";
            header("Location: " . BASE_URL . "/?action=admin_manage_permissions"); exit;
        }

        // Vérifier le rôle cible
        $stmt = $this->conn->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $target_user = $stmt->fetch();

        if (!$target_user) {
            $_SESSION['flash_error'] = "Utilisateur introuvable.";
            header("Location: " . BASE_URL . "/?action=admin_manage_permissions"); exit;
        }

        // DIRECTEUR ne peut gérer que AGENT et CAISSIER
        if ($_SESSION['user_role'] === 'DIRECTEUR'
            && !in_array($target_user['role'], ['AGENT', 'CAISSIER'], true)) {
            http_response_code(403);
            $_SESSION['flash_error'] = "Vous ne pouvez pas modifier les droits de ce rôle.";
            header("Location: " . BASE_URL . "/?action=admin_manage_permissions"); exit;
        }

        try {
            $this->ensureUserPermissionsTable();
            $this->conn->prepare("DELETE FROM user_permissions WHERE user_id = ?")->execute([$user_id]);
            $stmt = $this->conn->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (?,?)");
            foreach ($permissions as $perm_id) {
                if ($perm_id > 0) $stmt->execute([$user_id, $perm_id]);
            }
            AuditLog::log('update_user_permissions', 'permissions', $user_id, 'Droits utilisateur mis à jour');
            $_SESSION['flash_success'] = "Droits mis à jour avec succès.";
        } catch (Exception $e) {
            error_log("[COSMOS] updateUserPermissions: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de la mise à jour.";
        }

        header("Location: " . BASE_URL . "/?action=admin_manage_permissions");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Historique des activités (création/modification/suppression)
    // Accessible SUPER_ADMIN et DIRECTEUR
    // ──────────────────────────────────────────────────────────
    public function activityHistory(): void
    {
        $this->checkAdminAccess();

        $activity_id  = intval($_GET['activity_id'] ?? 0);
        $action_filter = trim($_GET['action']       ?? '');
        $start_date   = trim($_GET['start_date']    ?? '');
        $page         = max(1, intval($_GET['page'] ?? 1));
        $limit        = 30;
        $offset       = ($page - 1) * $limit;

        // Requête avec nom, prénom, date, heure et rôle du responsable
        $query  = "SELECT ah.*,
                          u.nom      AS nom,
                          u.prenom   AS prenom,
                          u.email    AS email,
                          u.role     AS role,
                          a.nom      AS activite_nom
                   FROM activity_history ah
                   LEFT JOIN users      u ON ah.user_id     = u.id
                   LEFT JOIN activities a ON ah.activity_id = a.id
                   WHERE 1=1";
        $params = [];

        if ($activity_id > 0) {
            $query  .= " AND ah.activity_id = ?";
            $params[] = $activity_id;
        }
        if ($action_filter) {
            $query  .= " AND ah.action = ?";
            $params[] = $action_filter;
        }
        if ($start_date) {
            $query  .= " AND DATE(ah.created_at) >= ?";
            $params[] = $start_date;
        }

        // DIRECTEUR : limite aux actions faites par ses employés
        if ($_SESSION['user_role'] === 'DIRECTEUR') {
            $query .= " AND (u.role IN ('AGENT','CAISSIER','DIRECTEUR') OR ah.user_id = ?)";
            $params[] = $_SESSION['user_id'];
        }

        $query .= " ORDER BY ah.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $history = $stmt->fetchAll();

        // Total
        $count_query = str_replace(
            "SELECT ah.*,\n                          u.nom      AS nom,\n                          u.prenom   AS prenom,\n                          u.email    AS email,\n                          u.role     AS role,\n                          a.nom      AS activite_nom",
            "SELECT COUNT(*)",
            $query
        );
        // Reconstruire proprement le COUNT
        $count_params = array_slice($params, 0, -2); // sans limit et offset
        $count_sql = "SELECT COUNT(*) FROM activity_history ah
                      LEFT JOIN users u ON ah.user_id = u.id
                      LEFT JOIN activities a ON ah.activity_id = a.id
                      WHERE 1=1";
        $count_p = [];
        if ($activity_id > 0)  { $count_sql .= " AND ah.activity_id = ?"; $count_p[] = $activity_id; }
        if ($action_filter)    { $count_sql .= " AND ah.action = ?";       $count_p[] = $action_filter; }
        if ($start_date)       { $count_sql .= " AND DATE(ah.created_at) >= ?"; $count_p[] = $start_date; }
        if ($_SESSION['user_role'] === 'DIRECTEUR') {
            $count_sql .= " AND (u.role IN ('AGENT','CAISSIER','DIRECTEUR') OR ah.user_id = ?)";
            $count_p[] = $_SESSION['user_id'];
        }

        $stmt2 = $this->conn->prepare($count_sql);
        $stmt2->execute($count_p);
        $total       = (int)$stmt2->fetchColumn();
        $total_pages = max(1, ceil($total / $limit));

        AuditLog::log('view_activity_history', 'activities', null, 'Consultation historique des activités');

        $pageTitle = "Historique des Activités";
        require 'views/admin/activity_history.php';
    }

    // ──────────────────────────────────────────────────────────
    // Export CSV
    // ──────────────────────────────────────────────────────────
    public function exportData(): void
    {
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=admin_dashboard"); exit;
        }

        $export_type = $_POST['export_type'] ?? '';

        try {
            if ($export_type === 'users')         $this->exportUsers();
            elseif ($export_type === 'payments')  $this->exportPayments();
            elseif ($export_type === 'login_history') $this->exportLoginHistory();
            elseif ($export_type === 'audit_logs')    $this->exportAuditLogs();
            else {
                $_SESSION['flash_error'] = "Type d'export inconnu.";
                header("Location: " . BASE_URL . "/?action=admin_dashboard"); exit;
            }
            AuditLog::log('export_data', 'export', null, "Export : $export_type");
        } catch (Exception $e) {
            error_log("[COSMOS] exportData: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de l'export.";
            header("Location: " . BASE_URL . "/?action=admin_dashboard"); exit;
        }
    }

    // ── Privés ─────────────────────────────────────────────────

    private function exportUsers(): void
    {
        $stmt = $this->conn->prepare(
            "SELECT id, nom, prenom, email, role, is_active, created_at, last_login
             FROM users ORDER BY created_at DESC"
        );
        $stmt->execute();
        $this->sendCSV('utilisateurs_' . date('Y-m-d'), $stmt->fetchAll());
    }

    private function exportPayments(): void
    {
        $stmt = $this->conn->prepare(
            "SELECT p.id, p.montant, p.methode, p.date_paiement,
                    u.nom AS client_nom, u.prenom AS client_prenom,
                    a.nom AS activite,
                    c.nom AS caissier_nom, c.prenom AS caissier_prenom
             FROM payments p
             LEFT JOIN reservations r ON p.reservation_id = r.id
             LEFT JOIN activities a ON r.activite_id = a.id
             LEFT JOIN users u ON r.user_id = u.id
             LEFT JOIN users c ON p.caissier_id = c.id
             ORDER BY p.date_paiement DESC"
        );
        $stmt->execute();
        $this->sendCSV('paiements_' . date('Y-m-d'), $stmt->fetchAll());
    }

    private function exportLoginHistory(): void
    {
        $history = new LoginHistory();
        $this->sendCSV('connexions_' . date('Y-m-d'), $history->getAll(10000, 0));
    }

    private function exportAuditLogs(): void
    {
        $audit = new AuditLog();
        $this->sendCSV('audit_' . date('Y-m-d'), $audit->getFiltered(['limit' => 10000]));
    }

    private function sendCSV(string $filename, array $data): void
    {
        if (empty($data)) {
            $_SESSION['flash_error'] = "Aucune donnée à exporter.";
            header("Location: " . BASE_URL . "/?action=admin_dashboard");
            exit;
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) fputcsv($output, $row);
        fclose($output);
        exit;
    }

    // ── Stats ──────────────────────────────────────────────────
    private function getUsersCount(): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function getActiveUsersCount(): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function getRecentLogins(int $limit = 5): array
    {
        $stmt = $this->conn->prepare(
            "SELECT lh.*, u.role
             FROM login_history lh
             LEFT JOIN users u ON lh.user_id = u.id
             WHERE lh.status = 'success'
             ORDER BY lh.login_time DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getSuspiciousLogins(int $limit = 5): array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM login_history WHERE is_suspicious = 1 ORDER BY login_time DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getRecentActivities(int $limit = 10): array
    {
        $stmt = $this->conn->prepare(
            "SELECT al.*, u.nom, u.prenom, u.role
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.timestamp DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getUsersWithFilters(array $filters, int $limit, int $offset): array
    {
        $query  = "SELECT id, nom, prenom, email, role, is_active, last_login, created_at
                   FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $query  .= " AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }
        if (!empty($filters['role'])) {
            $query  .= " AND role = ?";
            $params[] = $filters['role'];
        }
        if (!empty($filters['roles_allowed'])) {
            $placeholders = implode(',', array_fill(0, count($filters['roles_allowed']), '?'));
            $query  .= " AND role IN ($placeholders)";
            $params = array_merge($params, $filters['roles_allowed']);
        }
        if (isset($filters['is_active'])) {
            $query  .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }
        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function countUsersWithFilters(array $filters): int
    {
        $query  = "SELECT COUNT(*) FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $query  .= " AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }
        if (!empty($filters['role'])) {
            $query  .= " AND role = ?";
            $params[] = $filters['role'];
        }
        if (!empty($filters['roles_allowed'])) {
            $placeholders = implode(',', array_fill(0, count($filters['roles_allowed']), '?'));
            $query  .= " AND role IN ($placeholders)";
            $params = array_merge($params, $filters['roles_allowed']);
        }
        if (isset($filters['is_active'])) {
            $query  .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function getUserLoginHistory(int $user_id, int $limit, int $offset): array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM login_history WHERE user_id = ?
             ORDER BY login_time DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll();
    }

    private function ensureUserPermissionsTable(): void
    {
        $this->conn->exec(
            "CREATE TABLE IF NOT EXISTS user_permissions (
                id            INT AUTO_INCREMENT PRIMARY KEY,
                user_id       INT NOT NULL,
                permission_id INT NOT NULL,
                granted_by    INT NULL,
                created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uk_user_perm (user_id, permission_id),
                CONSTRAINT fk_up_user FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_up_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
