<?php
require 'config.php';

session_start();
$user_id = $_SESSION['user_id']; // Retrieve the user ID from the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;

    if ($order_id) {
        // Fetch the supplier ID and products associated with the order
        $stmt = $conn->prepare("SELECT supplier_id FROM Orders WHERE order_id = ?");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $orderResult = $stmt->get_result();
        $orderData = $orderResult->fetch_assoc();
        $supplier_id = $orderData['supplier_id'];

        // Fetch the products associated with the order
        $stmt = $conn->prepare("SELECT product_id, requested_quantity FROM orderdetails WHERE order_id = ?");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $productsResult = $stmt->get_result();

        // Loop through each product to update stock quantities
        while ($product = $productsResult->fetch_assoc()) {
            $productId = $product['product_id'];
            $requestedQuantity = $product['requested_quantity'];

            // Update the product stock quantity
            $updateStmt = $conn->prepare("UPDATE Products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
            $updateStmt->bind_param('ii', $requestedQuantity, $productId);
            $updateStmt->execute();

            // Insert into inventorytrackrecords
            $inventoryStmt = $conn->prepare("
                INSERT INTO inventorytrackrecords (product_id, transaction_type, change_quantity, after_transaction_quantity, user_id, supplier_id)
                VALUES (?, 'resupply', ?, ?, ?, ?)");
            
            // Get the new stock quantity
            $newStockQuantity = $requestedQuantity; // Assuming requested quantity is added
            $inventoryStmt->bind_param("iiisi", $productId, $requestedQuantity, $newStockQuantity, $user_id, $supplier_id);
            $inventoryStmt->execute();
        }

        // Update the order status to "Received"
        $sql = "UPDATE Orders SET status = 'Received' WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $order_id);
        if ($stmt->execute()) {
            echo 'Order marked as received and products updated.';
        } else {
            echo 'Error marking the order as received.';
        }
    } else {
        echo 'Invalid order ID.';
    }
}
?>
