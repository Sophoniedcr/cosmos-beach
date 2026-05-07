<?php
// ============================================================
// COSMOS BEACH — models/Database.php — v3.0
// Corrections : connexion singleton (évite les connexions multiples),
//               SUPPRIME backend/config/Database.php (doublon),
//               die() remplacé par exception propageable.
// ============================================================

class Database
{
    private static ?PDO $instance = null;

    // Lire depuis les variables d'environnement système ou valeurs par défaut XAMPP
    private string $host     = 'localhost';
    private string $db_name  = 'cosmos_beach';
    private string $username = 'root';
    private string $password = '';

    public function __construct()
    {
        // Permettre la surcharge via variables d'environnement (production)
        $this->host     = getenv('DB_HOST')     ?: $this->host;
        $this->db_name  = getenv('DB_NAME')     ?: $this->db_name;
        $this->username = getenv('DB_USER')     ?: $this->username;
        $this->password = getenv('DB_PASSWORD') ?: $this->password;
    }

    /**
     * Retourne la connexion PDO (singleton — une seule connexion par requête).
     *
     * @throws RuntimeException si la connexion échoue
     */
    public function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $this->host,
                $this->db_name
            );

            self::$instance = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Vraies requêtes préparées
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            ]);

        } catch (PDOException $e) {
            // Logger sans exposer les credentials
            error_log('[COSMOS][DB] Connexion échouée : ' . $e->getMessage());
            // Propager pour que index.php gère l'affichage d'erreur
            throw new RuntimeException("Impossible de se connecter à la base de données.");
        }

        return self::$instance;
    }

    /**
     * Réinitialise le singleton (utile pour les tests).
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}