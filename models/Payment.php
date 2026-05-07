<?php
class Payment {
    private $conn;
    private $table_name = "payments";

    public $id;
    public $reservation_id;
    public $caissier_id;
    public $montant;
    public $methode;
    public $date_paiement;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un paiement
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET reservation_id=?, caissier_id=?, montant=?, methode=?, date_paiement=NOW()";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$this->reservation_id, $this->caissier_id, $this->montant, $this->methode])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Récupérer par réservation ID
    public function getByReservationId($reservation_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE reservation_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reservation_id]);
        return $stmt->fetch();
    }

    // Récupérer tous les paiements avec détails
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT p.*, r.montant_total as reservation_montant, a.nom as activite_nom, 
                         u.nom as client_nom, u.prenom as client_prenom, 
                         c.nom as caissier_nom, c.prenom as caissier_prenom
                  FROM " . $this->table_name . " p
                  LEFT JOIN reservations r ON p.reservation_id = r.id
                  LEFT JOIN activities a ON r.activite_id = a.id
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN users c ON p.caissier_id = c.id
                  ORDER BY p.date_paiement DESC
                  LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    // Compter total des paiements
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    // Rechercher paiements avec filtres
    public function search($filters = []) {
        $query = "SELECT p.*, r.montant_total as reservation_montant, a.nom as activite_nom, 
                         u.nom as client_nom, u.prenom as client_prenom, 
                         c.nom as caissier_nom, c.prenom as caissier_prenom
                  FROM " . $this->table_name . " p
                  LEFT JOIN reservations r ON p.reservation_id = r.id
                  LEFT JOIN activities a ON r.activite_id = a.id
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN users c ON p.caissier_id = c.id
                  WHERE 1=1";

        $params = [];

        // Filtres optionnels
        if (!empty($filters['reservation_id'])) {
            $query .= " AND p.reservation_id = ?";
            $params[] = $filters['reservation_id'];
        }

        if (!empty($filters['client_name'])) {
            $query .= " AND (u.nom LIKE ? OR u.prenom LIKE ?)";
            $search = '%' . $filters['client_name'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['caissier_id'])) {
            $query .= " AND p.caissier_id = ?";
            $params[] = $filters['caissier_id'];
        }

        if (!empty($filters['methode'])) {
            $query .= " AND p.methode = ?";
            $params[] = $filters['methode'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(p.date_paiement) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(p.date_paiement) <= ?";
            $params[] = $filters['date_to'];
        }

        $query .= " ORDER BY p.date_paiement DESC";

        if (!empty($filters['limit'])) {
            $query .= " LIMIT " . intval($filters['limit']);
        }

        if (!empty($filters['offset'])) {
            $query .= " OFFSET " . intval($filters['offset']);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Statistiques des paiements
    public function getStatistics($start_date = null, $end_date = null) {
        $query = "SELECT 
                    COUNT(*) as total_paiements,
                    SUM(montant) as montant_total,
                    AVG(montant) as montant_moyen,
                    methode,
                    DATE(date_paiement) as date
                  FROM " . $this->table_name . "
                  WHERE 1=1";

        $params = [];

        if ($start_date) {
            $query .= " AND DATE(date_paiement) >= ?";
            $params[] = $start_date;
        }

        if ($end_date) {
            $query .= " AND DATE(date_paiement) <= ?";
            $params[] = $end_date;
        }

        $query .= " GROUP BY methode, DATE(date_paiement)
                    ORDER BY DATE(date_paiement) DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
