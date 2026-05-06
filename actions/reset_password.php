<?php
require_once('../config/db_connect.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $default_pw = '123456';

    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$default_pw, $user_id]);
        
        header("Location: ../admin/users.php?reset=1");
    } catch (PDOException $e) {
        die("Error resetting password: " . $e->getMessage());
    }
}
?>
