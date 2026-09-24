<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];

    // Update the product status to inactive
    $updateStmt = $conn->prepare("UPDATE Products SET status = 'inactive' WHERE product_id = ?");
    $updateStmt->bind_param("i", $productId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product archived successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error archiving product.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
