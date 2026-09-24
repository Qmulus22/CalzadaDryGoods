<?php
require 'config.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $conn->prepare("
        SELECT p.*, c.category_name 
        FROM Products p
        LEFT JOIN Categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
}
?>
