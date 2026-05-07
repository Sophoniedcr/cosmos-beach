<?php
class ReportController {
    public function financial_report() {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['DIRECTEUR', 'ADMIN'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();

        // Récupérer le total des revenus
        $queryTotal = "SELECT SUM(montant) as total FROM payments";
        $stmtTotal = $conn->query($queryTotal);
        $totalRevenue = $stmtTotal->fetch()['total'] ?? 0;

        // Récupérer les paiements du jour
        $queryToday = "SELECT SUM(montant) as total FROM payments WHERE DATE(date_paiement) = CURDATE()";
        $stmtToday = $conn->query($queryToday);
        $todayRevenue = $stmtToday->fetch()['total'] ?? 0;

        // Récupérer les réservations
        $queryRes = "SELECT COUNT(*) as total FROM reservations";
        $stmtRes = $conn->query($queryRes);
        $totalReservations = $stmtRes->fetch()['total'] ?? 0;

        $pageTitle = "Rapports & Statistiques";
        require 'views/dashboard/directeur.php';
    }

    public function inventory_report() {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['DIRECTEUR', 'ADMIN'])) {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        require_once 'models/Report.php';
        require_once 'models/Activity.php';

        $reportModel = new Report();
        $activityModel = new Activity();
        
        $activities = $activityModel->getAll();
        
        $reservations = [];
        $total_amount = 0;
        
        // Valeurs par défaut
        $date_debut = date('Y-m-01');
        $date_fin = date('Y-m-t');
        $activite_id = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date_debut = $_POST['date_debut'] ?? $date_debut;
            $date_fin = $_POST['date_fin'] ?? $date_fin;
            $activite_id = !empty($_POST['activite_id']) ? $_POST['activite_id'] : null;
            
            $reservations = $reportModel->getReservationsReport($date_debut, $date_fin, $activite_id);
            
            foreach ($reservations as $res) {
                if ($res['statut'] === 'PAYEE' || $res['statut'] === 'CONFIRMEE') {
                    $total_amount += $res['montant_total'];
                }
            }
        }

        $pageTitle = "États en Sortie";
        require 'views/dashboard/reports.php';
    }
}
?>
