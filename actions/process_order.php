<?php
require_once('../config/db_connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // I-assume natin Station 04 para sa testing
    $pc_number = 4; 
    
    if (!isset($_POST['cart_data'])) {
        echo json_encode(['status' => 'error', 'message' => 'No cart data received']);
        exit;
    }

    $cart_data = json_decode($_POST['cart_data'], true);

    if (!$cart_data || count($cart_data) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Your cart is empty!']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Fetch PC and User Details
        $stmt = $pdo->prepare("SELECT p.id as pc_id, u.id as user_id, u.balance 
                               FROM pcs p 
                               JOIN users u ON p.current_user_id = u.id 
                               WHERE p.pc_number = ?");
        $stmt->execute([$pc_number]);
        $session = $stmt->fetch();

        if (!$session) {
            throw new Exception("No active session found for Station $pc_number. Please login first.");
        }

        $total_price = 0;
        $items_list = [];

        foreach ($cart_data as $item) {
            // Check stock and price again (Security)
            $stmt = $pdo->prepare("SELECT price, stock_quantity, item_name FROM inventory WHERE id = ? FOR UPDATE");
            $stmt->execute([$item['id']]);
            $db_item = $stmt->fetch();

            if (!$db_item) {
                throw new Exception("Item '" . $item['name'] . "' no longer exists.");
            }

            if ($db_item['stock_quantity'] <= 0) {
                throw new Exception("Item '" . $db_item['item_name'] . "' is out of stock.");
            }

            $total_price += $db_item['price'];
            $items_list[] = $db_item['item_name'] . " (₱" . number_format($db_item['price'], 0) . ")";
            
            // Deduct Stock
            $stmt = $pdo->prepare("UPDATE inventory SET stock_quantity = stock_quantity - 1 WHERE id = ?");
            $stmt->execute([$item['id']]);
        }

        // 2. Check Balance
        if ($session['balance'] < $total_price) {
            throw new Exception("Insufficient Balance! You need ₱" . number_format($total_price, 2));
        }

        // 3. Deduct Balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$total_price, $session['user_id']]);

        // 4. Record Transaction
        $summary = implode(", ", $items_list);
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, pc_id, amount, transaction_type, status, order_summary) 
                               VALUES (?, ?, ?, 'Order', 'Pending', ?)");
        $stmt->execute([$session['user_id'], $session['pc_id'], $total_price, $summary]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => 'Order placed successfully! Admin is preparing your food.',
            'new_balance' => $session['balance'] - $total_price
        ]);

    } catch (Exception $e) {
        if($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
