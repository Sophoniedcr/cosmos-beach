<?php
class Permission {
    private $conn;
    private $table = 'permissions';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtenir toutes les permissions
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY category, name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les permissions par catégorie
     */
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = ? ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les permissions d'un rôle
     */
    public function getPermissionsByRole($role) {
        $query = "SELECT p.* FROM " . $this->table . " p
                  INNER JOIN role_permissions rp ON p.id = rp.permission_id
                  WHERE rp.role = ?
                  ORDER BY p.category, p.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifier si un utilisateur a une permission
     */
    public static function hasPermission($user_id, $permission) {
        try {
            $user = new User();
            $user->id = $user_id;
            
            // Récupérer le rôle de l'utilisateur
            $query = "SELECT role FROM users WHERE id = ?";
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return false;
            }
            
            $role = $result['role'];
            
            // Vérifier si le rôle a cette permission
            $query = "SELECT COUNT(*) as count FROM permissions p
                      INNER JOIN role_permissions rp ON p.id = rp.permission_id
                      WHERE rp.role = ? AND p.name = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$role, $permission]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error checking permission: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assigner des permissions à un rôle
     */
    public function assignPermissionToRole($role, $permission_id) {
        $query = "INSERT IGNORE INTO role_permissions (role, permission_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$role, $permission_id]);
    }

    /**
     * Retirer une permission d'un rôle
     */
    public function removePermissionFromRole($role, $permission_id) {
        $query = "DELETE FROM role_permissions WHERE role = ? AND permission_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$role, $permission_id]);
    }

    /**
     * Obtenir les permissions d'une catégorie
     */
    public function getCategoriesGrouped() {
        $query = "SELECT category, COUNT(*) as count FROM " . $this->table . " 
                  GROUP BY category ORDER BY category";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
