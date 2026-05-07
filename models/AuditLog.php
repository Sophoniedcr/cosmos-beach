<?php
class AuditLog {
    private $conn;
    private $table = 'audit_logs';

    public $id;
    public $user_id;
    public $user_name;
    public $action;
    public $entity_type;
    public $entity_id;
    public $description;
    public $ip_address;
    public $user_agent;
    public $status;
    public $timestamp;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Enregistrer une action utilisateur
     */
    public static function log($action, $entity_type = null, $entity_id = null, $description = null, $status = 'success') {
        try {
            $auditLog = new self();
            $auditLog->user_id = $_SESSION['user_id'] ?? null;
            $auditLog->user_name = ($_SESSION['user_nom'] ?? '') . ' ' . ($_SESSION['user_prenom'] ?? '');
            $auditLog->action = $action;
            $auditLog->entity_type = $entity_type;
            $auditLog->entity_id = $entity_id;
            $auditLog->description = $description;
            $auditLog->ip_address = self::getClientIP();
            $auditLog->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $auditLog->status = $status;

            return $auditLog->create();
        } catch (Exception $e) {
            error_log("Error logging audit: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un log d'audit
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, user_name, action, entity_type, entity_id, description, ip_address, user_agent, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $this->user_id,
            $this->user_name,
            $this->action,
            $this->entity_type,
            $this->entity_id,
            $this->description,
            $this->ip_address,
            $this->user_agent,
            $this->status
        ]);
    }

    /**
     * Obtenir tous les logs
     */
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY timestamp DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les logs filtrés
     */
    public function getFiltered($filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (isset($filters['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (isset($filters['action'])) {
            $query .= " AND action = ?";
            $params[] = $filters['action'];
        }

        if (isset($filters['entity_type'])) {
            $query .= " AND entity_type = ?";
            $params[] = $filters['entity_type'];
        }

        if (isset($filters['start_date'])) {
            $query .= " AND timestamp >= ?";
            $params[] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            $query .= " AND timestamp <= ?";
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY timestamp DESC";

        if (isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = $filters['limit'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir l'IP du client
     */
    private static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Compter les total de logs
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>
