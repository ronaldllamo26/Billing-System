<?php
require_once('../config/db_connect.php');

header('Content-Type: application/json');

// Kunwari Station 04 (Sa production galing ito sa config file ng PC)
$pc_number = 4;

try {
    // 1. Get Real PC Status and Time Remaining
    $stmt = $pdo->prepare("SELECT p.status as pc_status, p.time_remaining, u.balance, u.username 
                           FROM pcs p 
                           LEFT JOIN users u ON p.current_user_id = u.id 
                           WHERE p.pc_number = ?");
    $stmt->execute([$pc_number]);
    $session = $stmt->fetch();

    if (!$session) {
        echo json_encode(['status' => 'error', 'message' => 'Station not found']);
        exit;
    }

    // 2. Get LATEST Completed Order (Alerts)
    $stmt = $pdo->prepare("SELECT id, order_summary FROM transactions 
                           WHERE pc_id = (SELECT id FROM pcs WHERE pc_number = ?) 
                           AND transaction_type = 'Order' 
                           AND status = 'Completed' 
                           ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$pc_number]);
    $last_serve = $stmt->fetch();

    echo json_encode([
        'status' => 'success',
        'pc_status' => $session['pc_status'],
        'time_left' => $session['time_remaining'],
        'balance' => $session['balance'] ?? 0,
        'username' => $session['username'] ?? 'Guest',
        'last_order_id' => $last_serve ? $last_serve['id'] : null,
        'order_served' => $last_serve ? $last_serve['order_summary'] : null
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
