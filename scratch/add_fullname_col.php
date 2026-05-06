<?php
require_once('config/db_connect.php');
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(255) AFTER username");
    echo "Database updated: full_name column added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
