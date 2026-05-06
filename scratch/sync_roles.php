<?php
require_once('config/db_connect.php');
$pdo->exec("UPDATE users SET role = 'member' WHERE role = '' OR role IS NULL OR role = 'User'");
echo "All member roles synchronized to 'member'.";
?>
