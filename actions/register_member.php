<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password']; 
    $balance = (float)$_POST['balance'];
    $membership_type = $_POST['membership_type'] ?? 'Regular';

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, balance, role, membership_type) VALUES (?, ?, ?, ?, 'member', ?)");
        $stmt->execute([$username, $full_name, $password, $balance, $membership_type]);
        
        header("Location: ../admin/users.php?registered=1");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            header("Location: ../admin/users.php?error=duplicate");
        } else {
            header("Location: ../admin/users.php?error=1");
        }
    }
}
?>
