<?php
class Security {
    /**
     * Génère un jeton CSRF et le stocke en session.
     * @return string Le jeton généré.
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie si le jeton CSRF soumis est valide.
     * @param string $token Le jeton à vérifier.
     * @return bool True si valide, False sinon.
     */
    public static function verifyCSRFToken($token) {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    /**
     * Valide la sécurité d'un mot de passe.
     * @param string $password Le mot de passe à valider.
     * @return array Tableau d'erreurs (vide si valide).
     */
    public static function validatePassword($password) {
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
}
?>
