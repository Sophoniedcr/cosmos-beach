-- ============================================================
-- COSMOS BEACH — alter_marketeur.sql
-- À exécuter UNE SEULE FOIS sur une base existante
-- Ajoute : rôle MARKETEUR, colonnes events, table event_tickets
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE cosmos_beach;

-- ──────────────────────────────────────────────────────────
-- 1. Ajouter MARKETEUR à l'ENUM role de la table users
-- ──────────────────────────────────────────────────────────
ALTER TABLE `users`
  MODIFY COLUMN `role`
    ENUM('VISITEUR','AGENT','CAISSIER','DIRECTEUR','SUPER_ADMIN','MARKETEUR')
    NOT NULL DEFAULT 'VISITEUR';

-- ──────────────────────────────────────────────────────────
-- 2. Enrichir la table events pour la gestion de tickets
-- ──────────────────────────────────────────────────────────
ALTER TABLE `events`
  ADD COLUMN IF NOT EXISTS `prix_ticket`   DECIMAL(10,2) NOT NULL DEFAULT 0.00   AFTER `image_url`,
  ADD COLUMN IF NOT EXISTS `capacite_max`  INT           NOT NULL DEFAULT 100     AFTER `prix_ticket`,
  ADD COLUMN IF NOT EXISTS `lieu`          VARCHAR(255)  NULL                     AFTER `capacite_max`,
  ADD COLUMN IF NOT EXISTS `type_event`    ENUM('concert','soiree','sport','promotion','autre') NOT NULL DEFAULT 'autre' AFTER `lieu`;

-- ──────────────────────────────────────────────────────────
-- 3. Créer la table event_tickets
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `event_tickets` (
  `id`              INT           AUTO_INCREMENT PRIMARY KEY,
  `event_id`        INT           NOT NULL,
  `user_id`         INT           NOT NULL,
  `numero_ticket`   VARCHAR(20)   NOT NULL COMMENT 'Ex: TKT-00001',
  `nombre_places`   INT           NOT NULL DEFAULT 1,
  `montant_total`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `statut`          ENUM('EN_ATTENTE','CONFIRME','ANNULE') NOT NULL DEFAULT 'EN_ATTENTE',
  `email_envoye`    TINYINT(1)    NOT NULL DEFAULT 0,
  `date_achat`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_numero_ticket` (`numero_ticket`),
  INDEX `idx_event_id`  (`event_id`),
  INDEX `idx_user_id`   (`user_id`),
  INDEX `idx_statut`    (`statut`),
  CONSTRAINT `fk_et_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_et_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- 4. Permissions pour le rôle MARKETEUR
-- ──────────────────────────────────────────────────────────
INSERT IGNORE INTO `role_permissions` (`role`, `permission_id`)
SELECT 'MARKETEUR', `id` FROM `permissions`
WHERE `name` IN ('manage_events', 'view_reports');

-- ──────────────────────────────────────────────────────────
-- 5. Compte MARKETEUR de démonstration
--    Mot de passe : Marketeur@2024!
--    Remplacer le hash par : echo password_hash('Marketeur@2024!', PASSWORD_BCRYPT);
-- ──────────────────────────────────────────────────────────
INSERT INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`, `is_active`)
VALUES (
  'Marketing', 'Service',
  'marketeur@cosmosbeach.com',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- mot de passe: password
  'MARKETEUR', 1
)
ON DUPLICATE KEY UPDATE role = 'MARKETEUR';

SET FOREIGN_KEY_CHECKS = 1;

-- ──────────────────────────────────────────────────────────
-- NOTE : Pour définir un vrai mot de passe sécurisé,
-- exécuter ce PHP et mettre à jour le hash :
--   echo password_hash('VotreMotDePasse', PASSWORD_BCRYPT);
-- ──────────────────────────────────────────────────────────
