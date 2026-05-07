-- ============================================================
-- COSMOS BEACH — SCHÉMA SQL CANONIQUE v3.0
-- Fichier unique à exécuter pour une installation propre
-- Remplace : database.sql, migration_v2.sql, cosmos_beach_full.sql,
--            001_create_password_resets_table.sql,
--            002_create_admin_audit_tables.sql, alter_db.php
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Base de données
CREATE DATABASE IF NOT EXISTS cosmos_beach
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cosmos_beach;

-- ============================================================
-- TABLE: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`              INT          AUTO_INCREMENT PRIMARY KEY,
  `nom`             VARCHAR(100) NOT NULL,
  `prenom`          VARCHAR(100) NOT NULL DEFAULT '',
  `email`           VARCHAR(150) NOT NULL,
  `password_hash`   VARCHAR(255) NOT NULL,
  -- Rôles canoniques : VISITEUR, AGENT, CAISSIER, DIRECTEUR, SUPER_ADMIN, MARKETEUR
  `role`            ENUM('VISITEUR','AGENT','CAISSIER','DIRECTEUR','SUPER_ADMIN','MARKETEUR')
                    NOT NULL DEFAULT 'VISITEUR',
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `last_login`      TIMESTAMP    NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `disabled_at`     TIMESTAMP    NULL,
  `disabled_reason` TEXT         NULL,
  UNIQUE KEY `uk_email`       (`email`),
  INDEX  `idx_role`           (`role`),
  INDEX  `idx_is_active`      (`is_active`),
  INDEX  `idx_last_login`     (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activities
-- ============================================================
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id`           INT            AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(100)   NOT NULL,
  `description`  TEXT,
  `prix`         DECIMAL(10,2)  NOT NULL,
  `duree`        VARCHAR(50),
  `capacite_max` INT,
  `type`         ENUM('piscine_vip','piscine_ordinaire','restaurant','chambre','zoo','jeux','autre')
                 NOT NULL DEFAULT 'autre',
  `image_url`    VARCHAR(500),
  `is_active`    TINYINT(1)     NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_type`      (`type`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: reservations
-- ============================================================
DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id`                INT           AUTO_INCREMENT PRIMARY KEY,
  `user_id`           INT           NOT NULL,
  `activite_id`       INT           NOT NULL,
  `date_reservation`  DATETIME      NOT NULL,
  `statut`            ENUM('ATTENTE','CONFIRMEE','ANNULEE','PAYEE')
                      NOT NULL DEFAULT 'ATTENTE',
  `montant_total`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `nombre_personnes`  INT           NOT NULL DEFAULT 1,
  `nombre_chambres`   INT           NULL,
  `mode_reservation`  VARCHAR(20)   NULL COMMENT 'partage|separe pour chambres',
  `nombre_tables`     INT           NULL,
  `nombre_adultes`    INT           NULL,
  `nombre_enfants`    INT           NULL,
  `date_creation`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id`          (`user_id`),
  INDEX `idx_activite_id`      (`activite_id`),
  INDEX `idx_statut`           (`statut`),
  INDEX `idx_date_reservation` (`date_reservation`),
  CONSTRAINT `fk_res_user`     FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)       ON DELETE CASCADE,
  CONSTRAINT `fk_res_activite` FOREIGN KEY (`activite_id`) REFERENCES `activities`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: payments
-- ============================================================
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id`             INT           AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` INT           NOT NULL,
  `caissier_id`    INT           NULL COMMENT 'NULL si paiement en ligne',
  `montant`        DECIMAL(10,2) NOT NULL,
  `methode`        ENUM('ESPECES','CARTE','MOBILE_MONEY') NOT NULL,
  `reference`      VARCHAR(100)  NULL COMMENT 'Référence transaction externe',
  `date_paiement`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_reservation_id` (`reservation_id`),
  INDEX `idx_caissier_id`    (`caissier_id`),
  INDEX `idx_date_paiement`  (`date_paiement`),
  CONSTRAINT `fk_pay_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pay_caissier`    FOREIGN KEY (`caissier_id`)    REFERENCES `users`(`id`)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: events
-- ============================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id`           INT          AUTO_INCREMENT PRIMARY KEY,
  `titre`        VARCHAR(150) NOT NULL,
  `description`  TEXT,
  `date_debut`   DATETIME     NOT NULL,
  `date_fin`     DATETIME     NOT NULL,
  `image_url`    VARCHAR(500),
  `likes_count`  INT          NOT NULL DEFAULT 0,
  `prix_ticket`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `capacite_max` INT           NOT NULL DEFAULT 100,
  `lieu`         VARCHAR(255)  NULL,
  `type_event`   ENUM('concert','soiree','sport','promotion','autre') NOT NULL DEFAULT 'autre',
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_by`   INT          NULL,
  `date_creation` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_date_debut` (`date_debut`),
  INDEX `idx_is_active`  (`is_active`),
  CONSTRAINT `fk_event_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: event_tickets
