-- Migration: Ajouter système de gestion des permissions par utilisateur et historique détaillé

-- 1. Table pour les permissions des utilisateurs
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_permission (user_id, permission_id),
    INDEX idx_user_id (user_id),
    INDEX idx_permission_id (permission_id)
);

-- 2. Table pour l'historique des activités (qui a créé, modifié, supprimé quoi et quand)
CREATE TABLE IF NOT EXISTS activity_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    action ENUM('CREATE', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    changed_fields JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity_id (activity_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
);

-- 3. Table pour l'historique des réservations (qui a créé, modifié l'état)
CREATE TABLE IF NOT EXISTS reservation_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT NOT NULL,
    user_id INT NOT NULL,
    action ENUM('CREATE', 'UPDATE_STATUS', 'CANCEL', 'MODIFY') NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reservation_id (reservation_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- 4. Améliorations table paiements (ajouter date_paiement si elle n'existe pas)
ALTER TABLE payments ADD COLUMN IF NOT EXISTS date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_date_paiement (date_paiement);
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_reservation_id (reservation_id);
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_caissier_id (caissier_id);

-- 5. Modification de la table activities pour ajouter informations de création
ALTER TABLE activities ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE activities ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE activities ADD COLUMN IF NOT EXISTS updated_by INT;
ALTER TABLE activities ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE activities ADD FOREIGN KEY IF NOT EXISTS fk_created_by REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE activities ADD FOREIGN KEY IF NOT EXISTS fk_updated_by REFERENCES users(id) ON DELETE SET NULL;

-- 6. Modification de la table reservations
ALTER TABLE reservations ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE reservations ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE reservations ADD COLUMN IF NOT EXISTS updated_by INT;
ALTER TABLE reservations ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE reservations ADD FOREIGN KEY IF NOT EXISTS fk_res_created_by REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE reservations ADD FOREIGN KEY IF NOT EXISTS fk_res_updated_by REFERENCES users(id) ON DELETE SET NULL;

-- 7. Insérer des permissions par défaut pour user_permissions (optionnel - pour les utilisateurs existants)
-- Cette requête doit être exécutée une seule fois
INSERT IGNORE INTO user_permissions (user_id, permission_id)
SELECT u.id, rp.permission_id 
FROM users u
JOIN role_permissions rp ON u.role = rp.role
WHERE NOT EXISTS (
    SELECT 1 FROM user_permissions WHERE user_permissions.user_id = u.id
);
