-- Migration: Create audit logs table for tracking user activities
-- Created: 2026-05-05

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `user_name` VARCHAR(255),
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50),
    `entity_id` INT,
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `status` ENUM('success', 'failed') DEFAULT 'success',
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_timestamp` (`timestamp`),
    KEY `idx_entity` (`entity_type`, `entity_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Create login history table
-- Tracks all login attempts with IP, device, browser info

CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `email` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255),
    `last_name` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `logout_time` TIMESTAMP NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(500),
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `status` ENUM('success', 'failed') DEFAULT 'success',
    `failure_reason` VARCHAR(255),
    `is_suspicious` BOOLEAN DEFAULT FALSE,
    KEY `idx_user_id` (`user_id`),
    KEY `idx_email` (`email`),
    KEY `idx_login_time` (`login_time`),
    KEY `idx_ip_address` (`ip_address`),
    KEY `idx_suspicious` (`is_suspicious`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Create permissions table
-- Define what each role can do

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `description` TEXT,
    `category` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_name` (`name`),
    KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Create role_permissions junction table
-- Link roles to their permissions

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role` VARCHAR(50) NOT NULL,
    `permission_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_role_permission` (`role`, `permission_id`),
    KEY `idx_role` (`role`),
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Update users table with new fields
-- Add is_active field if not exists

ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `is_active` BOOLEAN DEFAULT TRUE;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_login` TIMESTAMP NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `disabled_at` TIMESTAMP NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `disabled_reason` TEXT;

-- Insert default permissions
INSERT IGNORE INTO `permissions` (`name`, `description`, `category`) VALUES
('view_admin_dashboard', 'Voir le tableau de bord admin', 'admin'),
('manage_users', 'Gérer les utilisateurs', 'users'),
('view_users', 'Voir la liste des utilisateurs', 'users'),
('activate_deactivate_users', 'Activer/Désactiver les utilisateurs', 'users'),
('view_audit_logs', 'Voir les journaux d\'audit', 'audit'),
('view_login_history', 'Voir l\'historique des connexions', 'audit'),
('manage_permissions', 'Gérer les permissions et rôles', 'permissions'),
('manage_roles', 'Gérer les rôles', 'permissions'),
('manage_activities', 'Gérer les activités', 'activities'),
('export_data', 'Exporter les données', 'export'),
('view_reports', 'Voir les rapports', 'reports'),
('manage_system_settings', 'Gérer les paramètres du système', 'system');

-- Insert default role permissions for SUPER_ADMIN
INSERT IGNORE INTO `role_permissions` (`role`, `permission_id`)
SELECT 'SUPER_ADMIN', `id` FROM `permissions`;

-- Insert default role permissions for DIRECTEUR
INSERT IGNORE INTO `role_permissions` (`role`, `permission_id`)
SELECT 'DIRECTEUR', `id` FROM `permissions` 
WHERE `name` IN ('view_admin_dashboard', 'manage_users', 'view_users', 'activate_deactivate_users', 'view_audit_logs', 'view_login_history', 'manage_activities', 'export_data', 'view_reports');

-- Insert default role permissions for AGENT
INSERT IGNORE INTO `role_permissions` (`role`, `permission_id`)
SELECT 'AGENT', `id` FROM `permissions` 
WHERE `name` IN ('view_users');
