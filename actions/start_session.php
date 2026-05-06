<?php
require_once('../config/db_connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pc_id = $_POST['pc_id'];
    $amount = (float)$_POST['amount'];
    $username = $_POST['username'];

    try {
        $pdo->beginTransaction();

        // 1. Get PC Type (Regular or VIP)
        $stmt = $pdo->prepare("SELECT id, pc_type FROM pcs WHERE id = ?");
        $stmt->execute([$pc_id]);
        $pc = $stmt->fetch();

        if (!$pc) throw new Exception("Station not found");

        // 2. Determine Rates
        $current_hour = (int)date('H'); // 0 to 23
        $seconds_to_add = 0;
        $pc_type = $pc['pc_type']; // 'Regular', 'VIP'

        if ($pc_type == 'Regular') {
            // MIDNIGHT PROMOS (Regular)
            if ($current_hour >= 0 && $current_hour < 3 && $amount == 85) {
                $seconds_to_add = 6 * 3600; // 6 Hours
            } else if ($current_hour >= 3 && $current_hour < 6 && $amount == 50) {
                $seconds_to_add = 3 * 3600; // 3 Hours
            } else {
                // STANDARD RATES (Regular)
                if ($amount >= 100) {
                    $seconds_to_add = 6 * 3600; // 6 Hours Tier
                } else if ($amount >= 60) {
                    $seconds_to_add = 3 * 3600; // 3 Hours Tier
                } else {
                    $seconds_to_add = ($amount / 25) * 3600; // Standard ₱25/hr
                }
            }
        } else if ($pc_type == 'VIP') {
            // MIDNIGHT PROMOS (VIP)
            if ($current_hour >= 0 && $current_hour < 3 && $amount == 125) {
                $seconds_to_add = 6 * 3600; // 6 Hours
            } else if ($current_hour >= 3 && $current_hour < 6 && $amount == 65) {
                $seconds_to_add = 3 * 3600; // 3 Hours
            } else {
                // STANDARD RATES (VIP)
                if ($amount >= 140) {
                    $seconds_to_add = 6 * 3600; // 6 Hours Tier
                } else if ($amount >= 90) {
                    $seconds_to_add = 3 * 3600; // 3 Hours Tier
                } else {
                    $seconds_to_add = ($amount / 35) * 3600; // Standard ₱35/hr
                }
            }
        }

        // 3. Update User/Guest
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, balance, role) VALUES (?, '123', 0, 'User')");
            $stmt->execute([$username]);
            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['id'];
        }

        // 4. Activate Session
        $stmt = $pdo->prepare("UPDATE pcs SET 
                                status = 'Occupied', 
                                current_user_id = ?, 
                                start_time = NOW(),
                                time_remaining = ?
                                WHERE id = ?");
        $stmt->execute([$user_id, (int)$seconds_to_add, $pc_id]);

        // 5. Log Transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, pc_id, amount, transaction_type) 
                               VALUES (?, ?, ?, 'Rental')");
        $stmt->execute([$user_id, $pc_id, $amount]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => 'Session Started!', 
            'time_added' => floor($seconds_to_add/3600) . " Hours"
        ]);

    } catch (Exception $e) {
        if($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
