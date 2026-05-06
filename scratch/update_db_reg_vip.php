<?php
require_once('config/db_connect.php');
try {
    // 1. Update PCs table ENUM
    $pdo->exec("ALTER TABLE pcs MODIFY COLUMN pc_type ENUM('Regular', 'VIP') DEFAULT 'Regular'");
    $pdo->exec("UPDATE pcs SET pc_type = 'Regular' WHERE pc_type NOT IN ('Regular', 'VIP')");
    
    // 2. Add membership_type to users
    $pdo->exec("ALTER TABLE users ADD COLUMN membership_type ENUM('Regular', 'VIP') DEFAULT 'Regular' AFTER role");
    
    echo "Database successfully updated for Regular/VIP focus.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
