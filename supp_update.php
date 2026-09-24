<?php
require 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $supplier_id = $data['supplier_id'];
    $supplier_name = $data['supplier_name'];
    $contact_person = $data['contact_person'];
    $contact_number = $data['contact_number'];
    $address = $data['address'];

    // Validate contact number (must be digits only)
    if (!preg_match('/^\d+$/', $contact_number)) {
        $response['message'] = 'Contact number must contain only numbers.';
        echo json_encode($response);
        exit();
    }

    // Update supplier details
    $stmt = $conn->prepare("UPDATE Suppliers SET supplier_name = ?, contact_person = ?, contact_number = ?, address = ? WHERE supplier_id = ?");
    $stmt->bind_param('ssssi', $supplier_name, $contact_person, $contact_number, $address, $supplier_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Supplier updated successfully.';
    } else {
        $response['message'] = 'Failed to update supplier. Please try again.';
    }

    echo json_encode($response);
}
?>
