<?php
// ============================================================
// COSMOS BEACH — controllers/DashboardController.php — v3.1
// Corrections :
//   - ADMIN/SUPER_ADMIN → redirect vers admin_dashboard (plus d'erreur fatale)
//   - DIRECTEUR → charge son propre dashboard avec KPIs (plus de financial_report)
//   - AGENT unifié (RECEPTIONNISTE + AGENT_RESERVATION supprimés)
//   - Variables KPIs chargées proprement pour le directeur
// ============================================================

class DashboardController
{
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $role      = $_SESSION['user_role'] ?? 'VISITEUR';
        $pageTitle = "Mon Tableau de Bord";

        switch ($role) {

            // ── Super Admin → espace admin dédié ────────────
            case 'SUPER_ADMIN':
                header("Location: " . BASE_URL . "/?action=admin_dashboard");
                exit;

            // ── Directeur → dashboard avec KPIs ────────────
            case 'DIRECTEUR':
                $this->loadDirecteurDashboard();
                break;

            // ── Marketeur ───────────────────────────────────
            case 'MARKETEUR':
                require_once 'models/EventModel.php';
                require_once 'models/EventTicket.php';
                $controller = new MarketingController();
                $controller->dashboard();
                return;

            // ── Caissier ────────────────────────────────────
            case 'CAISSIER':
                require_once 'models/Reservation.php';
                $resModel            = new Reservation();
                $pending_reservations = $resModel->getPending();
                $search_result       = null;
                $error_message       = '';
                $search_id           = '';
                require 'views/dashboard/caissier.php';
                break;

            // ── Agent (ex-Réceptionniste / Agent de réservation) ──
            case 'AGENT':
                require 'views/dashboard/agent.php';
                break;

            // ── Visiteur (par défaut) ────────────────────────
            case 'VISITEUR':
            default:
                require_once 'models/Reservation.php';
                require_once 'models/Reclamation.php';
                $resModel    = new Reservation();
                $reservations = $resModel->getByUser($_SESSION['user_id']);
                $recModel    = new Reclamation();
                $reclamations = $recModel->getByUser($_SESSION['user_id']);
                require 'views/dashboard/visiteur.php';
                break;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Dashboard Directeur : charge les KPIs et affiche la vue
    // ──────────────────────────────────────────────────────────
    private function loadDirecteurDashboard(): void
    {
        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Revenu global
            $stmt = $conn->prepare("SELECT COALESCE(SUM(montant), 0) FROM payments");
            $stmt->execute();
            $totalRevenue = (float)$stmt->fetchColumn();

            // Revenu du jour
            $stmt = $conn->prepare(
                "SELECT COALESCE(SUM(montant), 0) FROM payments WHERE DATE(date_paiement) = CURDATE()"
            );
            $stmt->execute();
            $todayRevenue = (float)$stmt->fetchColumn();

            // Total réservations
            $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations");
            $stmt->execute();
            $totalReservations = (int)$stmt->fetchColumn();

            // Réservations en attente
            $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE statut = 'ATTENTE'");
            $stmt->execute();
            $pendingReservations = (int)$stmt->fetchColumn();

            // Total utilisateurs actifs
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
            $stmt->execute();
            $activeUsers = (int)$stmt->fetchColumn();

            // Paiements récents (5 derniers) avec nom/prénom/date/heure
            $stmt = $conn->prepare(
                "SELECT p.id, p.montant, p.methode, p.date_paiement,
                        u.nom AS client_nom, u.prenom AS client_prenom,
                        a.nom AS activite_nom,
                        c.nom AS caissier_nom, c.prenom AS caissier_prenom
                 FROM payments p
                 LEFT JOIN reservations r ON p.reservation_id = r.id
                 LEFT JOIN activities a   ON r.activite_id = a.id
                 LEFT JOIN users u        ON r.user_id = u.id
                 LEFT JOIN users c        ON p.caissier_id = c.id
                 ORDER BY p.date_paiement DESC
                 LIMIT 5"
            );
            $stmt->execute();
            $recentPayments = $stmt->fetchAll();

            // Historique connexions des employés (dernières 10)
            $stmt = $conn->prepare(
                "SELECT lh.*, u.role
                 FROM login_history lh
                 LEFT JOIN users u ON lh.user_id = u.id
                 WHERE u.role IN ('AGENT','CAISSIER')
                    OR lh.user_id IS NULL
                 ORDER BY lh.login_time DESC
                 LIMIT 10"
            );
            $stmt->execute();
            $employeeLogins = $stmt->fetchAll();

        } catch (Exception $e) {
            error_log("[COSMOS][Directeur] " . $e->getMessage());
            $totalRevenue        = 0;
            $todayRevenue        = 0;
            $totalReservations   = 0;
            $pendingReservations = 0;
            $activeUsers         = 0;
            $recentPayments      = [];
            $employeeLogins      = [];
        }

        $pageTitle = "Tableau de Bord Direction";
        require 'views/dashboard/directeur.php';
    }
}
