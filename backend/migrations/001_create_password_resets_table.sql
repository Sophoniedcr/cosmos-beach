-- Migration: Create password_resets table for OTP management
-- Created: 2026-05-05

CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `otp_code` VARCHAR(10) NOT NULL,
    `otp_hash` VARCHAR(255) NOT NULL,
    `attempts` INT DEFAULT 0,
    `max_attempts` INT DEFAULT 5,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NOT NULL,
    `verified_at` TIMESTAMP NULL,
    `is_used` BOOLEAN DEFAULT FALSE,
    UNIQUE KEY `unique_otp` (`email`, `otp_code`),
    KEY `idx_email` (`email`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires_at` (`expires_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour les recherches rapides
CREATE INDEX idx_email_not_used ON password_resets(email, is_used, expires_at);
