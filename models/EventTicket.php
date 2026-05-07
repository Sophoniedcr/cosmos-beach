<?php
// ============================================================
// COSMOS BEACH — models/EventTicket.php
// Gestion des tickets d'événements payants
// ============================================================

class EventTicket {
    private $conn;
    private $table_name = "event_tickets";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Générer un numéro de ticket unique (ex: TKT-00042)
    private function generateTicketNumber(): string {
        $stmt = $this->conn->prepare("SELECT MAX(id) FROM " . $this->table_name);
        $stmt->execute();
        $maxId = (int)$stmt->fetchColumn();
        return 'TKT-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
    }

    // Créer un ticket (réservation payante)
    public function create(int $event_id, int $user_id, int $nombre_places, float $montant_total): array {
        $numero = $this->generateTicketNumber();

        $query = "INSERT INTO " . $this->table_name . "
                  SET event_id=?, user_id=?, numero_ticket=?,
                      nombre_places=?, montant_total=?, statut='EN_ATTENTE'";
        $stmt = $this->conn->prepare($query);
        $ok   = $stmt->execute([$event_id, $user_id, $numero, $nombre_places, $montant_total]);

        if ($ok) {
            return ['success' => true, 'ticket_id' => (int)$this->conn->lastInsertId(), 'numero' => $numero];
        }
        return ['success' => false];
    }

    // Confirmer un ticket (après paiement)
    public function confirm(int $ticket_id): bool {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table_name . " SET statut='CONFIRME' WHERE id=?"
        );
        return $stmt->execute([$ticket_id]);
    }

    // Annuler un ticket
    public function cancel(int $ticket_id, int $user_id): bool {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table_name . " SET statut='ANNULE'
             WHERE id=? AND user_id=? AND statut='EN_ATTENTE'"
        );
        return $stmt->execute([$ticket_id, $user_id]);
    }

    // Marquer l'email de confirmation comme envoyé
    public function markEmailSent(int $ticket_id): bool {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table_name . " SET email_envoye=1 WHERE id=?"
        );
        return $stmt->execute([$ticket_id]);
    }

    // Tickets d'un visiteur
    public function getByUser(int $user_id): array {
        $query = "SELECT et.*, e.titre AS event_titre, e.date_debut, e.date_fin,
                         e.lieu, e.image_url, e.type_event
                  FROM " . $this->table_name . " et
                  JOIN events e ON et.event_id = e.id
                  WHERE et.user_id = ?
                  ORDER BY et.date_achat DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Tickets pour un événement donné (vue marketeur)
    public function getByEvent(int $event_id): array {
        $query = "SELECT et.*, u.nom AS client_nom, u.prenom AS client_prenom,
                         u.email AS client_email
                  FROM " . $this->table_name . " et
                  JOIN users u ON et.user_id = u.id
                  WHERE et.event_id = ?
                  ORDER BY et.date_achat DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$event_id]);
        return $stmt->fetchAll();
    }

    // Ticket par ID
    public function getById(int $id): array|false {
        $query = "SELECT et.*, e.titre AS event_titre, e.date_debut, e.date_fin,
                         e.lieu, e.image_url, u.nom AS client_nom, u.prenom AS client_prenom,
                         u.email AS client_email
                  FROM " . $this->table_name . " et
                  JOIN events e ON et.event_id = e.id
                  JOIN users u ON et.user_id = u.id
                  WHERE et.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Stats globales pour un marketeur
    public function getStatsByCreator(int $creator_id): array {
        $query = "SELECT
                    COUNT(et.id)                AS total_tickets,
                    COALESCE(SUM(et.montant_total), 0) AS revenu_total,
                    SUM(CASE WHEN et.statut='CONFIRME' THEN 1 ELSE 0 END) AS confirmes,
                    SUM(CASE WHEN et.statut='ANNULE'   THEN 1 ELSE 0 END) AS annules
                  FROM event_tickets et
                  JOIN events e ON et.event_id = e.id
                  WHERE e.created_by = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$creator_id]);
        return $stmt->fetch() ?: ['total_tickets'=>0,'revenu_total'=>0,'confirmes'=>0,'annules'=>0];
    }
}
?>
