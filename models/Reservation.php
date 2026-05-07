<?php
class Reservation {
    private $conn;
    private $table_name = "reservations";

    public $id;
    public $user_id;
    public $activite_id;
    public $date_reservation;
    public $statut;
    public $montant_total;
    public $nombre_personnes;
    public $nombre_chambres;
    public $mode_reservation;
    public $nombre_tables;
    public $nombre_adultes;
    public $nombre_enfants;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=?, activite_id=?, date_reservation=?, montant_total=?, 
                      nombre_personnes=?, nombre_chambres=?, mode_reservation=?, 
                      nombre_tables=?, nombre_adultes=?, nombre_enfants=?";
        $stmt = $this->conn->prepare($query);
        
        $this->date_reservation = htmlspecialchars(strip_tags($this->date_reservation));
        
        $params = [
            $this->user_id, 
            $this->activite_id, 
            $this->date_reservation, 
            $this->montant_total, 
            $this->nombre_personnes,
            $this->nombre_chambres,
            $this->mode_reservation,
            $this->nombre_tables,
            $this->nombre_adultes,
            $this->nombre_enfants
        ];
        
        if ($stmt->execute($params)) {
            return true;
        }
        return false;
    }

    public function getByUser($user_id) {
        $query = "SELECT r.*, a.nom as activite_nom, a.image_url 
                  FROM " . $this->table_name . " r
                  JOIN activities a ON r.activite_id = a.id
                  WHERE r.user_id = ? 
                  ORDER BY r.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Récupérer les réservations en attente pour le caissier
    public function getPending() {
         $query = "SELECT r.*, a.nom as activite_nom, u.nom as client_nom 
                  FROM " . $this->table_name . " r
                  JOIN activities a ON r.activite_id = a.id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.statut = 'ATTENTE' 
                  ORDER BY r.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $new_status) {
        $query = "UPDATE " . $this->table_name . " SET statut = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$new_status, $id]);
    }

    // Récupérer par ID
    public function getById($id) {
        $query = "SELECT r.*, a.nom as activite_nom, u.nom as client_nom 
                  FROM " . $this->table_name . " r
                  JOIN activities a ON r.activite_id = a.id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Vérifier si la capacité n'est pas atteinte pour une date donnée
    public function isAvailable($activite_id, $date_reservation) {
        // Obtenir la capacité Max de l'activité
        $queryAct = "SELECT capacite_max FROM activities WHERE id = ?";
        $stmtAct = $this->conn->prepare($queryAct);
        $stmtAct->execute([$activite_id]);
        $activite = $stmtAct->fetch();
        
        if(!$activite) return false;

        // Calculer les réservations pour ce jour précis en tenant compte du nombre de personnes
        $dateDay = date('Y-m-d', strtotime($date_reservation));
        $queryRes = "SELECT COALESCE(SUM(nombre_personnes), 0) as total FROM " . $this->table_name . " 
                     WHERE activite_id = ? AND DATE(date_reservation) = ? AND statut IN ('ATTENTE', 'CONFIRMEE', 'PAYEE')";
        $stmtRes = $this->conn->prepare($queryRes);
        $stmtRes->execute([$activite_id, $dateDay]);
        $row = $stmtRes->fetch();

        return ($row['total'] < $activite['capacite_max']);
    }
}
?>