-- ============================================================
DROP TABLE IF EXISTS `event_tickets`;
CREATE TABLE `event_tickets` (
  `id`              INT           AUTO_INCREMENT PRIMARY KEY,
  `event_id`        INT           NOT NULL,
  `user_id`         INT           NOT NULL,
  `numero_ticket`   VARCHAR(20)   NOT NULL,
  `nombre_places`   INT           NOT NULL DEFAULT 1,
  `montant_total`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `statut`          ENUM('EN_ATTENTE','CONFIRME','ANNULE') NOT NULL DEFAULT 'EN_ATTENTE',
  `email_envoye`    TINYINT(1)    NOT NULL DEFAULT 0,
  `date_achat`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_numero_ticket` (`numero_ticket`),
  INDEX `idx_event_id` (`event_id`),
  INDEX `idx_user_id`  (`user_id`),
  INDEX `idx_statut`   (`statut`),
  CONSTRAINT `fk_et_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_et_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
DROP TABLE IF EXISTS `reclamations`;
CREATE TABLE `reclamations` (
  `id`             INT          AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT          NOT NULL,
  `sujet`          VARCHAR(255) NOT NULL,
  `message`        TEXT         NOT NULL,
  `statut`         ENUM('NOUVELLE','EN_COURS','RESOLUE') NOT NULL DEFAULT 'NOUVELLE',
  `traite_par`     INT          NULL COMMENT 'ID agent qui traite',
  `date_creation`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_traitement` TIMESTAMP   NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_statut`  (`statut`),
  CONSTRAINT `fk_rec_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_traite`  FOREIGN KEY (`traite_par`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_resets (OTP)
-- ============================================================
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id`           INT          AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT          NOT NULL,
  `email`        VARCHAR(150) NOT NULL,
  -- otp_code NE PLUS stocké en clair — uniquement le hash
  `otp_hash`     VARCHAR(255) NOT NULL,
  `attempts`     INT          NOT NULL DEFAULT 0,
  `max_attempts` INT          NOT NULL DEFAULT 5,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at`   TIMESTAMP    NOT NULL,
  `verified_at`  TIMESTAMP    NULL,
  `is_used`      TINYINT(1)   NOT NULL DEFAULT 0,
  INDEX `idx_email`        (`email`),
  INDEX `idx_user_id`      (`user_id`),
  INDEX `idx_expires_at`   (`expires_at`),
  INDEX `idx_email_active` (`email`, `is_used`, `expires_at`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: audit_logs
-- ============================================================
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id`          INT          AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT          NULL,
  `user_name`   VARCHAR(255) NULL,
  `action`      VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50)  NULL,
  `entity_id`   INT          NULL,
  `description` TEXT         NULL,
  `ip_address`  VARCHAR(45)  NULL,
  `user_agent`  VARCHAR(500) NULL,
  `status`      ENUM('success','failed') NOT NULL DEFAULT 'success',
  `timestamp`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id`   (`user_id`),
  INDEX `idx_action`    (`action`),
  INDEX `idx_timestamp` (`timestamp`),
  INDEX `idx_entity`    (`entity_type`, `entity_id`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: login_history
-- ============================================================
DROP TABLE IF EXISTS `login_history`;
CREATE TABLE `login_history` (
  `id`             INT          AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT          NULL,
  `email`          VARCHAR(150) NOT NULL,
  `first_name`     VARCHAR(100) NULL,
  `last_name`      VARCHAR(100) NULL,
  `login_time`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_time`    TIMESTAMP    NULL,
  `ip_address`     VARCHAR(45)  NOT NULL,
  `user_agent`     VARCHAR(500) NULL,
  `browser`        VARCHAR(100) NULL,
  `os`             VARCHAR(100) NULL,
  `device_type`    VARCHAR(50)  NULL,
  `status`         ENUM('success','failed') NOT NULL DEFAULT 'success',
  `failure_reason` VARCHAR(255) NULL,
  `is_suspicious`  TINYINT(1)   NOT NULL DEFAULT 0,
  INDEX `idx_user_id`     (`user_id`),
  INDEX `idx_email`       (`email`),
  INDEX `idx_login_time`  (`login_time`),
  INDEX `idx_ip`          (`ip_address`),
  INDEX `idx_suspicious`  (`is_suspicious`),
  CONSTRAINT `fk_lh_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: permissions
-- ============================================================
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id`          INT          AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `description` TEXT,
  `category`    VARCHAR(50)  NOT NULL DEFAULT 'general',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_name`    (`name`),
  INDEX  `idx_category`  (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: role_permissions
-- ============================================================
CREATE TABLE `role_permissions` (
  `id`            INT         AUTO_INCREMENT PRIMARY KEY,
  `role`          VARCHAR(50) NOT NULL,
  `permission_id` INT         NOT NULL,
  `created_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_role_perm`   (`role`, `permission_id`),
  INDEX  `idx_role`            (`role`),
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DONNÉES PAR DÉFAUT
-- ============================================================

-- Admin par défaut (mot de passe : Admin@2024!)
-- Hash bcrypt généré avec password_hash('Admin@2024!', PASSWORD_BCRYPT)
INSERT INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`) VALUES
('Administrateur', 'Super', 'admin@cosmosbeach.com',
 '$2y$12$eImiTXuWVxfM37uY4JANjQ==',  -- REMPLACER par un vrai hash PHP
 'SUPER_ADMIN');
-- IMPORTANT : Remplacer ce hash ! Exécuter en PHP :
-- echo password_hash('Admin@2024!', PASSWORD_BCRYPT);

-- Activités de démonstration (textes propres UTF-8)
INSERT INTO `activities` (`nom`, `description`, `prix`, `duree`, `capacite_max`, `type`, `image_url`) VALUES
('Piscine VIP',
 'Accès illimité à la piscine VIP avec transat et service boisson. Idéal pour se détendre au calme.',
 15000.00, 'Journée', 50, 'piscine_vip', 'img/piscine-VIP.jpg'),

('Restaurant Gastronomique',
 'Réservation d\'une table pour deux personnes avec menu dégustation local.',
 30000.00, '3 Heures', 100, 'restaurant', 'img/Nourriture.jpg'),

('Visite du Mini Zoo',
 'Découvrez notre collection d\'animaux exotiques et locaux, parfait pour les enfants.',
 5000.00, '2 Heures', 200, 'zoo', 'img/activite-jeu.jpg'),

('Chambre Deluxe',
 'Nuitée dans notre suite Deluxe avec vue sur l\'océan et petit-déjeuner inclus.',
 80000.00, 'Nuit', 2, 'chambre', 'img/Chambre-hotel.jpg'),

('Piscine Ordinaire',
 'Accès à la grande piscine publique avec toboggans pour toute la famille.',
 5000.00, 'Journée', 300, 'piscine_ordinaire', 'img/piscine publique.jpg');

-- Permissions système
INSERT INTO `permissions` (`name`, `description`, `category`) VALUES
('view_admin_dashboard',      'Voir le tableau de bord admin',          'admin'),
('manage_users',              'Gérer les utilisateurs',                  'users'),
('view_users',                'Voir la liste des utilisateurs',          'users'),
('activate_deactivate_users', 'Activer/Désactiver les utilisateurs',     'users'),
('view_audit_logs',           'Voir les journaux d\'audit',              'audit'),
('view_login_history',        'Voir l\'historique des connexions',       'audit'),
('manage_permissions',        'Gérer les permissions et rôles',          'permissions'),
('manage_roles',              'Gérer les rôles',                         'permissions'),
('manage_activities',         'Gérer les activités',                     'activities'),
('export_data',               'Exporter les données',                    'export'),
('view_reports',              'Voir les rapports financiers',            'reports'),
('manage_events',             'Gérer les événements marketing',          'marketing'),
('manage_reclamations',       'Gérer les réclamations',                  'support'),
('process_payments',          'Encaisser les paiements',                 'caisse'),
('manage_system_settings',    'Gérer les paramètres du système',         'system');

-- Permissions SUPER_ADMIN : toutes
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'SUPER_ADMIN', `id` FROM `permissions`;

-- Permissions DIRECTEUR
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'DIRECTEUR', `id` FROM `permissions`
WHERE `name` IN (
  'view_admin_dashboard','manage_users','view_users','activate_deactivate_users',
  'view_audit_logs','view_login_history','manage_activities','export_data',
  'view_reports','manage_events','manage_reclamations'
);

-- Permissions CAISSIER
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'CAISSIER', `id` FROM `permissions`
WHERE `name` IN ('process_payments','view_reports');

-- Permissions AGENT
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'AGENT', `id` FROM `permissions`
WHERE `name` IN ('view_users','manage_reclamations');