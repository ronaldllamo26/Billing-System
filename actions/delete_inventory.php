<?php
require_once('../config/db_connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../admin/inventory.php?deleted=1");
    } catch (PDOException $e) {
        die("Error deleting item: " . $e->getMessage());
    }
}
?>
