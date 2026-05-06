<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // In production, use password_hash
    $balance = (float)$_POST['balance'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, balance, role) VALUES (?, ?, ?, 'User')");
        $stmt->execute([$username, $password, $balance]);
        
        header("Location: ../admin/users.php?registered=1");
    } catch (PDOException $e) {
        die("Error registering member: " . $e->getMessage());
    }
}
?>
