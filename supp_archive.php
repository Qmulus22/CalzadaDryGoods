<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];

    // Prepare the SQL statement to update the supplier's status
    $stmt = $conn->prepare("UPDATE Suppliers SET status = 'inactive' WHERE supplier_id = ? AND status = 'active'");
    $stmt->bind_param('i', $supplier_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>