<?php
class Database {
    private $host = "localhost";
    private $db_name = "cosmos_beach";
    private $username = "root";
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            // Mode d'erreur PDO sur Exception et mode de récupération par défaut en tableau associatif
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            die("Erreur de connexion a la base de donnees : " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
