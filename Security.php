<?php
// ============================================================
// COSMOS BEACH — backend/config/Security.php — v3.2
// CORRECTION : validatePassword() manquait → erreur fatale dans reset_password
// ============================================================

class Security
{
    // ──────────────────────────────────────────────────────────
    // CSRF — génération
    // ──────────────────────────────────────────────────────────
    public static function generateCSRFToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // ──────────────────────────────────────────────────────────
    // CSRF — vérification (régénère après validation)
    // ──────────────────────────────────────────────────────────
    public static function verifyCSRFToken(string $token): bool
    {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        $valid = hash_equals($_SESSION['csrf_token'], $token);
        if ($valid) {
            // Régénérer pour la prochaine requête (anti-replay)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $valid;
    }

    // ──────────────────────────────────────────────────────────
    // Validation mot de passe
    // Retourne un tableau d'erreurs (vide = OK)
    // ──────────────────────────────────────────────────────────
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre minuscule.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }

        return $errors;
    }

    // ──────────────────────────────────────────────────────────
    // Échappement HTML (anti-XSS)
    // ──────────────────────────────────────────────────────────
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // ──────────────────────────────────────────────────────────
    // Email sécurisé
    // ──────────────────────────────────────────────────────────
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    // ──────────────────────────────────────────────────────────
    // IP client sécurisée (anti-spoofing)
    // ──────────────────────────────────────────────────────────
    public static function getClientIP(array $trusted_proxies = ['127.0.0.1']): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (in_array($remote, $trusted_proxies, true)) {
            $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
            if (!empty($forwarded)) {
                $ip = trim(explode(',', $forwarded)[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $remote;
    }
}
