<?php
require_once('../config/db_connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    try {
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'Completed' WHERE id = ?");
        $stmt->execute([$order_id]);

        echo json_encode(['status' => 'success', 'message' => 'Order marked as completed!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
