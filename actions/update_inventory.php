<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    try {
        // Handle Optional Image Upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
            $target_dir = "../assets/img/";
            $file_ext = pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION);
            $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $item_name) . "." . $file_ext;
            $target_file = $target_dir . $image_name;
            
            if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
                // Update with NEW image
                $stmt = $pdo->prepare("UPDATE inventory SET item_name = ?, price = ?, stock_quantity = ?, category = ?, item_image = ? WHERE id = ?");
                $stmt->execute([$item_name, $price, $stock, $category, $image_name, $id]);
            }
        } else {
            // Update WITHOUT changing image
            $stmt = $pdo->prepare("UPDATE inventory SET item_name = ?, price = ?, stock_quantity = ?, category = ? WHERE id = ?");
            $stmt->execute([$item_name, $price, $stock, $category, $id]);
        }
        
        header("Location: ../admin/inventory.php?updated=1");
    } catch (PDOException $e) {
        die("Error updating item: " . $e->getMessage());
    }
}
?>
