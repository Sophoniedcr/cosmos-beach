<?php
/**
 * ReservationHistory Model
 * Suit l'historique des modifications des réservations
 */
class ReservationHistory {
    private $conn;
    private $table = 'reservation_history';

    public $id;
    public $reservation_id;
    public $user_id;
    public $action;
    public $old_status;
    public $new_status;
    public $description;
    public $created_at;
    public $ip_address;
    public $user_agent;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Enregistrer une action sur une réservation
     */
    public static function log($reservation_id, $action, $user_id, $old_status = null, $new_status = null, $description = null) {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $query = "INSERT INTO reservation_history 
                     (reservation_id, user_id, action, old_status, new_status, description, ip_address, user_agent)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                $reservation_id,
                $user_id,
                $action,
                $old_status,
                $new_status,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error logging reservation history: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer l'historique d'une réservation
     */
    public function getByReservationId($reservation_id, $limit = 100) {
        $query = "SELECT rh.*, u.prenom, u.nom, u.email, u.role
                  FROM {$this->table} rh
                  LEFT JOIN users u ON rh.user_id = u.id
                  WHERE rh.reservation_id = ?
                  ORDER BY rh.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reservation_id, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer tout l'historique avec filtres
     */
    public function getAll($filters = []) {
        $query = "SELECT rh.*, u.prenom, u.nom, r.user_id as client_id
                  FROM {$this->table} rh
                  LEFT JOIN users u ON rh.user_id = u.id
                  LEFT JOIN reservations r ON rh.reservation_id = r.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['reservation_id'])) {
            $query .= " AND rh.reservation_id = ?";
            $params[] = $filters['reservation_id'];
        }

        if (!empty($filters['user_id'])) {
            $query .= " AND rh.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $query .= " AND rh.action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(rh.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(rh.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY rh.created_at DESC";

        if (!empty($filters['limit'])) {
            $query .= " LIMIT " . intval($filters['limit']);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Compter les enregistrements
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
}
?>
