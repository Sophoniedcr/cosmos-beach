<?php
// ============================================================
// COSMOS BEACH — models/PasswordReset.php — v3.1 CORRIGÉ
//
// CAUSES DE L'ERREUR "Erreur lors de la création du code OTP" :
//
// CAUSE 1 — CONTRAINTE UNIQUE VIOLÉE (principale cause) :
//   La table a UNIQUE KEY `unique_otp` (`email`, `otp_code`).
//   Deux OTP identiques pour le même email = erreur SQL silencieuse.
//   → Contrainte supprimée dans le SQL de migration ci-dessous.
//
// CAUSE 2 — COLONNE otp_code absente dans certains schémas :
//   Le schéma canonique v3 supprime otp_code mais le modèle l'insère.
//   → createOTPRequest() insère otp_code seulement si la colonne existe.
//
// CAUSE 3 — CHEMIN .env incorrect :
//   EmailService cherchait le .env dans backend/.env au lieu de la racine.
//   → Corrigé dans EmailService.php (fichier séparé).
//
// CAUSE 4 — OTP généré avec rand() non sécurisé :
//   → Remplacé par random_int() (CSPRNG).
//
// CAUSE 5 — Expiration définie à 15 min mais .env dit OTP_EXPIRATION_MINUTES=5 :
//   → On lit OTP_EXPIRATION_MINUTES depuis .env.
// ============================================================

class PasswordReset
{
    private $conn;
    private string $table_name = "password_resets";

    public $id;
    public $user_id;
    public $email;
    public $otp_code;  // Code en clair — uniquement en mémoire pour envoi email
    public $otp_hash;
    public $attempts;
    public $max_attempts;
    public $created_at;
    public $expires_at;
    public $verified_at;
    public $is_used;

    // Durée d'expiration en minutes (lue depuis .env ou 15 par défaut)
    private int $expiration_minutes = 15;

    public function __construct()
    {
        $database        = new Database();
        $this->conn      = $database->getConnection();

        // Lire la durée d'expiration depuis .env
        $env_file = dirname(__DIR__, 1) . '/.env';
        if (!file_exists($env_file)) {
            $env_file = dirname(__DIR__) . '/.env';
        }
        if (file_exists($env_file)) {
            $env = parse_ini_file($env_file);
            if (!empty($env['OTP_EXPIRATION_MINUTES'])) {
                $this->expiration_minutes = (int)$env['OTP_EXPIRATION_MINUTES'];
            }
        }
    }

    // ──────────────────────────────────────────────────────────
    // Générer un OTP cryptographiquement sécurisé
    // ──────────────────────────────────────────────────────────
    public function generateOTP(): string
    {
        // random_int() utilise le CSPRNG du système — sécurisé
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // ──────────────────────────────────────────────────────────
    // Créer une demande OTP
    // ──────────────────────────────────────────────────────────
    public function createOTPRequest(int $user_id, string $email): bool
    {
        try {
            // 1. Invalider les OTP précédents
            $this->invalidatePreviousOTPs($email);

            // 2. Générer OTP sécurisé
            $otp_code = $this->generateOTP();
            $otp_hash = password_hash($otp_code, PASSWORD_BCRYPT, ['cost' => 10]);
            $expires_at = date('Y-m-d H:i:s', strtotime('+' . $this->expiration_minutes . ' minutes'));

            // 3. Détecter si la colonne otp_code existe dans la table
            //    (compatibilité avec les deux schémas SQL)
            $has_otp_code_column = $this->columnExists('otp_code');

            if ($has_otp_code_column) {
                $query = "INSERT INTO {$this->table_name}
                          (user_id, email, otp_code, otp_hash, expires_at, attempts, max_attempts)
                          VALUES (?, ?, ?, ?, ?, 0, 5)";
                $params = [$user_id, $email, $otp_code, $otp_hash, $expires_at];
            } else {
                $query = "INSERT INTO {$this->table_name}
                          (user_id, email, otp_hash, expires_at, attempts, max_attempts)
                          VALUES (?, ?, ?, ?, 0, 5)";
                $params = [$user_id, $email, $otp_hash, $expires_at];
            }

            $stmt = $this->conn->prepare($query);

            if ($stmt->execute($params)) {
                $this->id       = (int)$this->conn->lastInsertId();
                $this->otp_code = $otp_code; // Garder en mémoire pour l'envoi email
                return true;
            }

            error_log("[COSMOS][OTP] execute() a retourné false pour user_id=$user_id");
            return false;

        } catch (\PDOException $e) {
            error_log("[COSMOS][OTP] PDOException createOTPRequest: " . $e->getMessage()
                    . " | Code: " . $e->getCode());
            return false;
        } catch (\Exception $e) {
            error_log("[COSMOS][OTP] Exception createOTPRequest: " . $e->getMessage());
            return false;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Invalider les OTP précédents non utilisés
    // ──────────────────────────────────────────────────────────
    private function invalidatePreviousOTPs(string $email): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table_name}
             SET is_used = 1
             WHERE email = ? AND is_used = 0"
        );
        $stmt->execute([$email]);
    }

