<?php
require 'config.php';

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$supplier_id = isset($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 0;

if ($product_id > 0 && $supplier_id > 0) {
    // Insert into productsuppliers
    $stmt = $conn->prepare("INSERT INTO productsuppliers (product_id, supplier_id, status) VALUES (?, ?, 'active')");
    $stmt->bind_param('ii', $product_id, $supplier_id);
    if ($stmt->execute()) {
        echo "Product related successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid input.";
}
?>
