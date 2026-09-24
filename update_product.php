<?php
require 'config.php';

session_start();
$user_id = $_SESSION['user_id']; // Retrieve the user ID from the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productCode = $_POST['product_code'];
    $barcode = $_POST['barcode'];
    $categoryId = $_POST['category_id'];
    $unit = $_POST['unit'];
    $basePrice = $_POST['base_price'];
    $sellingPrice = $_POST['selling_price'];
    $stockQuantity = $_POST['stock_quantity'];

    // Fetch the original product details
    $stmt = $conn->prepare("SELECT stock_quantity FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $originalResult = $stmt->get_result();
    $originalProduct = $originalResult->fetch_assoc();

    // Check if stock quantity is greater than original
    if ($stockQuantity <= $originalProduct['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Stock quantity must be greater than the original quantity.']);
        exit;
    }

    // Update the product
    $updateStmt = $conn->prepare("
        UPDATE Products 
        SET product_name = ?, product_code = ?, barcode = ?, category_id = ?, unit = ?, 
            base_price = ?, selling_price = ?, stock_quantity = ? 
        WHERE product_id = ?");
    $updateStmt->bind_param("ssssiidii", $productName, $productCode, $barcode, $categoryId, $unit, $basePrice, $sellingPrice, $stockQuantity, $productId);

    if ($updateStmt->execute()) {
        // Insert into inventorytrackrecords
        $changeQuantity = $stockQuantity - $originalProduct['stock_quantity'];
        $afterTransactionQuantity = $stockQuantity;

        $inventoryStmt = $conn->prepare("
            INSERT INTO inventorytrackrecords (product_id, transaction_type, previous_quantity, change_quantity, after_transaction_quantity, user_id)
            VALUES (?, 'resupply', ?, ?, ?, ?)");
        $inventoryStmt->bind_param("iiiii", $productId, $originalProduct['stock_quantity'], $changeQuantity, $afterTransactionQuantity, $user_id);
        
        if ($inventoryStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error logging inventory transaction.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating product.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
