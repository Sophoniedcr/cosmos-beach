<?php
// ============================================================
// COSMOS BEACH — controllers/HistoryController.php — v1.0
// Charge les données d'historique adaptées au rôle connecté
// ============================================================

class HistoryController
{
    private $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }

        $role    = $_SESSION['user_role'] ?? 'VISITEUR';
        $user_id = (int)$_SESSION['user_id'];

        // Variables initialisées à vide (évite les undefined)
        $reservations    = [];
        $paiements       = [];
        $activite_history = [];
        $connexions      = [];
        $audit_logs      = [];

        // ── Réservations ────────────────────────────────────────
        if ($role === 'VISITEUR') {
            // Le visiteur voit seulement les siennes
            $stmt = $this->conn->prepare(
                "SELECT r.*, a.nom AS activite_nom, a.image_url,
                        '' AS client_nom, '' AS client_prenom, '' AS client_email
                 FROM reservations r
                 JOIN activities a ON r.activite_id = a.id
                 WHERE r.user_id = ?
                 ORDER BY r.date_creation DESC
                 LIMIT 50"
            );
            $stmt->execute([$user_id]);
            $reservations = $stmt->fetchAll();

        } elseif ($role === 'AGENT') {
            // L'agent voit toutes les réservations (il les traite)
            $stmt = $this->conn->prepare(
                "SELECT r.*, a.nom AS activite_nom,
                        u.nom AS client_nom, u.prenom AS client_prenom, u.email AS client_email
                 FROM reservations r
                 JOIN activities a ON r.activite_id = a.id
                 JOIN users u ON r.user_id = u.id
                 ORDER BY r.date_creation DESC
                 LIMIT 50"
            );
            $stmt->execute();
            $reservations = $stmt->fetchAll();

        } elseif (in_array($role, ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'])) {
            $stmt = $this->conn->prepare(
                "SELECT r.*, a.nom AS activite_nom,
                        u.nom AS client_nom, u.prenom AS client_prenom, u.email AS client_email
                 FROM reservations r
                 JOIN activities a ON r.activite_id = a.id
                 JOIN users u ON r.user_id = u.id
                 ORDER BY r.date_creation DESC
                 LIMIT 100"
            );
            $stmt->execute();
            $reservations = $stmt->fetchAll();
        }

        // ── Paiements (Caissier, Directeur, Admin) ───────────────
        if (in_array($role, ['CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'])) {
            $query  = "SELECT p.*,
                              u.nom AS client_nom, u.prenom AS client_prenom,
                              a.nom AS activite_nom,
                              c.nom AS caissier_nom, c.prenom AS caissier_prenom
                       FROM payments p
                       LEFT JOIN reservations r ON p.reservation_id = r.id
                       LEFT JOIN activities a   ON r.activite_id = a.id
                       LEFT JOIN users u        ON r.user_id = u.id
                       LEFT JOIN users c        ON p.caissier_id = c.id";
            $params = [];

            // Le caissier voit seulement ses propres encaissements
            if ($role === 'CAISSIER') {
                $query  .= " WHERE p.caissier_id = ?";
                $params[] = $user_id;
            }

            $query .= " ORDER BY p.date_paiement DESC LIMIT 50";
            $stmt   = $this->conn->prepare($query);
            $stmt->execute($params);
            $paiements = $stmt->fetchAll();
        }

        // ── Historique activités (Directeur, Admin) ──────────────
        if (in_array($role, ['DIRECTEUR', 'SUPER_ADMIN'])) {
            $query = "SELECT ah.*, u.nom, u.prenom, u.email, u.role,
                             a.nom AS activite_nom
                      FROM activity_history ah
                      LEFT JOIN users u ON ah.user_id = u.id
                      LEFT JOIN activities a ON ah.activity_id = a.id";

            if ($role === 'DIRECTEUR') {
                $query .= " WHERE u.role IN ('AGENT','CAISSIER','DIRECTEUR')";
            }

            $query .= " ORDER BY ah.created_at DESC LIMIT 20";
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $activite_history = $stmt->fetchAll();
            } catch (\Exception $e) {
                // La table peut ne pas exister encore
                $activite_history = [];
                error_log("[COSMOS] activity_history table: " . $e->getMessage());
            }
        }

        // ── Connexions (Directeur, Admin) ────────────────────────
        if (in_array($role, ['DIRECTEUR', 'SUPER_ADMIN'])) {
            $query = "SELECT lh.*, u.role
                      FROM login_history lh
                      LEFT JOIN users u ON lh.user_id = u.id";

            if ($role === 'DIRECTEUR') {
                $query .= " WHERE u.role IN ('AGENT','CAISSIER','DIRECTEUR')";
            }

            $query .= " ORDER BY lh.login_time DESC LIMIT 30";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $connexions = $stmt->fetchAll();
        }

        // ── Journal d'audit (Admin uniquement) ───────────────────
        if ($role === 'SUPER_ADMIN') {
            $stmt = $this->conn->prepare(
                "SELECT al.*, al.user_name
                 FROM audit_logs al
                 ORDER BY al.timestamp DESC
                 LIMIT 50"
            );
            $stmt->execute();
            $audit_logs = $stmt->fetchAll();
        }

        $pageTitle = "Mon Historique";
        require 'views/history/mon_historique.php';
    }
}
