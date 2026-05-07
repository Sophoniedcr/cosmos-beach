<?php
// ============================================================
// COSMOS BEACH — controllers/AgentController.php — v1.0
// Implémente le diagramme Use Case Réceptionniste :
//   ✓ Enregistrer un visiteur (Walk-in)
//   ✓ Générer rapport journalier
// ============================================================

class AgentController
{
    private $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // ──────────────────────────────────────────────────────────
    // Vérification accès agent
    // ──────────────────────────────────────────────────────────
    private function checkAgentAccess(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?action=login");
            exit;
        }
        if (!in_array($_SESSION['user_role'], ['AGENT', 'DIRECTEUR', 'SUPER_ADMIN'], true)) {
            http_response_code(403);
            require 'views/errors/403.php';
            exit;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Enregistrer un visiteur Walk-in
    // ──────────────────────────────────────────────────────────
    public function registerWalkin(): void
    {
        $this->checkAgentAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide. Veuillez réessayer.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $nom              = htmlspecialchars(strip_tags(trim($_POST['nom']      ?? '')));
        $prenom           = htmlspecialchars(strip_tags(trim($_POST['prenom']   ?? '')));
        $telephone        = htmlspecialchars(strip_tags(trim($_POST['telephone'] ?? '')));
        $nombre_personnes = max(1, intval($_POST['nombre_personnes'] ?? 1));
        $activite_id      = intval($_POST['activite_id'] ?? 0) ?: null;
        $notes            = htmlspecialchars(strip_tags(trim($_POST['notes'] ?? '')));

        if (empty($nom) || empty($prenom)) {
            $_SESSION['flash_error'] = "Le nom et le prénom sont obligatoires.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        try {
            // Créer la table si elle n'existe pas encore
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS `visiteurs_onsite` (
                  `id`               INT AUTO_INCREMENT PRIMARY KEY,
                  `nom`              VARCHAR(100) NOT NULL,
                  `prenom`           VARCHAR(100) NOT NULL DEFAULT '',
                  `telephone`        VARCHAR(30)  NULL,
                  `nombre_personnes` INT NOT NULL DEFAULT 1,
                  `activite_id`      INT NULL,
                  `agent_id`         INT NOT NULL,
                  `statut`           ENUM('EN_ATTENTE','PLACE','PARTI') NOT NULL DEFAULT 'EN_ATTENTE',
                  `notes`            TEXT NULL,
                  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX idx_agent_id (agent_id),
                  CONSTRAINT fk_vo_agent2 FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            $stmt = $this->conn->prepare(
                "INSERT INTO visiteurs_onsite
                   (nom, prenom, telephone, nombre_personnes, activite_id, agent_id, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $nom, $prenom,
                $telephone ?: null,
                $nombre_personnes,
                $activite_id,
                $_SESSION['user_id'],
                $notes ?: null,
            ]);

            AuditLog::log(
                'register_walkin', 'visiteurs_onsite',
                (int)$this->conn->lastInsertId(),
                "Walk-in enregistré : $prenom $nom ($nombre_personnes pers.)"
            );

            $_SESSION['flash_success'] = "Visiteur {$prenom} {$nom} enregistré avec succès.";

        } catch (\Exception $e) {
            error_log("[COSMOS][AgentController] registerWalkin: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de l'enregistrement. Veuillez réessayer.";
        }

        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // Générer le rapport journalier
    // ──────────────────────────────────────────────────────────
    public function generateReport(): void
    {
        $this->checkAgentAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Requête invalide.";
            header("Location: " . BASE_URL . "/?action=dashboard");
            exit;
        }

        $observations = htmlspecialchars(strip_tags(trim($_POST['observations'] ?? '')));

        try {
            // Compter les réservations du jour
            $stmt_res = $this->conn->prepare(
                "SELECT COUNT(*) FROM reservations WHERE DATE(date_creation) = CURDATE()"
            );
            $stmt_res->execute();
            $nb_reservations = (int)$stmt_res->fetchColumn();

            // Compter les visiteurs walk-in du jour
            $nb_visiteurs = 0;
            try {
                $stmt_vi = $this->conn->prepare(
                    "SELECT COUNT(*) FROM visiteurs_onsite WHERE DATE(created_at) = CURDATE() AND agent_id = ?"
                );
                $stmt_vi->execute([$_SESSION['user_id']]);
                $nb_visiteurs = (int)$stmt_vi->fetchColumn();
            } catch (\Exception $e2) { /* table inexistante */ }

            // Créer la table rapports si nécessaire
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS `rapports_journaliers` (
                  `id`              INT AUTO_INCREMENT PRIMARY KEY,
                  `agent_id`        INT NOT NULL,
                  `date_rapport`    DATE NOT NULL,
                  `nb_visiteurs`    INT NOT NULL DEFAULT 0,
                  `nb_reservations` INT NOT NULL DEFAULT 0,
                  `observations`    TEXT NULL,
                  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX idx_agent_date (agent_id, date_rapport),
                  CONSTRAINT fk_rj_agent2 FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Insérer ou mettre à jour le rapport du jour
            $stmt = $this->conn->prepare(
                "INSERT INTO rapports_journaliers
                   (agent_id, date_rapport, nb_visiteurs, nb_reservations, observations)
                 VALUES (?, CURDATE(), ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                   nb_visiteurs    = VALUES(nb_visiteurs),
                   nb_reservations = VALUES(nb_reservations),
                   observations    = VALUES(observations)"
            );
            $stmt->execute([
                $_SESSION['user_id'],
                $nb_visiteurs,
                $nb_reservations,
                $observations ?: null,
            ]);

            AuditLog::log(
                'generate_daily_report', 'rapports_journaliers', null,
                "Rapport journalier généré : $nb_visiteurs visiteurs, $nb_reservations réservations"
            );

            $_SESSION['flash_success'] = "Rapport journalier généré : {$nb_visiteurs} visiteurs walk-in, {$nb_reservations} réservations.";

        } catch (\Exception $e) {
            error_log("[COSMOS][AgentController] generateReport: " . $e->getMessage());
            $_SESSION['flash_error'] = "Erreur lors de la génération du rapport.";
        }

        header("Location: " . BASE_URL . "/?action=dashboard");
        exit;
    }
}