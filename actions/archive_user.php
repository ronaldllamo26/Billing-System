<?php
require_once('../config/db_connect.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $status = isset($_GET['restore']) ? 0 : 1;
    $msg = $status == 1 ? 'archived' : 'restored';

    try {
        $stmt = $pdo->prepare("UPDATE users SET is_archived = ? WHERE id = ?");
        $stmt->execute([$status, $user_id]);
        
        header("Location: ../admin/users.php?$msg=1");
    } catch (PDOException $e) {
        die("Error processing archive: " . $e->getMessage());
    }
}
?>
