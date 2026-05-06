<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pc_id = $_POST['pc_id'];
    $minutes = $_POST['minutes'];

    try {
        // Kunwari i-uupdate natin yung PC status at duration
        // For simulation, just return success
        $stmt = $pdo->prepare("UPDATE pcs SET status = 'Occupied' WHERE id = ?");
        $stmt->execute([$pc_id]);

        echo json_encode(['status' => 'success', 'message' => "Added $minutes minutes to PC $pc_id"]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
