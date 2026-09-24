<?php
require 'config.php';

if (isset($_GET['supplier_id'])) {
    $supplier_id = intval($_GET['supplier_id']);
    $stmt = $conn->prepare("SELECT * FROM Suppliers WHERE supplier_id = ?");
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Supplier not found.']);
    }
}
?>
