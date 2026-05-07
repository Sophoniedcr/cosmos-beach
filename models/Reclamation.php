<?php
class Reclamation {
    private $conn;
    private $table_name = "reclamations";

    public $id;
    public $user_id;
    public $sujet;
    public $message;
    public $statut;
    public $date_creation;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=?, sujet=?, message=?, statut='NOUVELLE'";
        $stmt = $this->conn->prepare($query);
        
        $this->sujet = htmlspecialchars(strip_tags($this->sujet));
        $this->message = htmlspecialchars(strip_tags($this->message));
        
        return $stmt->execute([$this->user_id, $this->sujet, $this->message]);
    }

    public function getByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getAllPending() {
         $query = "SELECT r.*, u.nom as client_nom FROM " . $this->table_name . " r JOIN users u ON r.user_id = u.id WHERE r.statut != 'RESOLUE' ORDER BY r.date_creation DESC";
         $stmt = $this->conn->prepare($query);
         $stmt->execute();
         return $stmt->fetchAll();
    }

    public function updateStatus($id, $new_status) {
        $query = "UPDATE " . $this->table_name . " SET statut = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$new_status, $id]);
    }
}
?>
