<?php
require_once('config/db_connect.php');
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN is_archived TINYINT(1) DEFAULT 0");
    echo "Database updated: is_archived column added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
