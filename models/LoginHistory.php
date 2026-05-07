<?php
class LoginHistory {
    private $conn;
    private $table = 'login_history';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Enregistrer une tentative de connexion
     */
    public static function recordLogin($user_id, $email, $first_name, $last_name, $status = 'success', $failure_reason = null) {
        try {
            $history = new self();
            $loginHistory = [
                'user_id' => $user_id,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'ip_address' => self::getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'browser' => self::getBrowser(),
                'os' => self::getOS(),
                'device_type' => self::getDeviceType(),
                'status' => $status,
                'failure_reason' => $failure_reason,
                'is_suspicious' => self::isSuspiciousLogin($user_id)
            ];

            return $history->create($loginHistory);
        } catch (Exception $e) {
            error_log("Error recording login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un enregistrement de connexion
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, email, first_name, last_name, ip_address, user_agent, browser, os, device_type, status, failure_reason, is_suspicious) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['ip_address'],
            $data['user_agent'],
            $data['browser'],
            $data['os'],
            $data['device_type'],
            $data['status'],
            $data['failure_reason'],
            $data['is_suspicious'] ? 1 : 0
        ]);
    }

    /**
     * Enregistrer la déconnexion
     */
    public static function recordLogout($user_id) {
        try {
            $history = new self();
            $query = "UPDATE " . $history->table . " 
                      SET logout_time = NOW() 
                      WHERE user_id = ? AND logout_time IS NULL 
                      ORDER BY login_time DESC LIMIT 1";
            
            $stmt = $history->conn->prepare($query);
            return $stmt->execute([$user_id]);
        } catch (Exception $e) {
            error_log("Error recording logout: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir l'historique des connexions d'un utilisateur
     */
    public function getUserLoginHistory($user_id, $limit = 50) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = ? 
                  ORDER BY login_time DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir tous les historiques de connexion
     */
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY login_time DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les connexions suspectes
     */
    public function getSuspiciousLogins() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_suspicious = TRUE 
                  ORDER BY login_time DESC 
                  LIMIT 100";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Vérifier si la connexion est suspecte
     */
    private static function isSuspiciousLogin($user_id) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Vérifier les connexions récentes du même utilisateur
            $query = "SELECT DISTINCT ip_address FROM login_history 
                      WHERE user_id = ? 
                      ORDER BY login_time DESC 
                      LIMIT 10";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id]);
            $recent_ips = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $current_ip = self::getClientIP();
            
            // Connexion suspecte si IP différente des 10 dernières connexions
            if (!empty($recent_ips) && !in_array($current_ip, $recent_ips)) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir le navigateur
     */
    private static function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
        if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
        if (strpos($user_agent, 'Safari') !== false) return 'Safari';
        if (strpos($user_agent, 'Edge') !== false) return 'Edge';
        if (strpos($user_agent, 'Opera') !== false) return 'Opera';
        
        return 'Unknown';
    }

    /**
     * Obtenir le système d'exploitation
     */
    private static function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($user_agent, 'Windows') !== false) return 'Windows';
        if (strpos($user_agent, 'Mac') !== false) return 'macOS';
        if (strpos($user_agent, 'Linux') !== false) return 'Linux';
        if (strpos($user_agent, 'Android') !== false) return 'Android';
        if (strpos($user_agent, 'iPhone') !== false) return 'iOS';
        
        return 'Unknown';
    }

    /**
     * Obtenir le type d'appareil
     */
    private static function getDeviceType() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/Mobile|Android|iPhone|iPad/', $user_agent)) {
            return 'Mobile';
        }
        
        return 'Desktop';
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
     * Compter les connexions
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
