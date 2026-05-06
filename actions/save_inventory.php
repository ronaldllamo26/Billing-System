<?php
require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    
    // Handle Image Upload
    $image_name = 'placeholder.png';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file_ext = pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION);
        $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $item_name) . "." . $file_ext;
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
            // Upload successful
        } else {
            $image_name = 'placeholder.png';
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO inventory (item_name, price, stock_quantity, category, item_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$item_name, $price, $stock, $category, $image_name]);
        
        // Redirect back to inventory with success
        header("Location: ../admin/inventory.php?success=1");
    } catch (PDOException $e) {
        die("Error saving item: " . $e->getMessage());
    }
}
?>
