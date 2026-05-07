<?php
class Report {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère les réservations filtrées par date et/ou activité
     * @param string $date_debut (YYYY-MM-DD)
     * @param string $date_fin (YYYY-MM-DD)
     * @param int|null $activite_id ID de l'activité (optionnel)
     * @return array
     */
    public function getReservationsReport($date_debut, $date_fin, $activite_id = null) {
        $query = "SELECT r.*, a.nom as activite_nom, u.nom as client_nom, a.type as activite_type 
                  FROM reservations r
                  JOIN activities a ON r.activite_id = a.id
                  JOIN users u ON r.user_id = u.id
                  WHERE DATE(r.date_reservation) >= ? AND DATE(r.date_reservation) <= ?";
        
        $params = [$date_debut, $date_fin];

        if ($activite_id) {
            $query .= " AND r.activite_id = ?";
            $params[] = $activite_id;
        }

        $query .= " ORDER BY r.date_reservation DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
