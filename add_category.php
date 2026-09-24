<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require 'config.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $category_name = $_POST['category_name'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate input
    if (empty($category_name) || empty($description)) {
        $response['message'] = 'All fields are required.';
    } else {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO Categories (category_name, description) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $category_name, $description);
            $success = $stmt->execute();
            if ($success) {
                $response['success'] = true;
                $response['message'] = 'Category added successfully.';
            } else {
                $response['message'] = 'Failed to add category. Please try again.';
            }
        } else {
            $response['message'] = 'Failed to prepare SQL statement.';
        }
    }
    
    // Output the response as JSON
    echo json_encode($response);
}
?>