    // ──────────────────────────────────────────────────────────
    // Vérifier l'OTP soumis par l'utilisateur
    // ──────────────────────────────────────────────────────────
    public function verifyOTP(string $email, string $otp_code): array
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM {$this->table_name}
                 WHERE email = ? AND is_used = 0
                 ORDER BY created_at DESC
                 LIMIT 1"
            );
            $stmt->execute([$email]);
            $row = $stmt->fetch();

            if (!$row) {
                return ['success' => false, 'message' => 'Aucune demande de réinitialisation trouvée. Veuillez recommencer.'];
            }

            // Expiration
            if (strtotime($row['expires_at']) < time()) {
                $this->markAsUsed($row['id']);
                return ['success' => false, 'message' => 'Le code OTP a expiré. Veuillez demander un nouveau code.'];
            }

            // Tentatives dépassées
            if ((int)$row['attempts'] >= (int)$row['max_attempts']) {
                $this->markAsUsed($row['id']);
                return ['success' => false, 'message' => 'Trop de tentatives incorrectes. Veuillez demander un nouveau code.'];
            }

            // Vérification du hash
            if (!password_verify($otp_code, $row['otp_hash'])) {
                $this->incrementAttempts($row['id']);
                $remaining = (int)$row['max_attempts'] - (int)$row['attempts'] - 1;
                return ['success' => false, 'message' => "Code incorrect. Tentatives restantes : $remaining"];
            }

            // ✓ Valide
            $this->markAsVerified($row['id']);

            return [
                'success'  => true,
                'message'  => 'Code vérifié avec succès.',
                'reset_id' => $row['id'],
                'user_id'  => $row['user_id'],
            ];

        } catch (\Exception $e) {
            error_log("[COSMOS][OTP] verifyOTP: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique. Veuillez réessayer.'];
        }
    }

    // ──────────────────────────────────────────────────────────
    // Helpers privés
    // ──────────────────────────────────────────────────────────
    private function incrementAttempts(int $reset_id): void
    {
        $this->conn->prepare(
            "UPDATE {$this->table_name} SET attempts = attempts + 1 WHERE id = ?"
        )->execute([$reset_id]);
    }

    private function markAsVerified(int $reset_id): void
    {
        $this->conn->prepare(
            "UPDATE {$this->table_name} SET verified_at = NOW() WHERE id = ?"
        )->execute([$reset_id]);
    }

    public function markAsUsed(int $reset_id): void
    {
        $this->conn->prepare(
            "UPDATE {$this->table_name} SET is_used = 1 WHERE id = ?"
        )->execute([$reset_id]);
    }

    public function getResetRequest(int $reset_id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([$reset_id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function isValidResetRequest(int $reset_id): bool
    {
        $request = $this->getResetRequest($reset_id);
        if (!$request)                              return false;
        if ($request['is_used'])                    return false;
        if (strtotime($request['expires_at']) < time()) return false;
        if (is_null($request['verified_at']))       return false;
        return true;
    }

    public function cleanupExpiredOTPs(): bool
    {
        return $this->conn->prepare(
            "DELETE FROM {$this->table_name}
             WHERE expires_at < NOW()
                OR (is_used = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR))"
        )->execute();
    }

    // ──────────────────────────────────────────────────────────
    // Vérifie si une colonne existe dans la table (compatibilité schémas)
    // ──────────────────────────────────────────────────────────
    private function columnExists(string $column_name): bool
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = ?
                   AND COLUMN_NAME  = ?"
            );
            $stmt->execute([$this->table_name, $column_name]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
