<?php
require 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['full_name'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate contact number (must be digits only)
    if (!preg_match('/^\d+$/', $contact)) {
        $response['message'] = 'Contact number must contain only numbers.';
        echo json_encode($response);
        exit();
    }

    // Check if the username already exists for another user (excluding the current user being updated)
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? AND user_id != ?");
    $stmt->bind_param('si', $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['message'] = 'Username already taken. Please choose a different username.';
        echo json_encode($response);
        exit();
    }

    // Update employee details
    $stmt = $conn->prepare("UPDATE Users SET full_name = ?, contact = ?, username = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param('ssssi', $name, $contact, $username, $password, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Employee updated successfully.';
    } else {
        $response['message'] = 'Failed to update employee. Please try again.';
    }

    echo json_encode($response);
}
?>
