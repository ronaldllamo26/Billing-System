<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $amount = (float)$_POST['amount'];

    try {
        $pdo->beginTransaction();

        // 1. Update Balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        // 2. Log Top-up Transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, transaction_type) 
                               VALUES (?, ?, 'Top-up')");
        $stmt->execute([$user_id, $amount]);

        $pdo->commit();
        
        header("Location: ../admin/users.php?topped_up=1");
    } catch (PDOException $e) {
        if($pdo->inTransaction()) $pdo->rollBack();
        die("Error processing top-up: " . $e->getMessage());
    }
}
?>
