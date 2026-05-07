<?php
require_once 'backend/config/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Fix encoding issues caused by bad dump import
    $updates = [
        "UPDATE activities SET description = REPLACE(description, '├¿', 'è')",
        "UPDATE activities SET description = REPLACE(description, '├®', 'é')",
        "UPDATE activities SET description = REPLACE(description, '├á', 'à')",
        "UPDATE activities SET description = REPLACE(description, '├ó', 'â')",
        "UPDATE activities SET description = REPLACE(description, '├¬', 'ê')"
    ];
    
    foreach ($updates as $sql) {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    
    echo "Encoding fixed successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
