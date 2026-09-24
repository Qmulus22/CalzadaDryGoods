<?php
require 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Validate contact number (must be digits only)
    if (!preg_match('/^\d+$/', $contact)) {
        $response['message'] = 'Contact number must contain only numbers.';
        echo json_encode($response);
        exit();
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['message'] = 'Username already taken. Please choose a different username.';
        echo json_encode($response);
        exit();
    }

    // Insert new employee
    $stmt = $conn->prepare("INSERT INTO Users (full_name, contact, username, password, user_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $name, $contact, $username, $password, $user_type);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Employee added successfully.';
    } else {
        $response['message'] = 'Failed to add employee. Please try again.';
    }

    echo json_encode($response);
}
?>
