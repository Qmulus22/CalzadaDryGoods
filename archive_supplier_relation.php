<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_supplier_id = isset($_POST['product_supplier_id']) ? intval($_POST['product_supplier_id']) : 0;

    if ($product_supplier_id > 0) {
        // Update the status to archived
        $stmt = $conn->prepare("UPDATE productsuppliers SET status = 'archived' WHERE product_supplier_id = ?");
        $stmt->bind_param('i', $product_supplier_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update the supplier relationship."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid product_supplier_id."]);
    }
}
?>
