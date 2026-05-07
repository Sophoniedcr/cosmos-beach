<?php
/**
 * ActivityHistory Model
 * Suit l'historique des modifications des activités
 */
class ActivityHistory {
    private $conn;
    private $table = 'activity_history';

    public $id;
    public $activity_id;
    public $user_id;
    public $action;
    public $old_values;
    public $new_values;
    public $changed_fields;
    public $created_at;
    public $ip_address;
    public $user_agent;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Enregistrer une action sur une activité
     */
    public static function log($activity_id, $action, $user_id, $old_values = null, $new_values = null) {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Calculer les champs modifiés
            $changed_fields = [];
            if ($old_values && $new_values) {
                foreach ($new_values as $key => $value) {
                    if (!isset($old_values[$key]) || $old_values[$key] != $value) {
                        $changed_fields[] = $key;
                    }
                }
            }

            $query = "INSERT INTO activity_history 
                     (activity_id, user_id, action, old_values, new_values, changed_fields, ip_address, user_agent)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                $activity_id,
                $user_id,
                $action,
                $old_values ? json_encode($old_values) : null,
                $new_values ? json_encode($new_values) : null,
                json_encode($changed_fields),
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error logging activity history: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer l'historique d'une activité
     */
    public function getByActivityId($activity_id, $limit = 100) {
        $query = "SELECT ah.*, u.prenom, u.nom, u.email
                  FROM {$this->table} ah
                  LEFT JOIN users u ON ah.user_id = u.id
                  WHERE ah.activity_id = ?
                  ORDER BY ah.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$activity_id, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer tout l'historique avec filtres
     */
    public function getAll($filters = []) {
        $query = "SELECT ah.*, u.prenom, u.nom, a.nom as activity_nom
                  FROM {$this->table} ah
                  LEFT JOIN users u ON ah.user_id = u.id
                  LEFT JOIN activities a ON ah.activity_id = a.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['activity_id'])) {
            $query .= " AND ah.activity_id = ?";
            $params[] = $filters['activity_id'];
        }

        if (!empty($filters['user_id'])) {
            $query .= " AND ah.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $query .= " AND ah.action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(ah.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(ah.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY ah.created_at DESC";

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
