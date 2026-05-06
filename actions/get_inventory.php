<?php
require_once('../config/db_connect.php');

try {
    $stmt = $pdo->query("SELECT * FROM inventory WHERE stock_quantity > 0");
    $items = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $items]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
