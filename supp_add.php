<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $supplier_name = $data['supplier_name'];
    $contact_person = $data['contact_person'];
    $contact_number = $data['contact_number'];
    $address = $data['address'];
    $status = $data['status'];

    // Prepare the SQL statement to insert the new supplier
    $stmt = $conn->prepare("INSERT INTO Suppliers (supplier_name, contact_person, contact_number, address, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $supplier_name, $contact_person, $contact_number, $address, $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>
