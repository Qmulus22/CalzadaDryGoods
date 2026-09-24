<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $product_name = trim($_POST['product_name']);
    $product_code = trim($_POST['product_code']);
    $barcode = trim($_POST['barcode']);
    $category_id = $_POST['category_id'];
    $unit = trim($_POST['unit']);
    $base_price = $_POST['base_price'];
    $selling_price = $_POST['selling_price'];
    $stock_quantity = $_POST['stock_quantity'];
    
    // Check for existing product code
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Products WHERE product_code = ? AND status = 'active'");
    $stmt->bind_param("s", $product_code);
    $stmt->execute();
    $stmt->bind_result($productCodeExists);
    $stmt->fetch();
    $stmt->close();
    
    // Check for existing barcode
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Products WHERE barcode = ? AND status = 'active'");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $stmt->bind_result($barcodeExists);
    $stmt->fetch();
    $stmt->close();

    if ($productCodeExists > 0) {
        echo json_encode(['success' => false, 'message' => 'Product Code already exists.']);
        exit;
    }

    if ($barcodeExists > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists.']);
        exit;
    }

    // Insert new product
    $stmt = $conn->prepare("INSERT INTO Products (product_name, product_code, barcode, category_id, unit, base_price, selling_price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissdii", $product_name, $product_code, $barcode, $category_id, $unit, $base_price, $selling_price, $stock_quantity);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product.']);
    }
    $stmt->close();
}
?>
