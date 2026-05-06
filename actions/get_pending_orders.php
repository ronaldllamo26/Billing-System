<?php
require_once('../config/db_connect.php');

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT t.*, p.pc_number 
                         FROM transactions t 
                         JOIN pcs p ON t.pc_id = p.id 
                         WHERE t.transaction_type = 'Order' AND t.status = 'Pending'
                         ORDER BY t.created_at DESC");
    
    $orders = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'orders' => $orders]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
