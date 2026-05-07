USE cosmos_beach;

ALTER TABLE `events`
  ADD COLUMN IF NOT EXISTS `lieu`     VARCHAR(255) NULL AFTER `image_url`,
  ADD COLUMN IF NOT EXISTS `capacite` INT          NULL DEFAULT 0 AFTER `lieu`;

-- Créer la table visiteurs_onsite (Enregistrement visiteur Walk-in par l'agent)
CREATE TABLE IF NOT EXISTS `visiteurs_onsite` (
  `id`            INT          AUTO_INCREMENT PRIMARY KEY,
  `nom`           VARCHAR(100) NOT NULL,
  `prenom`        VARCHAR(100) NOT NULL DEFAULT '',
  `telephone`     VARCHAR(30)  NULL,
  `nombre_personnes` INT       NOT NULL DEFAULT 1,
  `activite_id`   INT          NULL,
  `agent_id`      INT          NOT NULL COMMENT 'Agent réceptionniste qui enregistre',
  `statut`        ENUM('EN_ATTENTE','PLACE','PARTI') NOT NULL DEFAULT 'EN_ATTENTE',
  `notes`         TEXT         NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_agent_id`   (`agent_id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_statut`     (`statut`),
  CONSTRAINT `fk_vo_agent`    FOREIGN KEY (`agent_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vo_activite` FOREIGN KEY (`activite_id`) REFERENCES `activities`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rapport journalier généré par l'agent
CREATE TABLE IF NOT EXISTS `rapports_journaliers` (
  `id`            INT          AUTO_INCREMENT PRIMARY KEY,
  `agent_id`      INT          NOT NULL,
  `date_rapport`  DATE         NOT NULL,
  `nb_visiteurs`  INT          NOT NULL DEFAULT 0,
  `nb_reservations` INT        NOT NULL DEFAULT 0,
  `observations`  TEXT         NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_agent_date` (`agent_id`, `date_rapport`),
  CONSTRAINT `fk_rj_agent` FOREIGN KEY (`agent_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration events v2 appliquée avec succès !' AS resultat;