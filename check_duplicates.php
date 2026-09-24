<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$productCode = $data['product_code'];
$barcode = $data['barcode'];
$productId = $data['product_id'];

// Check for duplicate product code
$stmt = $conn->prepare("SELECT COUNT(*) FROM Products WHERE product_code = ? AND product_id != ?");
$stmt->bind_param("si", $productCode, $productId);
$stmt->execute();
$stmt->bind_result($countCode);
$stmt->fetch();
$stmt->close();

// Check for duplicate barcode
$stmt = $conn->prepare("SELECT COUNT(*) FROM Products WHERE barcode = ? AND product_id != ?");
$stmt->bind_param("si", $barcode, $productId);
$stmt->execute();
$stmt->bind_result($countBarcode);
$stmt->fetch();
$stmt->close();

$isUnique = ($countCode == 0 && $countBarcode == 0);
$message = $isUnique ? "The product code and barcode are unique." : "The product code or barcode already exists.";

echo json_encode(['isUnique' => $isUnique, 'message' => $message]);
?>
