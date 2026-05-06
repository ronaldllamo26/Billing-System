<?php
require_once('config/db_connect.php');
$stmt = $pdo->query("SHOW CREATE TABLE users");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
