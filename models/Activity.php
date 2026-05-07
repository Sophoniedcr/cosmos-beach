<?php
class Activity {
    private $conn;
    private $table_name = "activities";

    // Propriétés
    public $id;
    public $nom;
    public $description;
    public $prix;
    public $duree;
    public $capacite_max;
    public $type;
    public $image_url;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer toutes les activités
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer une seule activité
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nom=?, description=?, prix=?, duree=?, capacite_max=?, type=?, image_url=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->nom, $this->description, $this->prix, $this->duree, $this->capacite_max, $this->type, $this->image_url]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    public function update($id) {
        $query = "UPDATE " . $this->table_name . " SET nom=?, description=?, prix=?, duree=?, capacite_max=?, type=?, image_url=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->nom, $this->description, $this->prix, $this->duree, $this->capacite_max, $this->type, $this->image_url, $id]);
    }
}
?>
