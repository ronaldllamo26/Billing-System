<?php
require_once('config/db_connect.php');
$pdo->exec("UPDATE pcs SET time_remaining = 3600, status = 'Occupied' WHERE pc_number = 4");
echo "PC-04 updated to 1 hour.";
?>
