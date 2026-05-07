<?php
// ============================================================
// COSMOS BEACH — models/User.php — v3.0 CORRIGÉ
// Corrections :
//   - emailExists() ne plante plus si prenom absent (colonne ajoutée)
//   - create() insère prenom correctement
//   - session_regenerate_id() après login géré dans AuthController
//   - getAllUsers() et méthodes admin ajoutées
// ============================================================

class User
{
    private $conn;
    private string $table_name = "users";

    public $id;
    public string $nom      = '';
    public string $prenom   = '';
    public string $email    = '';
    public string $password_hash = '';
    public string $role     = 'VISITEUR';
    public int    $is_active = 1;
    public ?string $last_login   = null;
    public ?string $created_at   = null;
    public ?string $disabled_at  = null;

    public function __construct()
    {
        $database    = new Database();
        $this->conn  = $database->getConnection();
    }

    // ----------------------------------------------------------
    // Vérifier si un email existe et charger l'utilisateur
    // ----------------------------------------------------------
    public function emailExists(): bool
    {
        $query = "SELECT id, nom, prenom, password_hash, role, is_active, last_login
                  FROM {$this->table_name}
                  WHERE email = ?
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->email]);
        $row  = $stmt->fetch();

        if ($row) {
            $this->id            = (int)$row['id'];
            $this->nom           = $row['nom'];
            $this->prenom        = $row['prenom']    ?? '';
            $this->password_hash = $row['password_hash'];
            $this->role          = $row['role'];
            $this->is_active     = (int)($row['is_active'] ?? 1);
            $this->last_login    = $row['last_login'] ?? null;
            return true;
        }

        return false;
    }

    // ----------------------------------------------------------
    // Trouver un utilisateur par son ID
    // ----------------------------------------------------------
    public function findById(int $id): ?array
    {
        $query = "SELECT id, nom, prenom, email, role, is_active, last_login, created_at
                  FROM {$this->table_name}
                  WHERE id = ?
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row  = $stmt->fetch();

        return $row ?: null;
    }

    // ----------------------------------------------------------
    // Créer un nouvel utilisateur (inscription)
    // ----------------------------------------------------------
    public function create(): bool
    {
        $query = "INSERT INTO {$this->table_name}
                    (nom, prenom, email, password_hash, role)
                  VALUES (?, ?, ?, ?, 'VISITEUR')";

        $stmt = $this->conn->prepare($query);

        $this->nom    = htmlspecialchars(strip_tags(trim($this->nom)));
        $this->prenom = htmlspecialchars(strip_tags(trim($this->prenom)));
        $this->email  = filter_var(trim($this->email), FILTER_SANITIZE_EMAIL);

        return $stmt->execute([
            $this->nom,
            $this->prenom,
            $this->email,
            $this->password_hash,
        ]);
    }

    // ----------------------------------------------------------
    // Mettre à jour le mot de passe
    // ----------------------------------------------------------
    public function updatePassword(string $new_password_hash): bool
    {
        $query = "UPDATE {$this->table_name}
                  SET password_hash = ?
                  WHERE email = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$new_password_hash, $this->email]);
    }

    // ----------------------------------------------------------
    // Mettre à jour la dernière connexion
    // ----------------------------------------------------------
    public function updateLastLogin(int $user_id): bool
    {
        $query = "UPDATE {$this->table_name}
                  SET last_login = NOW()
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id]);
    }

    // ----------------------------------------------------------
    // Récupérer tous les utilisateurs (admin)
    // ----------------------------------------------------------
    public function getAllUsers(): array
    {
        $query = "SELECT id, nom, prenom, email, role, is_active, last_login, created_at
                  FROM {$this->table_name}
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ----------------------------------------------------------
    // Activer / Désactiver un compte
    // ----------------------------------------------------------
    public function toggleStatus(int $user_id, int $new_status, string $reason = ''): bool
    {
        if ($new_status === 0) {
            // Désactivation
            $query = "UPDATE {$this->table_name}
                      SET is_active = 0,
                          disabled_at = NOW(),
                          disabled_reason = ?
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$reason, $user_id]);
        } else {
            // Réactivation
            $query = "UPDATE {$this->table_name}
                      SET is_active = 1,
                          disabled_at = NULL,
                          disabled_reason = NULL
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id]);
        }
    }

    // ----------------------------------------------------------
    // Changer le rôle d'un utilisateur
    // ----------------------------------------------------------
    public function updateRole(int $user_id, string $role): bool
    {
        $allowed = ['VISITEUR', 'AGENT', 'CAISSIER', 'DIRECTEUR', 'SUPER_ADMIN'];
        if (!in_array($role, $allowed, true)) {
            return false;
        }

        $query = "UPDATE {$this->table_name}
                  SET role = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$role, $user_id]);
    }

    // ----------------------------------------------------------
    // Nombre total d'utilisateurs actifs
    // ----------------------------------------------------------
    public function countActive(): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE is_active = 1"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // ----------------------------------------------------------
    // Nombre total d'utilisateurs
    // ----------------------------------------------------------
    public function countAll(): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
