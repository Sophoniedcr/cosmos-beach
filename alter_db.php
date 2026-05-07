<?php
require_once 'backend/config/Database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "ALTER TABLE reservations ADD COLUMN IF NOT EXISTS nombre_personnes INT DEFAULT 1";
    $conn->exec($sql);
    echo "Column added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
