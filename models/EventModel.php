<?php
// ============================================================
// COSMOS BEACH — models/EventModel.php — v2.0
// Ajouts : prix_ticket, capacite_max, lieu, type_event,
//          getById(), getActiveEvents(), getTicketCount(),
//          update(), toggle(), getByCreator()
// ============================================================

class EventModel {
    private $conn;
    private $table_name = "events";

    public $id;
    public $titre;
    public $description;
    public $date_debut;
    public $date_fin;
    public $image_url;
    public $prix_ticket;
    public $capacite_max;
    public $lieu;
    public $type_event;
    public $created_by;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tous les événements (admin/marketing)
    public function getAll(): array {
        $query = "SELECT e.*, u.nom AS createur_nom, u.prenom AS createur_prenom
                  FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.created_by = u.id
                  ORDER BY e.date_debut DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Événements actifs et non expirés (page publique)
    public function getActiveEvents(): array {
        $query = "SELECT e.*, u.nom AS createur_nom,
                         (SELECT COUNT(*) FROM event_tickets et WHERE et.event_id = e.id AND et.statut = 'CONFIRME') AS tickets_vendus
                  FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.created_by = u.id
                  WHERE e.is_active = 1 AND e.date_fin >= NOW()
                  ORDER BY e.date_debut ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Événements créés par un marketeur donné
    public function getByCreator(int $user_id): array {
        $query = "SELECT e.*,
                         (SELECT COUNT(*) FROM event_tickets et WHERE et.event_id = e.id AND et.statut = 'CONFIRME') AS tickets_vendus,
                         (SELECT COALESCE(SUM(et2.montant_total),0) FROM event_tickets et2 WHERE et2.event_id = e.id AND et2.statut = 'CONFIRME') AS revenu_total
                  FROM " . $this->table_name . " e
                  WHERE e.created_by = ?
                  ORDER BY e.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Récupérer un événement par son ID
    public function getById(int $id): array|false {
        $query = "SELECT e.*, u.nom AS createur_nom, u.prenom AS createur_prenom,
                         (SELECT COUNT(*) FROM event_tickets et WHERE et.event_id = e.id AND et.statut = 'CONFIRME') AS tickets_vendus
                  FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.created_by = u.id
                  WHERE e.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Créer un événement
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . "
                  SET titre=?, description=?, date_debut=?, date_fin=?,
                      image_url=?, prix_ticket=?, capacite_max=?,
                      lieu=?, type_event=?, created_by=?, is_active=1";
        $stmt = $this->conn->prepare($query);
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->lieu  = htmlspecialchars(strip_tags($this->lieu ?? ''));
        return $stmt->execute([
            $this->titre,
            $this->description,
            $this->date_debut,
            $this->date_fin,
            $this->image_url,
            $this->prix_ticket ?? 0,
            $this->capacite_max ?? 100,
            $this->lieu,
            $this->type_event ?? 'autre',
            $this->created_by,
        ]);
    }

    // Mettre à jour un événement
    public function update(int $id, array $data): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET titre=?, description=?, date_debut=?, date_fin=?,
                      image_url=?, prix_ticket=?, capacite_max=?,
                      lieu=?, type_event=?
                  WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            htmlspecialchars(strip_tags($data['titre'])),
            $data['description'],
            $data['date_debut'],
            $data['date_fin'],
            $data['image_url'] ?? null,
            $data['prix_ticket'] ?? 0,
            $data['capacite_max'] ?? 100,
            htmlspecialchars(strip_tags($data['lieu'] ?? '')),
            $data['type_event'] ?? 'autre',
            $id,
        ]);
    }

    // Activer / désactiver (soft delete)
    public function toggle(int $id): bool {
        $query = "UPDATE " . $this->table_name . " SET is_active = NOT is_active WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Supprimer définitivement
    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Nombre de tickets confirmés pour un événement
    public function getTicketCount(int $event_id): int {
        $query = "SELECT COALESCE(SUM(nombre_places), 0) FROM event_tickets WHERE event_id = ? AND statut = 'CONFIRME'";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute([$event_id]);
        return (int)$stmt->fetchColumn();
    }

    // Places restantes
    public function getAvailableSeats(int $event_id): int {
        $event = $this->getById($event_id);
        if (!$event) return 0;
        return max(0, $event['capacite_max'] - $this->getTicketCount($event_id));
    }
}
?>
